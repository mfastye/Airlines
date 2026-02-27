<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\Booking;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Models\Provider;
use Illuminate\Support\Facades\DB;

class FinancialTransactionService
{
    /**
     * Process financial entries when a ticket is issued
     */
    public function processTicketIssuance(Booking $booking): void
    {
        DB::transaction(function () use ($booking) {
            $provider = $booking->provider;

            // 1. Credit provider account with net price
            if ($provider) {
                $this->creditProviderAccount($booking, $provider);
            }

            // 2. Credit system commission account
            if ((float)$booking->commission_amount > 0) {
                $this->creditCommissionAccount($booking);
            }

            // 3. If agent booking: debit agent + credit agent commission
            if ($booking->agent_id) {
                $this->processAgentTransaction($booking);
            }
        });
    }

    /**
     * Process financial entries when a booking is cancelled
     */
    public function processBookingCancellation(Booking $booking): void
    {
        DB::transaction(function () use ($booking) {
            $provider = $booking->provider;

            // Only reverse if ticket was issued (financial entries exist)
            if ($booking->status !== 'issued') return;

            // 1. Debit provider account (reverse revenue)
            if ($provider) {
                $providerAccount = $this->getProviderAccount($provider);
                if ($providerAccount) {
                    $providerAccount->debit(
                        (float)$booking->net_price,
                        "Cancellation reversal - Booking {$booking->booking_reference}",
                        "إلغاء - حجز {$booking->booking_reference}",
                        auth()->id(), 'booking', $booking->id
                    );
                    $provider->decrement('balance', (float)$booking->net_price);
                }
            }

            // 2. Debit system commission (reverse)
            if ((float)$booking->commission_amount > 0) {
                $commissionAccount = FinancialAccount::where('account_type', 'commission')->first();
                if ($commissionAccount) {
                    $commissionAccount->debit(
                        (float)$booking->commission_amount,
                        "Commission reversal - Booking {$booking->booking_reference}",
                        "إلغاء عمولة - حجز {$booking->booking_reference}",
                        auth()->id(), 'booking', $booking->id
                    );
                }
            }

            // 3. Refund agent if applicable
            if ($booking->agent_id) {
                $agent = $booking->agent;
                $agentAccount = $this->getAgentAccount($agent);
                if ($agentAccount && $agent) {
                    // Refund ticket price
                    $agentAccount->credit(
                        (float)$booking->total_price,
                        "Refund - Booking {$booking->booking_reference}",
                        "استرداد - حجز {$booking->booking_reference}",
                        auth()->id(), 'booking', $booking->id
                    );
                    $agent->increment('balance', (float)$booking->total_price);

                    // Reverse commission
                    $agentCommission = $this->calculateAgentCommission($booking, $agent);
                    if ($agentCommission > 0) {
                        $agentAccount->debit(
                            $agentCommission,
                            "Commission reversal - Booking {$booking->booking_reference}",
                            "إلغاء عمولة - حجز {$booking->booking_reference}",
                            auth()->id(), 'booking', $booking->id
                        );
                        $agent->decrement('balance', $agentCommission);
                    }
                }
            }
        });
    }

    /**
     * Add balance to agent account
     */
    public function addAgentBalance(Agent $agent, float $amount, string $method = 'manual'): void
    {
        DB::transaction(function () use ($agent, $amount, $method) {
            $agentAccount = $this->getOrCreateAgentAccount($agent);
            $agentAccount->credit(
                $amount,
                "Balance deposit via {$method}",
                "إيداع رصيد عبر {$method}",
                auth()->id(), 'deposit', $agent->id
            );
            $agent->increment('balance', $amount);
        });
    }

    protected function creditProviderAccount(Booking $booking, Provider $provider): void
    {
        $account = $this->getOrCreateProviderAccount($provider);
        $account->credit(
            (float)$booking->net_price,
            "Ticket issued - Booking {$booking->booking_reference}",
            "إصدار تذكرة - حجز {$booking->booking_reference}",
            auth()->id(), 'booking', $booking->id
        );
        $provider->increment('balance', (float)$booking->net_price);
    }

    protected function creditCommissionAccount(Booking $booking): void
    {
        $account = FinancialAccount::firstOrCreate(
            ['account_type' => 'commission'],
            ['account_name_en' => 'System Commissions', 'account_name_ar' => 'عمولات النظام', 'currency' => 'USD']
        );
        $account->credit(
            (float)$booking->commission_amount,
            "Commission - Booking {$booking->booking_reference}",
            "عمولة - حجز {$booking->booking_reference}",
            auth()->id(), 'booking', $booking->id
        );
    }

    protected function processAgentTransaction(Booking $booking): void
    {
        $agent = $booking->agent;
        if (!$agent) return;

        $account = $this->getOrCreateAgentAccount($agent);

        // Debit ticket price
        $account->debit(
            (float)$booking->total_price,
            "Ticket payment - Booking {$booking->booking_reference}",
            "دفع تذكرة - حجز {$booking->booking_reference}",
            auth()->id(), 'booking', $booking->id
        );
        $agent->decrement('balance', (float)$booking->total_price);

        // Credit agent commission
        $agentCommission = $this->calculateAgentCommission($booking, $agent);
        if ($agentCommission > 0) {
            $account->credit(
                $agentCommission,
                "Commission earned - Booking {$booking->booking_reference}",
                "عمولة مكتسبة - حجز {$booking->booking_reference}",
                auth()->id(), 'booking', $booking->id
            );
            $agent->increment('balance', $agentCommission);
        }
    }

    public function calculateAgentCommission(Booking $booking, Agent $agent): float
    {
        if (!$agent->commission_amount) return 0;
        if ($agent->commission_type === 'fixed') {
            return (float)$agent->commission_amount;
        }
        // Percentage of system commission
        return (float)$booking->commission_amount * ((float)$agent->commission_amount / 100);
    }

    protected function getOrCreateProviderAccount(Provider $provider): FinancialAccount
    {
        return FinancialAccount::firstOrCreate(
            ['provider_id' => $provider->id, 'account_type' => 'provider'],
            [
                'account_name_en' => $provider->business_name_en . ' Account',
                'account_name_ar' => ($provider->business_name_ar ?: $provider->business_name_en) . ' حساب',
                'currency' => 'USD',
            ]
        );
    }

    protected function getOrCreateAgentAccount(Agent $agent): FinancialAccount
    {
        return FinancialAccount::firstOrCreate(
            ['agent_id' => $agent->id, 'account_type' => 'agent'],
            [
                'account_name_en' => $agent->name . ' Account',
                'account_name_ar' => 'حساب ' . $agent->name,
                'currency' => 'USD',
            ]
        );
    }

    protected function getProviderAccount(Provider $provider): ?FinancialAccount
    {
        return FinancialAccount::where('provider_id', $provider->id)
            ->where('account_type', 'provider')->first();
    }

    protected function getAgentAccount(Agent $agent): ?FinancialAccount
    {
        return FinancialAccount::where('agent_id', $agent->id)
            ->where('account_type', 'agent')->first();
    }
}
