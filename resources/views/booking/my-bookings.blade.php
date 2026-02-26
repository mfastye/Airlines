@extends('layouts.app')
@section('title', __('my_bookings'))
@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">
        <i class="fas fa-ticket-alt text-primary-600 {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ __('my_bookings') }}
    </h1>

    @if(isset($bookings) && $bookings->count() > 0)
    <div class="space-y-4">
        @foreach($bookings as $booking)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden card-hover">
            <div class="flex flex-wrap items-center justify-between p-6 gap-4">
                <div>
                    <div class="text-sm text-gray-500 mb-1">{{ __('booking_reference') }}</div>
                    <div class="text-xl font-bold text-primary-600">{{ $booking->booking_reference }}</div>
                </div>
                @if($booking->flight)
                <div class="flex items-center space-x-4 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                    <div class="text-center">
                        <div class="font-bold dark:text-white">{{ $booking->flight->departureAirport->code ?? '' }}</div>
                        <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($booking->flight->departure_time)->format('H:i') }}</div>
                    </div>
                    <i class="fas fa-plane text-primary-600 text-sm {{ app()->getLocale() === 'ar' ? 'fa-flip-horizontal' : '' }}"></i>
                    <div class="text-center">
                        <div class="font-bold dark:text-white">{{ $booking->flight->arrivalAirport->code ?? '' }}</div>
                        <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($booking->flight->arrival_time)->format('H:i') }}</div>
                    </div>
                </div>
                @endif
                <div class="text-center">
                    <div class="text-xs text-gray-500">{{ __('date') }}</div>
                    <div class="text-sm font-medium dark:text-white">{{ $booking->flight ? \Carbon\Carbon::parse($booking->flight->departure_time)->format('d M Y') : '' }}</div>
                </div>
                <div class="text-center">
                    <div class="text-xs text-gray-500">{{ __('total_price') }}</div>
                    <div class="text-lg font-bold text-primary-600">${{ number_format($booking->total_price, 2) }}</div>
                </div>
                <div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                        @if($booking->status === 'confirmed') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                        @elseif($booking->status === 'cancelled') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                        @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400 @endif">
                        {{ __($booking->status) }}
                    </span>
                </div>
                <a href="{{ route('booking.confirmation', $booking) }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                    {{ __('view') }} <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
                </a>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-12 text-center">
        <i class="fas fa-ticket-alt text-gray-300 dark:text-gray-600 text-5xl mb-4"></i>
        <p class="text-gray-500 dark:text-gray-400">{{ __('no_data') }}</p>
        <a href="{{ route('home') }}" class="inline-block mt-4 bg-primary-600 hover:bg-primary-700 text-white px-6 py-2 rounded-lg transition text-sm">{{ __('search_flights') }}</a>
    </div>
    @endif
</div>
@endsection
