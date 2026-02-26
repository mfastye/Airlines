<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Flight;
use App\Models\FlightSeat;
use App\Models\Provider;
use App\Models\FinancialAccount;

class DashboardController extends Controller
{
    public function index()
    {
        $provider = Provider::find(auth()->user()->provider_id);
        if (!$provider) abort(403);

        $stats = [
            'total_seats' => FlightSeat::where('provider_id', $provider->id)->sum('total_seats'),
            'available_seats' => FlightSeat::where('provider_id', $provider->id)->sum('available_seats'),
            'total_bookings' => Booking::where('provider_id', $provider->id)->count(),
            'pending_bookings' => Booking::where('provider_id', $provider->id)->where('status', 'pending')->count(),
            'confirmed_bookings' => Booking::where('provider_id', $provider->id)->where('status', 'confirmed')->count(),
            'total_agents' => $provider->agents()->count(),
            'balance' => $provider->financialAccount?->balance ?? 0,
            'total_revenue' => Booking::where('provider_id', $provider->id)->where('status', 'confirmed')->sum('net_price'),
        ];

        $recentBookings = Booking::with(['customer', 'flight.airline'])
            ->where('provider_id', $provider->id)
            ->latest()->limit(10)->get();

        $account = $provider->financialAccount;
        $recentTransactions = $account ? $account->transactions()->latest()->limit(10)->get() : collect();

        return view('provider.dashboard', compact('provider', 'stats', 'recentBookings', 'recentTransactions'));
    }
}
