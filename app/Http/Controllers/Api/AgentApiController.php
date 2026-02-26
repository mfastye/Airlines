<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Customer;
use App\Models\FlightSeat;
use App\Services\BookingService;
use App\Services\SmartSearchService;
use Illuminate\Http\Request;

class AgentApiController extends Controller
{
    protected SmartSearchService $searchService;
    protected BookingService $bookingService;

    public function __construct(SmartSearchService $searchService, BookingService $bookingService)
    {
        $this->searchService = $searchService;
        $this->bookingService = $bookingService;
    }

    /**
     * Search flights via API
     */
    public function searchFlights(Request $request)
    {
        $request->validate([
            'departure_airport_id' => 'required|integer',
            'arrival_airport_id' => 'required|integer',
            'departure_date' => 'required|date',
            'return_date' => 'nullable|date',
            'trip_type' => 'required|in:one_way,round_trip',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'class' => 'required|in:economy,business,first',
        ]);

        $results = $this->searchService->search($request->all());

        return response()->json([
            'success' => true,
            'data' => $results,
        ]);
    }

    /**
     * Create booking via API
     */
    public function createBooking(Request $request)
    {
        $agent = $request->attributes->get('agent');

        $request->validate([
            'flight_id' => 'required|exists:flights,id',
            'flight_seat_id' => 'required|exists:flight_seats,id',
            'passengers' => 'required|array|min:1',
            'passengers.*.first_name' => 'required|string',
            'passengers.*.last_name' => 'required|string',
            'passengers.*.passport_number' => 'required|string',
            'passengers.*.date_of_birth' => 'required|date',
            'passengers.*.gender' => 'required|in:male,female',
            'passengers.*.nationality' => 'required|string',
            'passengers.*.type' => 'required|in:adult,child,infant',
            'customer_name' => 'required|string',
            'customer_phone' => 'required|string',
        ]);

        $seat = FlightSeat::findOrFail($request->flight_seat_id);
        $totalPrice = 0;
        foreach ($request->passengers as $p) {
            $totalPrice += match ($p['type']) {
                'adult' => (float)$seat->adult_price,
                'child' => (float)$seat->child_price,
                'infant' => (float)$seat->infant_price,
                default => 0,
            };
        }

        if ($agent->balance < $totalPrice) {
            return response()->json(['success' => false, 'message' => 'Insufficient balance'], 400);
        }

        $nameParts = explode(' ', $request->customer_name, 2);
        $customer = Customer::create([
            'first_name' => $nameParts[0],
            'last_name' => $nameParts[1] ?? '',
            'phone' => $request->customer_phone,
        ]);

        try {
            $booking = $this->bookingService->createBooking([
                'customer_id' => $customer->id,
                'flight_id' => $request->flight_id,
                'flight_seat_id' => $request->flight_seat_id,
                'trip_type' => 'one_way',
                'adults' => count(array_filter($request->passengers, fn($p) => $p['type'] === 'adult')),
                'children' => count(array_filter($request->passengers, fn($p) => $p['type'] === 'child')),
                'infants' => count(array_filter($request->passengers, fn($p) => $p['type'] === 'infant')),
                'agent_id' => $agent->id,
            ]);

            foreach ($request->passengers as $passenger) {
                $booking->passengers()->create($passenger);
            }

            $this->bookingService->confirmBooking($booking);

            return response()->json([
                'success' => true,
                'data' => $booking->load(['flight', 'passengers', 'customer']),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get booking details via API
     */
    public function getBooking(Request $request, $reference)
    {
        $agent = $request->attributes->get('agent');

        $booking = \App\Models\Booking::with(['customer', 'flight.airline', 'flight.departureAirport', 'flight.arrivalAirport', 'passengers'])
            ->where('agent_id', $agent->id)
            ->where('booking_reference', $reference)
            ->firstOrFail();

        return response()->json(['success' => true, 'data' => $booking]);
    }

    /**
     * Get agent balance via API
     */
    public function getBalance(Request $request)
    {
        $agent = $request->attributes->get('agent');

        return response()->json([
            'success' => true,
            'data' => [
                'balance' => $agent->balance,
                'currency' => $agent->currency,
            ],
        ]);
    }
}
