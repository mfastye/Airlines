<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialAccount;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Services\BookingService;
use App\Services\WhatsappService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected BookingService $bookingService;
    protected WhatsappService $whatsappService;

    public function __construct(BookingService $bookingService, WhatsappService $whatsappService)
    {
        $this->bookingService = $bookingService;
        $this->whatsappService = $whatsappService;
    }

    public function index(Request $request)
    {
        $query = Payment::with(['booking.customer', 'gateway', 'confirmedBy']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('gateway_id')) {
            $query->where('payment_gateway_id', $request->gateway_id);
        }

        $payments = $query->latest()->paginate(15);
        $gateways = PaymentGateway::all();
        return view('admin.payments.index', compact('payments', 'gateways'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['booking.customer', 'booking.flight', 'gateway', 'confirmedBy']);
        return view('admin.payments.show', compact('payment'));
    }

    public function confirm(Payment $payment)
    {
        $payment->update([
            'status' => 'confirmed',
            'confirmed_by' => auth()->id(),
            'confirmed_at' => now(),
        ]);

        // Confirm the booking
        $this->bookingService->confirmBooking($payment->booking, auth()->id());

        // Credit gateway account
        $gatewayAccount = FinancialAccount::firstOrCreate(
            ['payment_gateway_id' => $payment->payment_gateway_id, 'account_type' => 'gateway'],
            ['account_name_en' => 'Gateway Account', 'account_name_ar' => 'حساب البوابة', 'currency' => 'USD']
        );
        $gatewayAccount->credit($payment->amount, "Payment {$payment->transaction_id}", "دفع {$payment->transaction_id}", auth()->id(), 'payment', $payment->id);

        return back()->with('success', __('messages.payment_confirmed'));
    }

    public function reject(Request $request, Payment $payment)
    {
        $request->validate(['rejection_reason' => 'required|string']);

        $payment->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'confirmed_by' => auth()->id(),
            'confirmed_at' => now(),
        ]);

        $payment->booking->update(['payment_status' => 'failed']);

        // Send rejection WhatsApp
        $customer = $payment->booking->customer;
        if ($customer->whatsapp || $customer->phone) {
            $this->whatsappService->sendMessage(
                $customer->whatsapp ?? $customer->phone,
                "Payment rejected for booking {$payment->booking->booking_reference}. Reason: {$request->rejection_reason}",
                $customer->full_name,
                'payment',
                $payment->id
            );
        }

        return back()->with('success', __('messages.payment_rejected'));
    }

    // Payment Gateway Management
    public function gateways()
    {
        $gateways = PaymentGateway::withCount('payments')->get();
        return view('admin.payments.gateways', compact('gateways'));
    }

    public function createGateway()
    {
        return view('admin.payments.gateway-form');
    }

    public function storeGateway(Request $request)
    {
        $data = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'instructions_en' => 'nullable|string',
            'instructions_ar' => 'nullable|string',
            'type' => 'required|in:manual,automatic',
            'api_endpoint' => 'nullable|url',
            'api_key' => 'nullable|string',
            'api_secret' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('gateways', 'public');
        }

        $gateway = PaymentGateway::create($data);

        FinancialAccount::create([
            'account_name_en' => $data['name_en'] . ' Account',
            'account_name_ar' => $data['name_ar'] . ' حساب',
            'account_type' => 'gateway',
            'payment_gateway_id' => $gateway->id,
            'currency' => 'USD',
        ]);

        return redirect()->route('admin.payment-gateways.index')->with('success', __('messages.created_successfully'));
    }

    public function editGateway(PaymentGateway $gateway)
    {
        return view('admin.payments.gateway-form', compact('gateway'));
    }

    public function updateGateway(Request $request, PaymentGateway $gateway)
    {
        $data = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'instructions_en' => 'nullable|string',
            'instructions_ar' => 'nullable|string',
            'type' => 'required|in:manual,automatic',
            'api_endpoint' => 'nullable|url',
            'api_key' => 'nullable|string',
            'api_secret' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('gateways', 'public');
        }

        $gateway->update($data);
        return redirect()->route('admin.payment-gateways.index')->with('success', __('messages.updated_successfully'));
    }
}
