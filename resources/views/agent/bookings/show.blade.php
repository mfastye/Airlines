@extends('layouts.admin')
@section('title', __('booking_details'))
@section('content')
<div class="max-w-4xl">
    <div class="flex items-center mb-6">
        <a href="{{ route('agent.bookings.index') }}" class="text-gray-500 hover:text-primary-600 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"><i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i></a>
        <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ __('booking_details') }} - {{ $booking->booking_reference }}</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 text-center"><div class="text-xs text-gray-500">{{ __('total_price') }}</div><div class="text-xl font-bold text-primary-600">${{ number_format($booking->total_price, 2) }}</div></div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 text-center"><div class="text-xs text-gray-500">{{ __('payment_status') }}</div><span class="px-2 py-1 rounded-full text-xs {{ $booking->payment_status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">{{ __($booking->payment_status) }}</span></div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 text-center"><div class="text-xs text-gray-500">{{ __('booking_status') }}</div><span class="px-2 py-1 rounded-full text-xs {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">{{ __($booking->status) }}</span></div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
        <h3 class="font-semibold dark:text-white mb-3">{{ app()->getLocale() === 'ar' ? 'بيانات الرحلة' : 'Flight Details' }}</h3>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><span class="text-gray-500">{{ __('flight_number') }}:</span> <span class="font-mono font-bold dark:text-white">{{ $booking->flight->flight_number ?? '-' }}</span></div>
            <div><span class="text-gray-500">{{ __('airline') }}:</span> <span class="dark:text-white">{{ app()->getLocale() === 'ar' ? ($booking->flight->airline->name_ar ?? '') : ($booking->flight->airline->name_en ?? '') }}</span></div>
            <div><span class="text-gray-500">{{ __('departure_airport') }}:</span> <span class="dark:text-white">{{ $booking->flight->departureAirport->code ?? '' }} - {{ app()->getLocale() === 'ar' ? ($booking->flight->departureAirport->name_ar ?? '') : ($booking->flight->departureAirport->name_en ?? '') }}</span></div>
            <div><span class="text-gray-500">{{ __('arrival_airport') }}:</span> <span class="dark:text-white">{{ $booking->flight->arrivalAirport->code ?? '' }} - {{ app()->getLocale() === 'ar' ? ($booking->flight->arrivalAirport->name_ar ?? '') : ($booking->flight->arrivalAirport->name_en ?? '') }}</span></div>
        </div>
    </div>
    @if($booking->passengers && $booking->passengers->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700"><h3 class="font-semibold dark:text-white">{{ __('passengers') }}</h3></div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700"><tr><th class="px-4 py-2 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}">{{ __('name') }}</th><th class="px-4 py-2 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}">{{ app()->getLocale() === 'ar' ? 'رقم الجواز' : 'Passport' }}</th><th class="px-4 py-2 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}">{{ app()->getLocale() === 'ar' ? 'رقم التذكرة' : 'Ticket #' }}</th></tr></thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($booking->passengers as $p)
                <tr><td class="px-4 py-2 dark:text-white">{{ $p->full_name }}</td><td class="px-4 py-2 font-mono text-xs dark:text-gray-300">{{ $p->passport_number }}</td><td class="px-4 py-2 font-mono text-xs text-primary-600">{{ $p->ticket_number ?? '-' }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
