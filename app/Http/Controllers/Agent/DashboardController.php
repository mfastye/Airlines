<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Airport;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Flight;
use App\Models\FlightSeat;
use App\Services\BookingService;
use App\Services\FinancialTransactionService;
use App\Services\SmartSearchService;
use App\Services\WhatsappService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected SmartSearchService $searchService;
    protected BookingService $bookingService;
    protected FinancialTransactionService $financialService;
    protected WhatsappService $whatsappService;

    public function __construct(
        SmartSearchService $searchService,
        BookingService $bookingService,
        FinancialTransactionService $financialService,
        WhatsappService $whatsappService
    ) {
        $this->searchService = $searchService;
        $this->bookingService = $bookingService;
        $this->financialService = $financialService;
        $this->whatsappService = $whatsappService;
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
            'issued_bookings' => Booking::where('agent_id', $agent->id)->where('status', 'issued')->count(),
            'today_bookings' => Booking::where('agent_id', $agent->id)->whereDate('created_at', today())->count(),
            'balance' => $agent->balance,
            'total_revenue' => Booking::where('agent_id', $agent->id)->whereIn('status', ['confirmed', 'issued'])->sum('total_price'),
            'commission_balance' => 0,
        ];

        if ($agent->financialAccount) {
            $stats['commission_balance'] = $agent->financialAccount->transactions()
                ->where('type', 'credit')
                ->where('description_en', 'LIKE', '%Commission%')
                ->sum('amount');
        }

        $recentBookings = Booking::with(['customer', 'flight.airline', 'flight.departureAirport', 'flight.arrivalAirport'])
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
        $totalPrice = 0;

        foreach ($request->passengers as $p) {
            $totalPrice += match ($p['type']) {
                'adult' => (float)$seat->adult_price,
                'child' => (float)$seat->child_price,
                'infant' => (float)$seat->infant_price,
                default => 0,
            };
        }

        if ((float)$agent->balance < $totalPrice) {
            return back()->withErrors(['error' => __('Insufficient balance. Required: $') . number_format($totalPrice, 2) . __('. Available: $') . number_format($agent->balance, 2)]);
        }

        $nameParts = explode(' ', $request->customer_name, 2);
        $customer = Customer::create([
            'first_name' => $nameParts[0],
            'last_name' => $nameParts[1] ?? '',
            'phone' => $request->customer_phone,
            'whatsapp' => $request->customer_phone,
        ]);

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

        $this->whatsappService->sendBookingConfirmation($booking);

        return redirect()->route('agent.booking.show', $booking->id)->with('success', __('Booking created successfully'));
    }

    public function bookings(Request $request)
    {
        $agent = $this->getAgent();

        $query = Booking::with(['customer', 'flight.airline', 'flight.departureAirport', 'flight.arrivalAirport'])
            ->where('agent_id', $agent->id);

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
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $bookings = $query->latest()->paginate(15);
        return view('agent.bookings.index', compact('bookings', 'agent'));
    }

    public function showBooking(Booking $booking)
    {
        $agent = $this->getAgent();
        if ($booking->agent_id !== $agent->id) abort(403);

        $booking->load(['customer', 'flight.airline', 'flight.departureAirport', 'flight.arrivalAirport', 'passengers', 'payments']);
        return view('agent.bookings.show', compact('booking', 'agent'));
    }

    public function financial(Request $request)
    {
        $agent = $this->getAgent();
        $account = $agent->financialAccount;

        $query = $account ? $account->transactions()->latest() : null;

        if ($query && $request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($query && $request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($query && $request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query ? $query->paginate(20) : collect();

        $commissionStats = [
            'total_commissions' => 0,
            'total_debits' => 0,
            'total_credits' => 0,
        ];
        if ($account) {
            $commissionStats['total_commissions'] = $account->transactions()
                ->where('type', 'credit')
                ->where('description_en', 'LIKE', '%Commission%')
                ->sum('amount');
            $commissionStats['total_debits'] = $account->transactions()->where('type', 'debit')->sum('amount');
            $commissionStats['total_credits'] = $account->transactions()->where('type', 'credit')->sum('amount');
        }

        return view('agent.financial.index', compact('agent', 'account', 'transactions', 'commissionStats'));
    }

    public function settings()
    {
        $agent = $this->getAgent();
        return view('agent.settings.index', compact('agent'));
    }

    public function updateSettings(Request $request)
    {
        $agent = $this->getAgent();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $agent->update($request->only(['name', 'phone', 'email']));

        $user = auth()->user();
        $user->update(['name' => $request->name, 'email' => $request->email ?? $user->email]);

        return back()->with('success', __('Settings updated'));
    }
}
