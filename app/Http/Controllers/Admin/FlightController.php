<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\Flight;
use App\Models\FlightSeat;
use App\Models\Provider;
use Illuminate\Http\Request;

class FlightController extends Controller
{
    public function index(Request $request)
    {
        $query = Flight::with(['airline', 'departureAirport', 'arrivalAirport', 'provider', 'seats']);

        if ($request->filled('search')) {
            $query->where('flight_number', 'LIKE', "%{$request->search}%");
        }
        if ($request->filled('airline_id')) {
            $query->where('airline_id', $request->airline_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date')) {
            $query->whereDate('departure_time', $request->date);
        }
        if ($request->filled('provider_id')) {
            $query->where('provider_id', $request->provider_id);
        }

        $flights = $query->latest()->paginate(15);
        $airlines = Airline::where('status', 'active')->get();
        $providers = Provider::where('status', 'active')->get();

        return view('admin.flights.index', compact('flights', 'airlines', 'providers'));
    }

    public function create()
    {
        $airlines = Airline::where('status', 'active')->get();
        $airports = Airport::where('status', 'active')->get();
        $providers = Provider::where('status', 'active')->get();

        return view('admin.flights.create', compact('airlines', 'airports', 'providers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'flight_number' => 'required|string|max:20',
            'airline_id' => 'required|exists:airlines,id',
            'departure_airport_id' => 'required|exists:airports,id',
            'arrival_airport_id' => 'required|exists:airports,id|different:departure_airport_id',
            'departure_time' => 'required|date|after:now',
            'arrival_time' => 'required|date|after:departure_time',
            'aircraft_type' => 'nullable|string|max:100',
            'stops' => 'nullable|integer|min:0',
            'stop_details' => 'nullable|string',
            'status' => 'required|in:scheduled,active,cancelled',
            'provider_id' => 'nullable|exists:providers,id',
            // Seats data
            'seats' => 'required|array|min:1',
            'seats.*.class' => 'required|in:economy,business,first',
            'seats.*.total_seats' => 'required|integer|min:1',
            'seats.*.adult_price' => 'required|numeric|min:0',
            'seats.*.child_price' => 'required|numeric|min:0',
            'seats.*.infant_price' => 'nullable|numeric|min:0',
            'seats.*.baggage_allowance_kg' => 'nullable|integer|min:0',
            'seats.*.hand_baggage_kg' => 'nullable|integer|min:0',
            'seats.*.meal_included' => 'nullable|boolean',
            'seats.*.wifi_available' => 'nullable|boolean',
            'seats.*.entertainment_available' => 'nullable|boolean',
        ]);

        // Calculate duration
        $departure = \Carbon\Carbon::parse($data['departure_time']);
        $arrival = \Carbon\Carbon::parse($data['arrival_time']);
        $data['duration_minutes'] = $departure->diffInMinutes($arrival);
        $data['created_by'] = auth()->id();

        $flight = Flight::create($data);

        // Create seats
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
                'entertainment_available' => $seatData['entertainment_available'] ?? false,
                'provider_id' => $data['provider_id'] ?? null,
            ]);
        }

        return redirect()->route('admin.flights.index')->with('success', __('messages.created_successfully'));
    }

    public function show(Flight $flight)
    {
        $flight->load(['airline', 'departureAirport', 'arrivalAirport', 'seats', 'bookings.customer', 'provider']);
        return view('admin.flights.show', compact('flight'));
    }

    public function edit(Flight $flight)
    {
        $flight->load('seats');
        $airlines = Airline::where('status', 'active')->get();
        $airports = Airport::where('status', 'active')->get();
        $providers = Provider::where('status', 'active')->get();

        return view('admin.flights.edit', compact('flight', 'airlines', 'airports', 'providers'));
    }

    public function update(Request $request, Flight $flight)
    {
        $data = $request->validate([
            'flight_number' => 'required|string|max:20',
            'airline_id' => 'required|exists:airlines,id',
            'departure_airport_id' => 'required|exists:airports,id',
            'arrival_airport_id' => 'required|exists:airports,id|different:departure_airport_id',
            'departure_time' => 'required|date',
            'arrival_time' => 'required|date|after:departure_time',
            'aircraft_type' => 'nullable|string|max:100',
            'stops' => 'nullable|integer|min:0',
            'stop_details' => 'nullable|string',
            'status' => 'required|in:scheduled,active,cancelled,completed,delayed',
            'provider_id' => 'nullable|exists:providers,id',
        ]);

        $departure = \Carbon\Carbon::parse($data['departure_time']);
        $arrival = \Carbon\Carbon::parse($data['arrival_time']);
        $data['duration_minutes'] = $departure->diffInMinutes($arrival);

        $flight->update($data);

        // Update seats if provided
        if ($request->has('seats')) {
            foreach ($request->seats as $seatData) {
                if (!empty($seatData['id'])) {
                    $seat = FlightSeat::find($seatData['id']);
                    if ($seat) {
                        $diff = ($seatData['total_seats'] ?? $seat->total_seats) - $seat->total_seats;
                        $seat->update([
                            'total_seats' => $seatData['total_seats'] ?? $seat->total_seats,
                            'available_seats' => max(0, $seat->available_seats + $diff),
                            'adult_price' => $seatData['adult_price'] ?? $seat->adult_price,
                            'child_price' => $seatData['child_price'] ?? $seat->child_price,
                            'infant_price' => $seatData['infant_price'] ?? $seat->infant_price,
                            'baggage_allowance_kg' => $seatData['baggage_allowance_kg'] ?? $seat->baggage_allowance_kg,
                            'meal_included' => $seatData['meal_included'] ?? $seat->meal_included,
                            'wifi_available' => $seatData['wifi_available'] ?? $seat->wifi_available,
                            'entertainment_available' => $seatData['entertainment_available'] ?? $seat->entertainment_available,
                        ]);
                    }
                } else {
                    $flight->seats()->create([
                        'class' => $seatData['class'],
                        'total_seats' => $seatData['total_seats'],
                        'available_seats' => $seatData['total_seats'],
                        'adult_price' => $seatData['adult_price'],
                        'child_price' => $seatData['child_price'],
                        'infant_price' => $seatData['infant_price'] ?? 0,
                        'provider_id' => $data['provider_id'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('admin.flights.index')->with('success', __('messages.updated_successfully'));
    }

    public function destroy(Flight $flight)
    {
        if ($flight->bookings()->whereIn('status', ['confirmed', 'pending'])->exists()) {
            return back()->with('error', __('messages.cannot_delete_has_bookings'));
        }
        $flight->delete();
        return redirect()->route('admin.flights.index')->with('success', __('messages.deleted_successfully'));
    }
}
