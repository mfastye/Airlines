<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Provider;
use App\Services\BookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
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

        $query = Booking::with(['customer', 'flight.airline', 'flight.departureAirport', 'flight.arrivalAirport', 'agent', 'payments'])
            ->where('provider_id', $provider->id);

        if ($request->filled('search')) {
            $query->where('booking_reference', 'LIKE', "%{$request->search}%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->latest()->paginate(15);
        return view('provider.bookings.index', compact('bookings', 'provider'));
    }

    public function show(Booking $booking)
    {
        $provider = $this->getProvider();
        if ($booking->provider_id !== $provider->id) abort(403);

        $booking->load(['customer', 'flight.airline', 'flight.departureAirport', 'flight.arrivalAirport', 'passengers', 'payments', 'agent']);
        return view('provider.bookings.show', compact('booking', 'provider'));
    }

    public function confirm(Booking $booking)
    {
        $provider = $this->getProvider();
        if ($booking->provider_id !== $provider->id) abort(403);

        $this->bookingService->confirmBooking($booking, auth()->id());
        return back()->with('success', __('messages.booking_confirmed'));
    }

    public function uploadTicket(Request $request, Booking $booking)
    {
        $provider = $this->getProvider();
        if ($booking->provider_id !== $provider->id) abort(403);

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
