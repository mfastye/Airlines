<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Flight;
use App\Models\Provider;
use App\Models\Agent;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\FinancialAccount;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_bookings' => Booking::count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'confirmed_bookings' => Booking::where('status', 'confirmed')->count(),
            'total_flights' => Flight::where('status', 'scheduled')->count(),
            'total_providers' => Provider::where('status', 'active')->count(),
            'total_agents' => Agent::where('status', 'active')->count(),
            'total_customers' => Customer::count(),
            'total_revenue' => Payment::where('status', 'confirmed')->sum('amount'),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'system_commission' => FinancialAccount::where('account_type', 'commission')->sum('balance'),
        ];

        $recentBookings = Booking::with(['customer', 'flight.airline', 'flight.departureAirport', 'flight.arrivalAirport'])
            ->latest()->limit(10)->get();

        $recentPayments = Payment::with(['booking.customer', 'gateway'])
            ->latest()->limit(10)->get();

        return view('admin.dashboard', compact('stats', 'recentBookings', 'recentPayments'));
    }
}
