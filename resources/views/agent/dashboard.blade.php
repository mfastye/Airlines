@extends('layouts.admin')
@section('title', __('dashboard'))
@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between"><div><p class="text-sm text-gray-500">{{ __('balance') }}</p><p class="text-2xl font-bold text-gray-800 dark:text-white">${{ number_format($stats['balance'] ?? 0, 2) }}</p></div><div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center"><i class="fas fa-wallet text-green-600 text-xl"></i></div></div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between"><div><p class="text-sm text-gray-500">{{ __('total_bookings') }}</p><p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['total_bookings'] ?? 0 }}</p><p class="text-xs text-green-600"><i class="fas fa-calendar-day"></i> {{ $stats['today_bookings'] ?? 0 }} {{ __('today') }}</p></div><div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center"><i class="fas fa-ticket-alt text-blue-600 text-xl"></i></div></div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between"><div><p class="text-sm text-gray-500">{{ __('commission_balance') }}</p><p class="text-2xl font-bold text-accent-600">${{ number_format($stats['commission_balance'] ?? 0, 2) }}</p></div><div class="w-12 h-12 bg-accent-100 dark:bg-accent-900/30 rounded-xl flex items-center justify-center"><i class="fas fa-percentage text-accent-600 text-xl"></i></div></div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between"><div><p class="text-sm text-gray-500">{{ __('total_revenue') }}</p><p class="text-2xl font-bold text-gray-800 dark:text-white">${{ number_format($stats['total_revenue'] ?? 0, 2) }}</p></div><div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-xl flex items-center justify-center"><i class="fas fa-chart-line text-primary-600 text-xl"></i></div></div>
        </div>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-amber-50 dark:bg-amber-900/20 rounded-lg p-3 text-center border border-amber-200"><p class="text-xl font-bold text-amber-600">{{ $stats['pending_bookings'] ?? 0 }}</p><p class="text-xs text-amber-600">{{ __('pending') }}</p></div>
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 text-center border border-blue-200"><p class="text-xl font-bold text-blue-600">{{ $stats['confirmed_bookings'] ?? 0 }}</p><p class="text-xs text-blue-600">{{ __('confirmed') }}</p></div>
        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3 text-center border border-green-200"><p class="text-xl font-bold text-green-600">{{ $stats['issued_bookings'] ?? 0 }}</p><p class="text-xs text-green-600">{{ __('issued') }}</p></div>
        <div class="bg-primary-50 dark:bg-primary-900/20 rounded-lg p-3 text-center border border-primary-200"><p class="text-xl font-bold text-primary-600">${{ number_format($stats['balance'] ?? 0, 2) }}</p><p class="text-xs text-primary-600">{{ __('available_balance') }}</p></div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4"><i class="fas fa-search {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }} text-primary-600"></i>{{ __('search_flights') }}</h3>
        <form action="{{ route('agent.search') }}" method="POST">@csrf
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('from') }}</label><select name="departure_airport_id" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm"><option value="">{{ __('select_airport') }}</option>@foreach($airports ?? [] as $a)<option value="{{ $a->id }}">{{ $a->code }} - {{ app()->getLocale() === 'ar' ? $a->city_ar : $a->city_en }}</option>@endforeach</select></div>
                <div><label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('to') }}</label><select name="arrival_airport_id" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm"><option value="">{{ __('select_airport') }}</option>@foreach($airports ?? [] as $a)<option value="{{ $a->id }}">{{ $a->code }} - {{ app()->getLocale() === 'ar' ? $a->city_ar : $a->city_en }}</option>@endforeach</select></div>
                <div><label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('date') }}</label><input type="date" name="departure_date" required min="{{ date('Y-m-d') }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm"></div>
                <div><label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('passengers') }}</label><input type="number" name="adults" value="1" min="1" max="9" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm"></div>
                <div><label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('class') }}</label><select name="class" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm"><option value="economy">{{ __('economy') }}</option><option value="business">{{ __('business') }}</option><option value="first">{{ __('first_class') }}</option></select></div>
                <div class="flex items-end"><button type="submit" class="w-full bg-primary-600 text-white rounded-lg px-4 py-2.5 hover:bg-primary-700 text-sm"><i class="fas fa-search {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ __('search') }}</button></div>
            </div>
        </form>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center"><h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ __('recent_bookings') }}</h3><a href="{{ route('agent.bookings') }}" class="text-sm text-primary-600 hover:text-primary-700">{{ __('view_all') }}</a></div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse(($recentBookings ?? collect())->take(5) as $booking)
                <a href="{{ route('agent.booking.show', $booking) }}" class="block p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <div class="flex items-center justify-between">
                        <div><p class="text-sm font-medium text-gray-800 dark:text-white">{{ $booking->booking_reference }}</p><p class="text-xs text-gray-500">{{ optional($booking->customer)->first_name }} {{ optional($booking->customer)->last_name }}</p><p class="text-xs text-gray-400">{{ optional(optional($booking->flight)->departureAirport)->code }} &rarr; {{ optional(optional($booking->flight)->arrivalAirport)->code }}</p></div>
                        <div class="text-end"><span class="px-2 py-1 text-xs rounded-full {{ $booking->status === 'issued' ? 'bg-green-100 text-green-700' : ($booking->status === 'confirmed' ? 'bg-blue-100 text-blue-700' : ($booking->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700')) }}">{{ ucfirst($booking->status) }}</span><p class="text-xs text-gray-500 mt-1">${{ number_format($booking->total_price, 2) }}</p></div>
                    </div>
                </a>
                @empty
                <p class="text-sm text-gray-500 text-center py-4">{{ __('no_bookings') }}</p>
                @endforelse
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center"><h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ __('recent_transactions') }}</h3><a href="{{ route('agent.financial') }}" class="text-sm text-primary-600 hover:text-primary-700">{{ __('view_all') }}</a></div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse(($recentTransactions ?? collect())->take(5) as $t)
                <div class="p-4 flex items-center justify-between"><div><p class="text-sm text-gray-800 dark:text-white">{{ app()->getLocale() === 'ar' ? ($t->description_ar ?? $t->description_en) : $t->description_en }}</p><p class="text-xs text-gray-500">{{ $t->created_at->format('Y-m-d H:i') }}</p></div><span class="text-sm font-semibold {{ $t->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">{{ $t->type === 'credit' ? '+' : '-' }}${{ number_format($t->amount, 2) }}</span></div>
                @empty
                <p class="text-sm text-gray-500 text-center py-4">{{ __('no_transactions') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
