<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\Flight;
use App\Models\FlightSeat;
use App\Models\Provider;
use Illuminate\Http\Request;

class FlightController extends Controller
{
    protected function getProvider(): Provider
    {
        $provider = Provider::find(auth()->user()->provider_id);
        if (!$provider) abort(403);
        return $provider;
    }

    public function index(Request $request)
    {
        $provider = $this->getProvider();

        $query = Flight::with(['airline', 'departureAirport', 'arrivalAirport', 'seats'])
            ->where('provider_id', $provider->id);

        if ($request->filled('search')) {
            $query->where('flight_number', 'LIKE', "%{$request->search}%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $flights = $query->latest()->paginate(15);
        return view('provider.flights.index', compact('flights', 'provider'));
    }

    public function create()
    {
        $provider = $this->getProvider();
        $allowedAirlines = $provider->allowed_airlines ?? [];
        $airlines = Airline::where('status', 'active')
            ->when(!empty($allowedAirlines), fn($q) => $q->whereIn('id', $allowedAirlines))
            ->get();
        $airports = Airport::where('status', 'active')->get();

        return view('provider.flights.create', compact('airlines', 'airports', 'provider'));
    }

    public function store(Request $request)
    {
        $provider = $this->getProvider();

        $data = $request->validate([
            'flight_number' => 'required|string|max:20',
            'airline_id' => 'required|exists:airlines,id',
            'departure_airport_id' => 'required|exists:airports,id',
            'arrival_airport_id' => 'required|exists:airports,id|different:departure_airport_id',
            'departure_time' => 'required|date|after:now',
            'arrival_time' => 'required|date|after:departure_time',
            'aircraft_type' => 'nullable|string|max:100',
            'stops' => 'nullable|integer|min:0',
            'seats' => 'required|array|min:1',
            'seats.*.class' => 'required|in:economy,business,first',
            'seats.*.total_seats' => 'required|integer|min:1',
            'seats.*.adult_price' => 'required|numeric|min:0',
            'seats.*.child_price' => 'required|numeric|min:0',
            'seats.*.infant_price' => 'nullable|numeric|min:0',
            'seats.*.baggage_allowance_kg' => 'nullable|integer|min:0',
            'seats.*.meal_included' => 'nullable|boolean',
            'seats.*.wifi_available' => 'nullable|boolean',
        ]);

        $departure = \Carbon\Carbon::parse($data['departure_time']);
        $arrival = \Carbon\Carbon::parse($data['arrival_time']);
        $data['duration_minutes'] = $departure->diffInMinutes($arrival);
        $data['provider_id'] = $provider->id;
        $data['created_by'] = auth()->id();
        $data['status'] = 'scheduled';

        $flight = Flight::create($data);

        foreach ($request->seats as $seatData) {
            $flight->seats()->create([
                'class' => $seatData['class'],
                'total_seats' => $seatData['total_seats'],
                'available_seats' => $seatData['total_seats'],
                'adult_price' => $seatData['adult_price'],
                'child_price' => $seatData['child_price'],
                'infant_price' => $seatData['infant_price'] ?? 0,
                'baggage_allowance_kg' => $seatData['baggage_allowance_kg'] ?? 23,
                'hand_baggage_kg' => $seatData['hand_baggage_kg'] ?? 7,
                'meal_included' => $seatData['meal_included'] ?? false,
                'wifi_available' => $seatData['wifi_available'] ?? false,
                'provider_id' => $provider->id,
            ]);
        }

        return redirect()->route('provider.flights.index')->with('success', __('messages.created_successfully'));
    }

    public function show(Flight $flight)
    {
        $provider = $this->getProvider();
        if ($flight->provider_id !== $provider->id) abort(403);

        $flight->load(['airline', 'departureAirport', 'arrivalAirport', 'seats', 'bookings.customer']);
        return view('provider.flights.show', compact('flight', 'provider'));
    }

    public function edit(Flight $flight)
    {
        $provider = $this->getProvider();
        if ($flight->provider_id !== $provider->id) abort(403);

        $flight->load('seats');
        $allowedAirlines = $provider->allowed_airlines ?? [];
        $airlines = Airline::where('status', 'active')
            ->when(!empty($allowedAirlines), fn($q) => $q->whereIn('id', $allowedAirlines))
            ->get();
        $airports = Airport::where('status', 'active')->get();

        return view('provider.flights.edit', compact('flight', 'airlines', 'airports', 'provider'));
    }

    public function update(Request $request, Flight $flight)
    {
        $provider = $this->getProvider();
        if ($flight->provider_id !== $provider->id) abort(403);

        $data = $request->validate([
            'flight_number' => 'required|string|max:20',
            'airline_id' => 'required|exists:airlines,id',
            'departure_airport_id' => 'required|exists:airports,id',
            'arrival_airport_id' => 'required|exists:airports,id',
            'departure_time' => 'required|date',
            'arrival_time' => 'required|date|after:departure_time',
            'aircraft_type' => 'nullable|string|max:100',
            'stops' => 'nullable|integer|min:0',
        ]);

        $departure = \Carbon\Carbon::parse($data['departure_time']);
        $arrival = \Carbon\Carbon::parse($data['arrival_time']);
        $data['duration_minutes'] = $departure->diffInMinutes($arrival);

        $flight->update($data);

        if ($request->has('seats')) {
            foreach ($request->seats as $seatData) {
                if (!empty($seatData['id'])) {
                    $seat = FlightSeat::where('id', $seatData['id'])->where('provider_id', $provider->id)->first();
                    if ($seat) {
                        $seat->update([
                            'adult_price' => $seatData['adult_price'] ?? $seat->adult_price,
                            'child_price' => $seatData['child_price'] ?? $seat->child_price,
                            'total_seats' => $seatData['total_seats'] ?? $seat->total_seats,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('provider.flights.index')->with('success', __('messages.updated_successfully'));
    }
}
