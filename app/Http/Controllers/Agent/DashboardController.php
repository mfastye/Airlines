<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Airport;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Flight;
use App\Models\FlightSeat;
use App\Models\Passport;
use App\Services\BookingService;
use App\Services\SmartSearchService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected SmartSearchService $searchService;
    protected BookingService $bookingService;

    public function __construct(SmartSearchService $searchService, BookingService $bookingService)
    {
        $this->searchService = $searchService;
        $this->bookingService = $bookingService;
    }

    protected function getAgent(): Agent
    {
        $agent = Agent::find(auth()->user()->agent_id);
        if (!$agent) abort(403);
        return $agent;
    }

    public function index()
    {
        $agent = $this->getAgent();

        $stats = [
            'total_bookings' => Booking::where('agent_id', $agent->id)->count(),
            'pending_bookings' => Booking::where('agent_id', $agent->id)->where('status', 'pending')->count(),
            'confirmed_bookings' => Booking::where('agent_id', $agent->id)->where('status', 'confirmed')->count(),
            'balance' => $agent->balance,
            'total_revenue' => Booking::where('agent_id', $agent->id)->where('status', 'confirmed')->sum('total_price'),
        ];

        $recentBookings = Booking::with(['customer', 'flight.airline'])
            ->where('agent_id', $agent->id)
            ->latest()->limit(10)->get();

        $account = $agent->financialAccount;
        $recentTransactions = $account ? $account->transactions()->latest()->limit(10)->get() : collect();

        $airports = Airport::where('status', 'active')->get();

        return view('agent.dashboard', compact('agent', 'stats', 'recentBookings', 'recentTransactions', 'airports'));
    }

    public function search(Request $request)
    {
        $agent = $this->getAgent();

        $request->validate([
            'departure_airport_id' => 'required|exists:airports,id',
            'arrival_airport_id' => 'required|exists:airports,id',
            'departure_date' => 'required|date|after_or_equal:today',
            'adults' => 'required|integer|min:1',
            'class' => 'required|in:economy,business,first',
        ]);

        $results = $this->searchService->search($request->all());
        $airports = Airport::where('status', 'active')->get();

        return view('agent.search-results', compact('results', 'airports', 'agent'));
    }

    public function book(Request $request)
    {
        $agent = $this->getAgent();

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
            'customer_phone' => 'required|string',
            'customer_name' => 'required|string',
        ]);

        $seat = FlightSeat::findOrFail($request->flight_seat_id);
        $totalPassengers = count(array_filter($request->passengers, fn($p) => $p['type'] !== 'infant'));
        $totalPrice = 0;

        foreach ($request->passengers as $p) {
            $totalPrice += match ($p['type']) {
                'adult' => $seat->adult_price,
                'child' => $seat->child_price,
                'infant' => $seat->infant_price,
                default => 0,
            };
        }

        // Check agent balance
        if ($agent->balance < $totalPrice) {
            return back()->withErrors(['error' => __('messages.insufficient_balance')]);
        }

        // Create customer
        $nameParts = explode(' ', $request->customer_name, 2);
        $customer = Customer::create([
            'first_name' => $nameParts[0],
            'last_name' => $nameParts[1] ?? '',
            'phone' => $request->customer_phone,
            'whatsapp' => $request->customer_phone,
        ]);

        $flight = Flight::findOrFail($request->flight_id);

        $booking = $this->bookingService->createBooking([
            'customer_id' => $customer->id,
            'flight_id' => $request->flight_id,
            'flight_seat_id' => $request->flight_seat_id,
            'trip_type' => $request->trip_type ?? 'one_way',
            'adults' => count(array_filter($request->passengers, fn($p) => $p['type'] === 'adult')),
            'children' => count(array_filter($request->passengers, fn($p) => $p['type'] === 'child')),
            'infants' => count(array_filter($request->passengers, fn($p) => $p['type'] === 'infant')),
            'agent_id' => $agent->id,
        ]);

        foreach ($request->passengers as $passenger) {
            $booking->passengers()->create($passenger);
        }

        // Auto-confirm since agent has balance
        $this->bookingService->confirmBooking($booking, auth()->id());

        return redirect()->route('agent.booking.show', $booking->id)->with('success', __('messages.booking_created'));
    }

    public function bookings(Request $request)
    {
        $agent = $this->getAgent();

        $query = Booking::with(['customer', 'flight.airline', 'flight.departureAirport', 'flight.arrivalAirport'])
            ->where('agent_id', $agent->id);

        if ($request->filled('search')) {
            $query->where('booking_reference', 'LIKE', "%{$request->search}%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->latest()->paginate(15);
        return view('agent.bookings', compact('bookings', 'agent'));
    }

    public function showBooking(Booking $booking)
    {
        $agent = $this->getAgent();
        if ($booking->agent_id !== $agent->id) abort(403);

        $booking->load(['customer', 'flight.airline', 'flight.departureAirport', 'flight.arrivalAirport', 'passengers', 'payments']);
        return view('agent.booking-show', compact('booking', 'agent'));
    }

    public function financial()
    {
        $agent = $this->getAgent();
        $account = $agent->financialAccount;
        $transactions = $account ? $account->transactions()->latest()->paginate(20) : collect();

        return view('agent.financial', compact('agent', 'account', 'transactions'));
    }
}
