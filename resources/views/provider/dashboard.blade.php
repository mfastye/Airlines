@extends('layouts.admin')
@section('title', __('dashboard'))

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('total_flights') }}</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $stats['total_flights'] ?? 0 }}</p>
                    <p class="text-xs text-green-600 mt-1"><i class="fas fa-check-circle"></i> {{ $stats['active_flights'] ?? 0 }} {{ __('active') }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-plane text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('total_seats') }}</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $stats['total_seats'] ?? 0 }}</p>
                    <p class="text-xs text-green-600 mt-1"><i class="fas fa-chair"></i> {{ $stats['available_seats'] ?? 0 }} {{ __('available') }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chair text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('total_bookings') }}</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $stats['total_bookings'] ?? 0 }}</p>
                    <p class="text-xs text-amber-600 mt-1"><i class="fas fa-clock"></i> {{ $stats['pending_bookings'] ?? 0 }} {{ __('pending') }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-ticket-alt text-amber-600 dark:text-amber-400 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('balance') }}</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">${{ number_format($stats['balance'] ?? 0, 2) }}</p>
                    <p class="text-xs text-primary-600 mt-1"><i class="fas fa-dollar-sign"></i> ${{ number_format($stats['total_revenue'] ?? 0, 2) }} {{ __('revenue') }}</p>
                </div>
                <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-wallet text-primary-600 dark:text-primary-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700 flex items-center space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center"><i class="fas fa-check text-green-600 text-sm"></i></div>
            <div><p class="text-lg font-bold text-gray-800 dark:text-white">{{ $stats['confirmed_bookings'] ?? 0 }}</p><p class="text-xs text-gray-500">{{ __('confirmed') }}</p></div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700 flex items-center space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center"><i class="fas fa-file-alt text-blue-600 text-sm"></i></div>
            <div><p class="text-lg font-bold text-gray-800 dark:text-white">{{ $stats['issued_bookings'] ?? 0 }}</p><p class="text-xs text-gray-500">{{ __('issued') }}</p></div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700 flex items-center space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center"><i class="fas fa-times text-red-600 text-sm"></i></div>
            <div><p class="text-lg font-bold text-gray-800 dark:text-white">{{ $stats['cancelled_bookings'] ?? 0 }}</p><p class="text-xs text-gray-500">{{ __('cancelled') }}</p></div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700 flex items-center space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center"><i class="fas fa-user-tie text-purple-600 text-sm"></i></div>
            <div><p class="text-lg font-bold text-gray-800 dark:text-white">{{ $stats['total_agents'] ?? 0 }}</p><p class="text-xs text-gray-500">{{ __('agents') }}</p></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">{{ __('monthly_statistics') }}</h3>
            <div class="space-y-3">
                @foreach($monthlyData ?? [] as $month)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400 w-20">{{ $month['month'] }}</span>
                    <div class="flex-1 mx-3">
                        <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-4 relative">
                            @php $maxB = collect($monthlyData)->max('bookings') ?: 1; @endphp
                            <div class="bg-primary-600 rounded-full h-4" style="width: {{ ($month['bookings'] / $maxB) * 100 }}%"></div>
                        </div>
                    </div>
                    <span class="text-sm font-medium text-gray-800 dark:text-white w-24 text-end">{{ $month['bookings'] }} {{ __('bookings') }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">{{ __('upcoming_flights') }}</h3>
            @forelse($upcomingFlights ?? [] as $flight)
            <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700 last:border-0">
                <div class="flex items-center space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center"><i class="fas fa-plane text-blue-600 text-sm"></i></div>
                    <div>
                        <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $flight->flight_number }}</p>
                        <p class="text-xs text-gray-500">{{ optional($flight->departureAirport)->code }} &rarr; {{ optional($flight->arrivalAirport)->code }}</p>
                    </div>
                </div>
                <div class="text-end">
                    <p class="text-sm text-gray-800 dark:text-white">{{ $flight->departure_time ? $flight->departure_time->format('M d') : '-' }}</p>
                    <p class="text-xs text-gray-500">{{ $flight->departure_time ? $flight->departure_time->format('H:i') : '' }}</p>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-500 text-center py-4">{{ __('no_upcoming_flights') }}</p>
            @endforelse
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ __('recent_bookings') }}</h3>
                <a href="{{ route('provider.bookings.index') }}" class="text-sm text-primary-600 hover:text-primary-700">{{ __('view_all') }}</a>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse(($recentBookings ?? collect())->take(5) as $booking)
                <div class="p-4 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $booking->booking_reference }}</p>
                        <p class="text-xs text-gray-500">{{ optional($booking->customer)->first_name }} {{ optional($booking->customer)->last_name }}</p>
                    </div>
                    <div class="text-end">
                        <span class="px-2 py-1 text-xs rounded-full {{ $booking->status === 'issued' ? 'bg-green-100 text-green-700' : ($booking->status === 'confirmed' ? 'bg-blue-100 text-blue-700' : ($booking->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700')) }}">{{ ucfirst($booking->status) }}</span>
                        <p class="text-xs text-gray-500 mt-1">${{ number_format($booking->total_price, 2) }}</p>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-500 text-center py-4">{{ __('no_bookings') }}</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ __('recent_transactions') }}</h3>
                <a href="{{ route('provider.financial') }}" class="text-sm text-primary-600 hover:text-primary-700">{{ __('view_all') }}</a>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse(($recentTransactions ?? collect())->take(5) as $t)
                <div class="p-4 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-800 dark:text-white">{{ app()->getLocale() === 'ar' ? ($t->description_ar ?? $t->description_en) : $t->description_en }}</p>
                        <p class="text-xs text-gray-500">{{ $t->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                    <span class="text-sm font-semibold {{ $t->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">{{ $t->type === 'credit' ? '+' : '-' }}${{ number_format($t->amount, 2) }}</span>
                </div>
                @empty
                <p class="text-sm text-gray-500 text-center py-4">{{ __('no_transactions') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
