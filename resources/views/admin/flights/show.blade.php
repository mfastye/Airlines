@extends('layouts.admin')
@section('title', __('flight_details'))
@section('content')
<div class="max-w-4xl">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.flights.index') }}" class="text-gray-500 hover:text-primary-600 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"><i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i></a>
        <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ $flight->flight_number }}</h2>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div><div class="text-xs text-gray-500 uppercase">{{ __('airline') }}</div><div class="font-semibold dark:text-white">{{ app()->getLocale() === 'ar' ? ($flight->airline->name_ar ?? '') : ($flight->airline->name_en ?? '') }}</div></div>
            <div><div class="text-xs text-gray-500 uppercase">{{ __('route') }}</div><div class="font-semibold dark:text-white">{{ $flight->departureAirport->code ?? '' }} → {{ $flight->arrivalAirport->code ?? '' }}</div></div>
            <div><div class="text-xs text-gray-500 uppercase">{{ __('departure_date') }}</div><div class="font-semibold dark:text-white">{{ \Carbon\Carbon::parse($flight->departure_time)->format('d M Y H:i') }}</div></div>
            <div><div class="text-xs text-gray-500 uppercase">{{ __('status') }}</div><div><span class="px-2 py-1 rounded-full text-xs font-medium {{ $flight->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ __($flight->status) }}</span></div></div>
        </div>
    </div>
    @if($flight->seats && $flight->seats->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
        <h3 class="font-semibold dark:text-white mb-4">{{ app()->getLocale() === 'ar' ? 'المقاعد' : 'Seats' }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($flight->seats as $seat)
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                <div class="font-semibold dark:text-white mb-2">{{ __(ucfirst($seat->class)) }}</div>
                <div class="text-sm space-y-1 text-gray-600 dark:text-gray-400">
                    <div class="flex justify-between"><span>{{ app()->getLocale() === 'ar' ? 'إجمالي' : 'Total' }}</span><span class="font-medium">{{ $seat->total_seats }}</span></div>
                    <div class="flex justify-between"><span>{{ app()->getLocale() === 'ar' ? 'متاح' : 'Available' }}</span><span class="font-medium text-green-600">{{ $seat->available_seats }}</span></div>
                    <div class="flex justify-between"><span>{{ __('price') }}</span><span class="font-medium text-primary-600">${{ number_format($seat->price, 2) }}</span></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    @if($flight->bookings && $flight->bookings->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <h3 class="font-semibold dark:text-white mb-4">{{ __('manage_bookings') }}</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700"><tr><th class="px-3 py-2 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}">{{ __('booking_reference') }}</th><th class="px-3 py-2 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}">{{ app()->getLocale() === 'ar' ? 'العميل' : 'Customer' }}</th><th class="px-3 py-2 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}">{{ __('status') }}</th><th class="px-3 py-2 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}">{{ __('total_price') }}</th></tr></thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($flight->bookings as $booking)
                    <tr><td class="px-3 py-2 font-mono text-primary-600">{{ $booking->booking_reference }}</td><td class="px-3 py-2 dark:text-gray-300">{{ $booking->customer->name ?? '' }}</td><td class="px-3 py-2"><span class="px-2 py-0.5 rounded-full text-xs {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">{{ __($booking->status) }}</span></td><td class="px-3 py-2 font-semibold">${{ number_format($booking->total_price, 2) }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
