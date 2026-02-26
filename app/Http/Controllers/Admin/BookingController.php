<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\BookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function index(Request $request)
    {
        $query = Booking::with(['customer', 'flight.airline', 'flight.departureAirport', 'flight.arrivalAirport', 'provider', 'agent', 'payments']);

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

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        if ($request->filled('provider_id')) {
            $query->where('provider_id', $request->provider_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $bookings = $query->latest()->paginate(15);
        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $booking->load([
            'customer', 'flight.airline', 'flight.departureAirport', 'flight.arrivalAirport',
            'flightSeat', 'returnFlight', 'returnFlightSeat', 'passengers',
            'provider', 'agent', 'payments.gateway', 'bookedBy'
        ]);
        return view('admin.bookings.show', compact('booking'));
    }

    public function confirm(Booking $booking)
    {
        $this->bookingService->confirmBooking($booking, auth()->id());
        return back()->with('success', __('messages.booking_confirmed'));
    }

    public function cancel(Request $request, Booking $booking)
    {
        $request->validate(['cancellation_reason' => 'required|string']);
        $this->bookingService->cancelBooking($booking, $request->cancellation_reason, auth()->id());
        return back()->with('success', __('messages.booking_cancelled'));
    }

    public function uploadTicket(Request $request, Booking $booking)
    {
        $request->validate([
            'ticket_number' => 'required|string',
            'ticket_image' => 'required|file|max:5120',
        ]);

        $path = $request->file('ticket_image')->store('tickets', 'public');

        $booking->update([
            'ticket_number' => $request->ticket_number,
            'ticket_image' => $path,
        ]);

        return back()->with('success', __('messages.ticket_uploaded'));
    }
}
