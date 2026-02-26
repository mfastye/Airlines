<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Flight;
use App\Models\FlightSeat;
use App\Models\FinancialAccount;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class BookingService
{
    protected WhatsappService $whatsappService;

    public function __construct(WhatsappService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Create a new booking
     */
    public function createBooking(array $data): Booking
    {
        return DB::transaction(function () use ($data) {
            $flight = Flight::findOrFail($data['flight_id']);
            $seat = FlightSeat::findOrFail($data['flight_seat_id']);

            $totalPassengers = ($data['adults'] ?? 1) + ($data['children'] ?? 0);

            // Check availability
            if ($seat->available_seats < $totalPassengers) {
                throw new \Exception('Not enough seats available');
            }

            // Calculate price
            $adultTotal = ($data['adults'] ?? 1) * $seat->adult_price;
            $childTotal = ($data['children'] ?? 0) * $seat->child_price;
            $infantTotal = ($data['infants'] ?? 0) * $seat->infant_price;
            $totalPrice = $adultTotal + $childTotal + $infantTotal;

            // Calculate commission
            $commissionAmount = 0;
            if ($flight->provider_id) {
                $provider = $flight->provider;
                if ($provider->commission_type === 'fixed') {
                    $commissionAmount = $provider->commission_amount * $totalPassengers;
                } else {
                    $commissionAmount = $totalPrice * ($provider->commission_amount / 100);
                }
            }

            // Handle return flight if round trip
            if (($data['trip_type'] ?? 'one_way') === 'round_trip' && !empty($data['return_flight_seat_id'])) {
                $returnSeat = FlightSeat::findOrFail($data['return_flight_seat_id']);
                $totalPrice += ($data['adults'] ?? 1) * $returnSeat->adult_price;
                $totalPrice += ($data['children'] ?? 0) * $returnSeat->child_price;
                $totalPrice += ($data['infants'] ?? 0) * $returnSeat->infant_price;
            }

            $booking = Booking::create([
                'customer_id' => $data['customer_id'],
                'flight_id' => $data['flight_id'],
                'flight_seat_id' => $data['flight_seat_id'],
                'return_flight_id' => $data['return_flight_id'] ?? null,
                'return_flight_seat_id' => $data['return_flight_seat_id'] ?? null,
                'trip_type' => $data['trip_type'] ?? 'one_way',
                'class' => $seat->class,
                'adults' => $data['adults'] ?? 1,
                'children' => $data['children'] ?? 0,
                'infants' => $data['infants'] ?? 0,
                'total_price' => $totalPrice,
                'commission_amount' => $commissionAmount,
                'net_price' => $totalPrice - $commissionAmount,
                'has_visa' => $data['has_visa'] ?? false,
                'special_requests' => $data['special_requests'] ?? null,
                'provider_id' => $flight->provider_id,
                'agent_id' => $data['agent_id'] ?? null,
                'booked_by' => auth()->id(),
                'status' => 'pending',
                'payment_status' => 'pending',
            ]);

            // Decrement seats
            $seat->decrementSeats($totalPassengers);

            return $booking;
        });
    }

    /**
     * Confirm a booking after payment
     */
    public function confirmBooking(Booking $booking, ?int $confirmedBy = null): Booking
    {
        return DB::transaction(function () use ($booking, $confirmedBy) {
            $booking->update([
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'confirmed_at' => now(),
            ]);

            // Process financial transactions
            $this->processFinancialTransaction($booking);

            // Send WhatsApp confirmation
            $this->whatsappService->sendBookingConfirmation($booking);

            return $booking->fresh();
        });
    }

    /**
     * Cancel a booking
     */
    public function cancelBooking(Booking $booking, string $reason, ?int $cancelledBy = null): Booking
    {
        return DB::transaction(function () use ($booking, $reason) {
            // Restore seats
            $seat = $booking->flightSeat;
            $totalPassengers = $booking->adults + $booking->children;
            $seat->increment('available_seats', $totalPassengers);
            if ($seat->status === 'sold_out') {
                $seat->update(['status' => 'available']);
            }

            $booking->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $reason,
            ]);

            // Send WhatsApp notification
            $this->whatsappService->sendBookingStatusUpdate($booking, 'cancelled');

            return $booking->fresh();
        });
    }

    /**
     * Process financial transactions for confirmed booking
     */
    protected function processFinancialTransaction(Booking $booking): void
    {
        // Credit provider account
        if ($booking->provider_id) {
            $providerAccount = FinancialAccount::firstOrCreate(
                ['provider_id' => $booking->provider_id, 'account_type' => 'provider'],
                [
                    'account_name_en' => 'Provider Account',
                    'account_name_ar' => 'حساب المزود',
                    'currency' => 'USD',
                ]
            );
            $providerAccount->credit(
                $booking->net_price,
                "Booking {$booking->booking_reference} revenue",
                "إيرادات حجز {$booking->booking_reference}",
                auth()->id(),
                'booking',
                $booking->id
            );
        }

        // Credit system commission account
        if ($booking->commission_amount > 0) {
            $commissionAccount = FinancialAccount::firstOrCreate(
                ['account_type' => 'commission'],
                [
                    'account_name_en' => 'System Commissions',
                    'account_name_ar' => 'عمولات النظام',
                    'currency' => 'USD',
                ]
            );
            $commissionAccount->credit(
                $booking->commission_amount,
                "Commission from booking {$booking->booking_reference}",
                "عمولة من حجز {$booking->booking_reference}",
                auth()->id(),
                'booking',
                $booking->id
            );
        }

        // Debit agent account if booked by agent
        if ($booking->agent_id) {
            $agentAccount = FinancialAccount::firstOrCreate(
                ['agent_id' => $booking->agent_id, 'account_type' => 'agent'],
                [
                    'account_name_en' => 'Agent Account',
                    'account_name_ar' => 'حساب الوكيل',
                    'currency' => 'USD',
                ]
            );
            $agentAccount->debit(
                $booking->total_price,
                "Booking {$booking->booking_reference} payment",
                "دفع حجز {$booking->booking_reference}",
                auth()->id(),
                'booking',
                $booking->id
            );
        }
    }
}
