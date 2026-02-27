<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Flight;
use App\Models\FlightSeat;
use App\Models\Provider;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected function getProvider(): Provider
    {
        $provider = Provider::find(auth()->user()->provider_id);
        if (!$provider) abort(403);
        return $provider;
    }

    public function index()
    {
        $provider = $this->getProvider();

        $stats = [
            'total_flights' => Flight::where('provider_id', $provider->id)->count(),
            'active_flights' => Flight::where('provider_id', $provider->id)->where('status', 'active')->orWhere('status', 'scheduled')->where('provider_id', $provider->id)->count(),
            'total_seats' => FlightSeat::whereHas('flight', fn($q) => $q->where('provider_id', $provider->id))->sum('total_seats'),
            'available_seats' => FlightSeat::whereHas('flight', fn($q) => $q->where('provider_id', $provider->id))->sum('available_seats'),
            'total_bookings' => Booking::where('provider_id', $provider->id)->count(),
            'pending_bookings' => Booking::where('provider_id', $provider->id)->where('status', 'pending')->count(),
            'confirmed_bookings' => Booking::where('provider_id', $provider->id)->where('status', 'confirmed')->count(),
            'issued_bookings' => Booking::where('provider_id', $provider->id)->where('status', 'issued')->count(),
            'cancelled_bookings' => Booking::where('provider_id', $provider->id)->where('status', 'cancelled')->count(),
            'today_bookings' => Booking::where('provider_id', $provider->id)->whereDate('created_at', today())->count(),
            'balance' => $provider->balance ?? 0,
            'total_revenue' => Booking::where('provider_id', $provider->id)->whereIn('status', ['confirmed', 'issued'])->sum('net_price'),
            'total_commissions' => Booking::where('provider_id', $provider->id)->whereIn('status', ['confirmed', 'issued'])->sum('commission_amount'),
            'total_agents' => $provider->agents()->count(),
        ];

        $recentBookings = Booking::with(['customer', 'flight.airline', 'agent'])
            ->where('provider_id', $provider->id)
            ->latest()->limit(10)->get();

        $account = $provider->financialAccount;
        $recentTransactions = $account ? $account->transactions()->latest()->limit(10)->get() : collect();

        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthlyData[] = [
                'month' => $month->format('M Y'),
                'bookings' => Booking::where('provider_id', $provider->id)
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)->count(),
                'revenue' => (float)Booking::where('provider_id', $provider->id)
                    ->whereIn('status', ['confirmed', 'issued'])
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)->sum('net_price'),
            ];
        }

        $upcomingFlights = Flight::with(['airline', 'departureAirport', 'arrivalAirport'])
            ->where('provider_id', $provider->id)
            ->where('departure_time', '>=', now())
            ->whereIn('status', ['active', 'scheduled'])
            ->orderBy('departure_time')
            ->limit(5)->get();

        return view('provider.dashboard', compact('provider', 'stats', 'recentBookings', 'recentTransactions', 'monthlyData', 'upcomingFlights'));
    }
}
