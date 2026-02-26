<?php

namespace App\Services;

use App\Models\Flight;
use App\Models\FlightSeat;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SmartSearchService
{
    /**
     * Smart flight search with AI-powered suggestions
     */
    public function search(array $params): array
    {
        $departureAirport = $params['departure_airport_id'] ?? null;
        $arrivalAirport = $params['arrival_airport_id'] ?? null;
        $departureDate = $params['departure_date'] ?? null;
        $returnDate = $params['return_date'] ?? null;
        $tripType = $params['trip_type'] ?? 'one_way';
        $passengers = ($params['adults'] ?? 1) + ($params['children'] ?? 0);
        $class = $params['class'] ?? 'economy';

        // Search for exact matches
        $exactFlights = $this->searchFlights($departureAirport, $arrivalAirport, $departureDate, $class, $passengers);

        // If no exact matches, find nearby dates (AI smart suggestion)
        $suggestedFlights = collect();
        if ($exactFlights->isEmpty() && $departureDate) {
            $suggestedFlights = $this->findNearbyFlights($departureAirport, $arrivalAirport, $departureDate, $class, $passengers);
        }

        // Search return flights if round trip
        $returnFlights = collect();
        $suggestedReturnFlights = collect();
        if ($tripType === 'round_trip' && $returnDate) {
            $returnFlights = $this->searchFlights($arrivalAirport, $departureAirport, $returnDate, $class, $passengers);
            if ($returnFlights->isEmpty()) {
                $suggestedReturnFlights = $this->findNearbyFlights($arrivalAirport, $departureAirport, $returnDate, $class, $passengers);
            }
        }

        // Smart ranking - prioritize by price, availability, and time
        $exactFlights = $this->rankFlights($exactFlights, $class);
        $suggestedFlights = $this->rankFlights($suggestedFlights, $class);

        return [
            'exact_flights' => $exactFlights,
            'suggested_flights' => $suggestedFlights,
            'return_flights' => $returnFlights,
            'suggested_return_flights' => $suggestedReturnFlights,
            'has_exact_results' => $exactFlights->isNotEmpty(),
            'search_params' => $params,
        ];
    }

    protected function searchFlights($departureId, $arrivalId, $date, $class, $passengers): Collection
    {
        if (!$departureId || !$arrivalId || !$date) return collect();

        return Flight::with(['airline', 'departureAirport', 'arrivalAirport', 'seats', 'provider'])
            ->where('departure_airport_id', $departureId)
            ->where('arrival_airport_id', $arrivalId)
            ->whereDate('departure_time', $date)
            ->where('status', 'scheduled')
            ->whereHas('seats', function ($q) use ($class, $passengers) {
                $q->where('class', $class)
                  ->where('status', 'available')
                  ->where('available_seats', '>=', $passengers);
            })
            ->orderBy('departure_time')
            ->get();
    }

    protected function findNearbyFlights($departureId, $arrivalId, $date, $class, $passengers, $rangeDays = 3): Collection
    {
        if (!$departureId || !$arrivalId || !$date) return collect();

        $dateObj = Carbon::parse($date);

        return Flight::with(['airline', 'departureAirport', 'arrivalAirport', 'seats', 'provider'])
            ->where('departure_airport_id', $departureId)
            ->where('arrival_airport_id', $arrivalId)
            ->whereBetween('departure_time', [
                $dateObj->copy()->subDays($rangeDays)->startOfDay(),
                $dateObj->copy()->addDays($rangeDays)->endOfDay(),
            ])
            ->where('status', 'scheduled')
            ->whereHas('seats', function ($q) use ($class, $passengers) {
                $q->where('class', $class)
                  ->where('status', 'available')
                  ->where('available_seats', '>=', $passengers);
            })
            ->orderBy('departure_time')
            ->get();
    }

    protected function rankFlights(Collection $flights, string $class): Collection
    {
        return $flights->sortBy(function ($flight) use ($class) {
            $seat = $flight->seats->where('class', $class)->first();
            $price = $seat ? $seat->adult_price : PHP_INT_MAX;
            $availability = $seat ? $seat->available_seats : 0;

            // Score: lower is better (prioritize price, then availability)
            return $price - ($availability * 0.1);
        })->values();
    }

    /**
     * Get popular routes based on booking history
     */
    public function getPopularRoutes(int $limit = 6): Collection
    {
        return Flight::with(['airline', 'departureAirport', 'arrivalAirport'])
            ->where('status', 'scheduled')
            ->where('departure_time', '>', now())
            ->whereHas('seats', function ($q) {
                $q->where('status', 'available')->where('available_seats', '>', 0);
            })
            ->orderBy('departure_time')
            ->limit($limit)
            ->get();
    }

    /**
     * Auto-complete airport search
     */
    public function searchAirports(string $query): Collection
    {
        return \App\Models\Airport::where('status', 'active')
            ->where(function ($q) use ($query) {
                $q->where('name_en', 'LIKE', "%{$query}%")
                  ->orWhere('name_ar', 'LIKE', "%{$query}%")
                  ->orWhere('city_en', 'LIKE', "%{$query}%")
                  ->orWhere('city_ar', 'LIKE', "%{$query}%")
                  ->orWhere('code', 'LIKE', "%{$query}%")
                  ->orWhere('iata_code', 'LIKE', "%{$query}%");
            })
            ->limit(10)
            ->get();
    }
}
