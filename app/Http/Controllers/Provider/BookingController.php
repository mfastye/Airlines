<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Provider;
use App\Services\BookingService;
use App\Services\FinancialTransactionService;
use App\Services\WhatsappService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected BookingService $bookingService;
    protected WhatsappService $whatsappService;
    protected FinancialTransactionService $financialService;

    public function __construct(BookingService $bookingService, WhatsappService $whatsappService, FinancialTransactionService $financialService)
    {
        $this->bookingService = $bookingService;
        $this->whatsappService = $whatsappService;
        $this->financialService = $financialService;
    }

    protected function getProvider(): Provider
    {
        $provider = Provider::find(auth()->user()->provider_id);
        if (!$provider) abort(403);
        return $provider;
    }

    public function index(Request $request)
    {
        $provider = $this->getProvider();

        $query = Booking::with(['customer', 'flight.airline', 'flight.departureAirport', 'flight.arrivalAirport', 'agent', 'passengers', 'payments'])
            ->where('provider_id', $provider->id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_reference', 'LIKE', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('first_name', 'LIKE', "%{$search}%")
                          ->orWhere('last_name', 'LIKE', "%{$search}%");
                  });
            });
        }
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('payment_status')) $query->where('payment_status', $request->payment_status);
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->whereDate('created_at', '<=', $request->date_to);

        $bookings = $query->latest()->paginate(15);

        $stats = [
            'total' => Booking::where('provider_id', $provider->id)->count(),
            'pending' => Booking::where('provider_id', $provider->id)->where('status', 'pending')->count(),
            'confirmed' => Booking::where('provider_id', $provider->id)->where('status', 'confirmed')->count(),
            'issued' => Booking::where('provider_id', $provider->id)->where('status', 'issued')->count(),
            'cancelled' => Booking::where('provider_id', $provider->id)->where('status', 'cancelled')->count(),
        ];

        return view('provider.bookings.index', compact('bookings', 'provider', 'stats'));
    }

    public function show(Booking $booking)
    {
        $provider = $this->getProvider();
        if ($booking->provider_id !== $provider->id) abort(403);
        $booking->load(['customer', 'flight.airline', 'flight.departureAirport', 'flight.arrivalAirport', 'passengers', 'payments', 'agent', 'flightSeat']);
        return view('provider.bookings.show', compact('booking', 'provider'));
    }

    public function confirm(Booking $booking)
    {
        $provider = $this->getProvider();
        if ($booking->provider_id !== $provider->id) abort(403);
        if ($booking->status !== 'pending') return back()->with('error', __('Booking is not pending'));

        $booking->update(['status' => 'confirmed', 'confirmed_at' => now()]);
        $this->whatsappService->sendBookingStatusUpdate($booking, 'confirmed');

        if ($booking->agent_id && $booking->agent) {
            $agentPhone = $booking->agent->whatsapp ?? $booking->agent->phone;
            if ($agentPhone) {
                $this->whatsappService->sendMessage($agentPhone, "Booking {$booking->booking_reference} confirmed.", $booking->agent->name, 'booking', $booking->id);
            }
        }

        return back()->with('success', __('Booking confirmed successfully'));
    }

    public function cancel(Request $request, Booking $booking)
    {
        $provider = $this->getProvider();
        if ($booking->provider_id !== $provider->id) abort(403);
        $request->validate(['reason' => 'required|string|max:500']);

        // Process financial reversal if ticket was issued
        if ($booking->status === 'issued') {
            $this->financialService->processBookingCancellation($booking);
        }

        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $request->reason,
        ]);

        $this->whatsappService->sendBookingStatusUpdate($booking, 'cancelled');

        return back()->with('success', __('Booking cancelled'));
    }

    public function issueTicket(Request $request, Booking $booking)
    {
        $provider = $this->getProvider();
        if ($booking->provider_id !== $provider->id) abort(403);

        $request->validate([
            'ticket_number' => 'required|string|max:100',
            'ticket_image' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'pnr_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500',
        ]);

        $path = $request->file('ticket_image')->store('tickets', 'public');

        $booking->update([
            'ticket_number' => $request->ticket_number,
            'ticket_image' => $path,
            'status' => 'issued',
        ]);

        // Process financial transactions via service
        $this->financialService->processTicketIssuance($booking);

        // WhatsApp to customer
        $customer = $booking->customer;
        if ($customer) {
            $customerPhone = $customer->whatsapp ?? $customer->phone;
            if ($customerPhone) {
                $message = "Your ticket has been issued!\n"
                    . "Booking: {$booking->booking_reference}\n"
                    . "Ticket #: {$request->ticket_number}\n"
                    . "Flight: {$booking->flight->flight_number}\n"
                    . "Date: {$booking->flight->departure_time->format('Y-m-d H:i')}";
                $this->whatsappService->sendMessage($customerPhone, $message, $customer->full_name ?? '', 'booking', $booking->id);
            }
        }

        // WhatsApp to agent
        if ($booking->agent_id && $booking->agent) {
            $agentPhone = $booking->agent->whatsapp ?? $booking->agent->phone;
            if ($agentPhone) {
                $this->whatsappService->sendMessage($agentPhone, "Ticket issued for booking {$booking->booking_reference}. Ticket #: {$request->ticket_number}", $booking->agent->name, 'booking', $booking->id);
            }
        }

        return back()->with('success', __('Ticket issued successfully'));
    }

    public function uploadTicket(Request $request, Booking $booking)
    {
        return $this->issueTicket($request, $booking);
    }

    public function financial(Request $request)
    {
        $provider = $this->getProvider();
        $account = $provider->financialAccount;
        $query = $account ? $account->transactions()->latest() : null;

        if ($query && $request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($query && $request->filled('date_to')) $query->whereDate('created_at', '<=', $request->date_to);

        $transactions = $query ? $query->paginate(20) : collect();

        $stats = [
            'balance' => $account ? $account->balance : 0,
            'total_revenue' => Booking::where('provider_id', $provider->id)->where('status', 'issued')->sum('net_price'),
            'total_commissions' => Booking::where('provider_id', $provider->id)->where('status', 'issued')->sum('commission_amount'),
        ];

        return view('provider.financial.index', compact('provider', 'account', 'transactions', 'stats'));
    }

    public function reports(Request $request)
    {
        $provider = $this->getProvider();
        $query = Booking::with(['customer', 'flight.airline', 'agent'])->where('provider_id', $provider->id);

        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->whereDate('created_at', '<=', $request->date_to);
        if ($request->filled('status')) $query->where('status', $request->status);

        $bookings = $query->latest()->get();

        $summary = [
            'total_bookings' => $bookings->count(),
            'total_revenue' => $bookings->where('status', 'issued')->sum('net_price'),
            'total_commissions' => $bookings->where('status', 'issued')->sum('commission_amount'),
            'total_tickets' => $bookings->where('status', 'issued')->count(),
            'seats_sold' => $bookings->sum(fn($b) => $b->adults + $b->children),
        ];

        return view('provider.reports.index', compact('bookings', 'provider', 'summary'));
    }
}
