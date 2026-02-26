@extends('layouts.admin')
@section('title', __('booking_details'))
@section('content')
<div class="max-w-4xl">
    <div class="flex items-center mb-6">
        <a href="{{ route('provider.bookings.index') }}" class="text-gray-500 hover:text-primary-600 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"><i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i></a>
        <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ __('booking_details') }} - {{ $booking->booking_reference }}</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 text-center"><div class="text-xs text-gray-500">{{ __('total_price') }}</div><div class="text-xl font-bold text-primary-600">${{ number_format($booking->total_price, 2) }}</div></div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 text-center"><div class="text-xs text-gray-500">{{ __('payment_status') }}</div><div class="text-xl font-bold"><span class="px-2 py-1 rounded-full text-xs {{ $booking->payment_status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">{{ __($booking->payment_status) }}</span></div></div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 text-center"><div class="text-xs text-gray-500">{{ __('booking_status') }}</div><div class="text-xl font-bold"><span class="px-2 py-1 rounded-full text-xs {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">{{ __($booking->status) }}</span></div></div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
        <h3 class="font-semibold dark:text-white mb-3">{{ app()->getLocale() === 'ar' ? 'بيانات الرحلة' : 'Flight Details' }}</h3>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><span class="text-gray-500">{{ __('flight_number') }}:</span> <span class="font-mono font-bold dark:text-white">{{ $booking->flight->flight_number ?? '-' }}</span></div>
            <div><span class="text-gray-500">{{ __('airline') }}:</span> <span class="dark:text-white">{{ app()->getLocale() === 'ar' ? ($booking->flight->airline->name_ar ?? '') : ($booking->flight->airline->name_en ?? '') }}</span></div>
            <div><span class="text-gray-500">{{ __('departure_airport') }}:</span> <span class="dark:text-white">{{ $booking->flight->departureAirport->code ?? '' }}</span></div>
            <div><span class="text-gray-500">{{ __('arrival_airport') }}:</span> <span class="dark:text-white">{{ $booking->flight->arrivalAirport->code ?? '' }}</span></div>
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
    @if($booking->status === 'pending')
    <div class="flex space-x-4 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
        <form action="{{ route('provider.bookings.confirm', $booking) }}" method="POST" class="flex-1">@csrf<button class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg transition"><i class="fas fa-check {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ app()->getLocale() === 'ar' ? 'تأكيد الحجز' : 'Confirm Booking' }}</button></form>
        <form action="{{ route('provider.bookings.cancel', $booking) }}" method="POST" class="flex-1" onsubmit="return confirm('{{ __('are_you_sure') }}')">@csrf<button class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg transition"><i class="fas fa-times {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ app()->getLocale() === 'ar' ? 'إلغاء الحجز' : 'Cancel Booking' }}</button></form>
    </div>
    @endif
    @if($booking->status === 'confirmed' && !$booking->ticket_image)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <h3 class="font-semibold dark:text-white mb-3">{{ app()->getLocale() === 'ar' ? 'إرفاق التذكرة' : 'Upload Ticket' }}</h3>
        <form action="{{ route('provider.bookings.upload-ticket', $booking) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="ticket_image" accept="image/*,.pdf" required class="w-full text-sm mb-3 file:bg-primary-600 file:text-white file:border-0 file:rounded-lg file:px-4 file:py-2 file:cursor-pointer">
            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2.5 px-6 rounded-lg transition"><i class="fas fa-upload {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ app()->getLocale() === 'ar' ? 'رفع التذكرة' : 'Upload' }}</button>
        </form>
    </div>
    @endif
</div>
@endsection
