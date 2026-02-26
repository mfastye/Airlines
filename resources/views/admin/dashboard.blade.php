@extends('layouts.admin')
@section('title', __('dashboard'))
@section('content')
{{-- Stats Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 card-hover">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('total_bookings') }}</div>
                <div class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ number_format($totalBookings ?? 0) }}</div>
            </div>
            <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900 rounded-xl flex items-center justify-center">
                <i class="fas fa-ticket-alt text-primary-600 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 card-hover">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('total_revenue') }}</div>
                <div class="text-3xl font-bold text-gray-800 dark:text-white mt-1">${{ number_format($totalRevenue ?? 0, 2) }}</div>
            </div>
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-xl flex items-center justify-center">
                <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 card-hover">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('total_flights') }}</div>
                <div class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ number_format($totalFlights ?? 0) }}</div>
            </div>
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-xl flex items-center justify-center">
                <i class="fas fa-plane text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 card-hover">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('pending_payments') }}</div>
                <div class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ number_format($pendingPayments ?? 0) }}</div>
            </div>
            <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-xl flex items-center justify-center">
                <i class="fas fa-clock text-yellow-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Recent Bookings --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
            <i class="fas fa-ticket-alt text-primary-600 {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ __('recent_bookings') }}
        </h3>
        <div class="space-y-3">
            @forelse(($recentBookings ?? []) as $booking)
            <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700 last:border-0">
                <div>
                    <div class="font-medium text-gray-800 dark:text-white text-sm">{{ $booking->booking_reference }}</div>
                    <div class="text-xs text-gray-500">{{ $booking->flight->flight_number ?? '' }} - {{ $booking->created_at->format('d M Y') }}</div>
                </div>
                <div class="flex items-center space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                    <span class="text-sm font-semibold text-primary-600">${{ number_format($booking->total_price, 2) }}</span>
                    <span class="text-xs px-2 py-1 rounded-full
                        @if($booking->status === 'confirmed') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                        @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                        @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400 @endif">
                        {{ __($booking->status) }}
                    </span>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-500 text-center py-4">{{ __('no_data') }}</p>
            @endforelse
        </div>
        <a href="{{ route('admin.bookings.index') }}" class="block text-center text-sm text-primary-600 hover:text-primary-700 mt-4 font-medium">
            {{ app()->getLocale() === 'ar' ? 'عرض الكل' : 'View All' }} <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
        </a>
    </div>

    {{-- Quick Stats --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
            <i class="fas fa-chart-bar text-primary-600 {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ app()->getLocale() === 'ar' ? 'إحصائيات سريعة' : 'Quick Stats' }}
        </h3>
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('total_providers') }}</span>
                <span class="font-bold text-gray-800 dark:text-white">{{ $totalProviders ?? 0 }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('total_agents') }}</span>
                <span class="font-bold text-gray-800 dark:text-white">{{ $totalAgents ?? 0 }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('total_customers') }}</span>
                <span class="font-bold text-gray-800 dark:text-white">{{ $totalCustomers ?? 0 }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ app()->getLocale() === 'ar' ? 'شركات الطيران' : 'Airlines' }}</span>
                <span class="font-bold text-gray-800 dark:text-white">{{ $totalAirlines ?? 0 }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ app()->getLocale() === 'ar' ? 'المطارات' : 'Airports' }}</span>
                <span class="font-bold text-gray-800 dark:text-white">{{ $totalAirports ?? 0 }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
