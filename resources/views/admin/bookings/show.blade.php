@extends('layouts.admin')
@section('title', __('booking_details'))
@section('content')
<div class="max-w-4xl">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center">
            <a href="{{ route('admin.bookings.index') }}" class="text-gray-500 hover:text-primary-600 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"><i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i></a>
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ __('booking_details') }} - {{ $booking->booking_reference }}</h2>
        </div>
        <span class="px-3 py-1 rounded-full text-sm font-medium @if($booking->status === 'confirmed') bg-green-100 text-green-800 @elseif($booking->status === 'cancelled') bg-red-100 text-red-800 @else bg-yellow-100 text-yellow-800 @endif">{{ __($booking->status) }}</span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h3 class="font-semibold dark:text-white mb-4">{{ __('flight_details') }}</h3>
            @if($booking->flight)
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">{{ __('flight_number') }}</span><span class="font-medium dark:text-white">{{ $booking->flight->flight_number }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">{{ __('airline') }}</span><span class="font-medium dark:text-white">{{ app()->getLocale() === 'ar' ? ($booking->flight->airline->name_ar ?? '') : ($booking->flight->airline->name_en ?? '') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">{{ __('route') }}</span><span class="font-medium dark:text-white">{{ $booking->flight->departureAirport->code ?? '' }} → {{ $booking->flight->arrivalAirport->code ?? '' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">{{ __('departure_date') }}</span><span class="font-medium dark:text-white">{{ \Carbon\Carbon::parse($booking->flight->departure_time)->format('d M Y H:i') }}</span></div>
            </div>
            @endif
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h3 class="font-semibold dark:text-white mb-4">{{ app()->getLocale() === 'ar' ? 'معلومات الدفع' : 'Payment Info' }}</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">{{ __('total_price') }}</span><span class="font-bold text-primary-600 text-lg">${{ number_format($booking->total_price, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">{{ __('payment_status') }}</span><span class="px-2 py-0.5 rounded-full text-xs font-medium @if($booking->payment_status === 'paid') bg-green-100 text-green-800 @else bg-yellow-100 text-yellow-800 @endif">{{ __($booking->payment_status ?? 'pending') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">{{ app()->getLocale() === 'ar' ? 'تاريخ الحجز' : 'Booking Date' }}</span><span class="font-medium dark:text-white">{{ $booking->created_at->format('d M Y H:i') }}</span></div>
            </div>
        </div>
    </div>

    @if($booking->passengers && $booking->passengers->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
        <h3 class="font-semibold dark:text-white mb-4">{{ __('passenger_details') }}</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700"><tr><th class="px-3 py-2 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}">{{ __('name') }}</th><th class="px-3 py-2 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}">{{ __('passport_number') }}</th><th class="px-3 py-2 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}">{{ __('nationality') }}</th><th class="px-3 py-2 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}">{{ app()->getLocale() === 'ar' ? 'رقم التذكرة' : 'Ticket #' }}</th></tr></thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($booking->passengers as $p)
                    <tr><td class="px-3 py-2 dark:text-gray-300">{{ $p->first_name }} {{ $p->last_name }}</td><td class="px-3 py-2 font-mono dark:text-gray-300">{{ $p->passport_number }}</td><td class="px-3 py-2 dark:text-gray-300">{{ $p->nationality }}</td><td class="px-3 py-2 font-mono text-primary-600">{{ $p->ticket_number ?? '-' }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @if($booking->status === 'pending')
    <div class="flex space-x-4 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
        <form action="{{ route('admin.bookings.confirm', $booking) }}" method="POST" class="flex-1">@csrf<button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg transition"><i class="fas fa-check-circle {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ __('confirm') }}</button></form>
        <form action="{{ route('admin.bookings.cancel', $booking) }}" method="POST" class="flex-1" onsubmit="return confirm('{{ __('are_you_sure') }}')">@csrf<button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg transition"><i class="fas fa-times-circle {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ __('cancel') }}</button></form>
    </div>
    @endif
</div>
@endsection
