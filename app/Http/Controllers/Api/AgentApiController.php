<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\FlightSeat;
use App\Services\BookingService;
use App\Services\FinancialTransactionService;
use App\Services\SmartSearchService;
use App\Services\WhatsappService;
use Illuminate\Http\Request;

class AgentApiController extends Controller
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
        return response()->json(['success' => true, 'data' => $results]);
    }

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

        if ((float)$agent->balance < $totalPrice) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance',
                'required' => $totalPrice,
                'available' => (float)$agent->balance,
            ], 400);
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

            $this->whatsappService->sendBookingConfirmation($booking);

            return response()->json([
                'success' => true,
                'data' => $booking->load(['flight', 'passengers', 'customer']),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function addPassengers(Request $request, string $reference)
    {
        $agent = $request->attributes->get('agent');
        $booking = Booking::where('agent_id', $agent->id)
            ->where('booking_reference', $reference)
            ->where('status', 'pending')
            ->firstOrFail();

        $request->validate([
            'passengers' => 'required|array|min:1',
            'passengers.*.first_name' => 'required|string',
            'passengers.*.last_name' => 'required|string',
            'passengers.*.passport_number' => 'required|string',
            'passengers.*.date_of_birth' => 'required|date',
            'passengers.*.gender' => 'required|in:male,female',
            'passengers.*.nationality' => 'required|string',
            'passengers.*.type' => 'required|in:adult,child,infant',
        ]);

        foreach ($request->passengers as $passenger) {
            $booking->passengers()->create($passenger);
        }

        return response()->json(['success' => true, 'data' => $booking->load('passengers')]);
    }

    public function issueTicket(Request $request, string $reference)
    {
        $agent = $request->attributes->get('agent');
        $booking = Booking::where('agent_id', $agent->id)
            ->where('booking_reference', $reference)
            ->firstOrFail();

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Booking cannot be issued in current status: ' . $booking->status,
            ], 400);
        }

        if ((float)$agent->balance < (float)$booking->total_price) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance for ticket issuance',
                'required' => (float)$booking->total_price,
                'available' => (float)$agent->balance,
            ], 400);
        }

        $this->financialService->processTicketIssuance($booking);
        $booking->update(['status' => 'issued']);
        $commission = $this->financialService->calculateAgentCommission($booking, $agent);
        $this->whatsappService->sendBookingStatusUpdate($booking, 'issued');

        return response()->json([
            'success' => true,
            'data' => [
                'booking' => $booking->fresh()->load(['flight', 'passengers', 'customer']),
                'deducted_amount' => (float)$booking->total_price,
                'commission_earned' => $commission,
                'new_balance' => (float)$agent->fresh()->balance,
            ],
        ]);
    }

    public function getBooking(Request $request, string $reference)
    {
        $agent = $request->attributes->get('agent');
        $booking = Booking::with(['customer', 'flight.airline', 'flight.departureAirport', 'flight.arrivalAirport', 'passengers'])
            ->where('agent_id', $agent->id)
            ->where('booking_reference', $reference)
            ->firstOrFail();

        return response()->json(['success' => true, 'data' => $booking]);
    }

    public function getBalance(Request $request)
    {
        $agent = $request->attributes->get('agent');
        $commissionBalance = 0;
        if ($agent->financialAccount) {
            $commissionBalance = $agent->financialAccount->transactions()
                ->where('type', 'credit')
                ->where('description_en', 'LIKE', '%Commission%')
                ->sum('amount');
        }

        return response()->json([
            'success' => true,
            'data' => [
                'agent_name' => $agent->name,
                'main_balance' => (float)$agent->balance,
                'commission_balance' => $commissionBalance,
                'currency' => $agent->currency ?? 'USD',
            ],
        ]);
    }

    public function getTransactions(Request $request)
    {
        $agent = $request->attributes->get('agent');
        if (!$agent->financialAccount) {
            return response()->json(['success' => true, 'data' => ['transactions' => [], 'total' => 0]]);
        }

        $query = $agent->financialAccount->transactions()->latest();
        if ($request->filled('type')) $query->where('type', $request->type);
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->whereDate('created_at', '<=', $request->date_to);

        $limit = min($request->limit ?? 50, 100);
        $transactions = $query->limit($limit)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'transactions' => $transactions->map(fn($t) => [
                    'id' => $t->id, 'type' => $t->type, 'amount' => (float)$t->amount,
                    'balance_after' => (float)$t->balance_after, 'description' => $t->description_en,
                    'reference_type' => $t->reference_type, 'reference_id' => $t->reference_id,
                    'status' => 'completed', 'created_at' => $t->created_at->toISOString(),
                ]),
                'total' => $agent->financialAccount->transactions()->count(),
            ],
        ]);
    }
}
