@extends('layouts.admin')
@section('title', app()->getLocale() === 'ar' ? 'لوحة تحكم المزود' : 'Provider Dashboard')
@section('content')
<h2 class="text-xl font-bold text-gray-800 dark:text-white mb-6">{{ app()->getLocale() === 'ar' ? 'لوحة تحكم المزود' : 'Provider Dashboard' }}</h2>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6"><div class="flex items-center justify-between"><div><p class="text-sm text-gray-500">{{ app()->getLocale() === 'ar' ? 'الرصيد' : 'Balance' }}</p><p class="text-2xl font-bold text-primary-600">${{ number_format($balance ?? 0, 2) }}</p></div><div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center"><i class="fas fa-wallet text-primary-600 text-xl"></i></div></div></div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6"><div class="flex items-center justify-between"><div><p class="text-sm text-gray-500">{{ app()->getLocale() === 'ar' ? 'الرحلات' : 'Flights' }}</p><p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalFlights ?? 0 }}</p></div><div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center"><i class="fas fa-plane text-blue-600 text-xl"></i></div></div></div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6"><div class="flex items-center justify-between"><div><p class="text-sm text-gray-500">{{ app()->getLocale() === 'ar' ? 'الحجوزات' : 'Bookings' }}</p><p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalBookings ?? 0 }}</p></div><div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center"><i class="fas fa-ticket-alt text-green-600 text-xl"></i></div></div></div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6"><div class="flex items-center justify-between"><div><p class="text-sm text-gray-500">{{ app()->getLocale() === 'ar' ? 'الوكلاء' : 'Agents' }}</p><p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalAgents ?? 0 }}</p></div><div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center"><i class="fas fa-users text-yellow-600 text-xl"></i></div></div></div>
</div>
@if(isset($recentBookings) && $recentBookings->count() > 0)
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
    <div class="p-4 border-b border-gray-200 dark:border-gray-700"><h3 class="font-semibold dark:text-white">{{ app()->getLocale() === 'ar' ? 'آخر الحجوزات' : 'Recent Bookings' }}</h3></div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700"><tr><th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}">{{ __('booking_reference') }}</th><th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}">{{ __('flight_number') }}</th><th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}">{{ __('total_price') }}</th><th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}">{{ __('status') }}</th></tr></thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($recentBookings as $booking)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50"><td class="px-4 py-3 font-mono text-primary-600">{{ $booking->booking_reference }}</td><td class="px-4 py-3 dark:text-gray-300">{{ $booking->flight->flight_number ?? '-' }}</td><td class="px-4 py-3 font-semibold dark:text-white">${{ number_format($booking->total_price, 2) }}</td><td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full text-xs {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">{{ __($booking->status) }}</span></td></tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
