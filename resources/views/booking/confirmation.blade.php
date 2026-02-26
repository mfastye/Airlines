@extends('layouts.app')
@section('title', __('booking_confirmation'))
@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden animate-fade-in">
        {{-- Header --}}
        <div class="gradient-primary p-8 text-center">
            <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check-circle text-accent-400 text-4xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-white mb-2">{{ __('booking_confirmation') }}</h1>
            <p class="text-gray-300">{{ app()->getLocale() === 'ar' ? 'تم إنشاء حجزك بنجاح' : 'Your booking has been created successfully' }}</p>
        </div>

        <div class="p-8">
            {{-- Booking Reference --}}
            <div class="text-center mb-8">
                <div class="text-sm text-gray-500 mb-1">{{ __('booking_reference') }}</div>
                <div class="text-3xl font-bold text-primary-600 tracking-wider">{{ $booking->booking_reference ?? '' }}</div>
                <div class="mt-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' }}">
                        <i class="fas {{ $booking->status === 'confirmed' ? 'fa-check-circle' : 'fa-clock' }} {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                        {{ __($booking->status ?? 'pending') }}
                    </span>
                </div>
            </div>

            {{-- Flight Details --}}
            @if($booking->flight)
            <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-6 mb-6">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-4">{{ __('flight_details') }}</h3>
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <div class="text-lg font-bold text-gray-800 dark:text-white">{{ $booking->flight->flight_number }}</div>
                        <div class="text-sm text-gray-500">{{ app()->getLocale() === 'ar' ? ($booking->flight->airline->name_ar ?? '') : ($booking->flight->airline->name_en ?? '') }}</div>
                    </div>
                    <div class="flex items-center space-x-4 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                        <div class="text-center">
                            <div class="text-xl font-bold dark:text-white">{{ $booking->flight->departureAirport->code ?? '' }}</div>
                            <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($booking->flight->departure_time)->format('H:i') }}</div>
                        </div>
                        <i class="fas fa-plane text-primary-600 {{ app()->getLocale() === 'ar' ? 'fa-flip-horizontal' : '' }}"></i>
                        <div class="text-center">
                            <div class="text-xl font-bold dark:text-white">{{ $booking->flight->arrivalAirport->code ?? '' }}</div>
                            <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($booking->flight->arrival_time)->format('H:i') }}</div>
                        </div>
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        <i class="fas fa-calendar {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ \Carbon\Carbon::parse($booking->flight->departure_time)->format('D, d M Y') }}
                    </div>
                </div>
            </div>
            @endif

            {{-- Passengers --}}
            @if($booking->passengers && $booking->passengers->count() > 0)
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-3">{{ __('passenger_details') }}</h3>
                @foreach($booking->passengers as $passenger)
                <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700 last:border-0">
                    <div class="flex items-center space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                        <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-primary-600 text-xs"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-800 dark:text-white text-sm">{{ $passenger->first_name }} {{ $passenger->last_name }}</div>
                            <div class="text-xs text-gray-500">{{ $passenger->passport_number }}</div>
                        </div>
                    </div>
                    @if($passenger->ticket_number)
                    <span class="text-xs bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 px-2 py-1 rounded">{{ $passenger->ticket_number }}</span>
                    @endif
                </div>
                @endforeach
            </div>
            @endif

            {{-- Payment Info --}}
            <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-6 mb-6">
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">{{ __('total_price') }}</span>
                    <span class="text-2xl font-bold text-primary-600">${{ number_format($booking->total_price ?? 0, 2) }}</span>
                </div>
            </div>

            {{-- WhatsApp Notice --}}
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 text-center">
                <i class="fab fa-whatsapp text-green-600 text-2xl mb-2"></i>
                <p class="text-sm text-green-700 dark:text-green-400">
                    {{ app()->getLocale() === 'ar' ? 'سيتم إرسال تفاصيل الحجز إلى رقم الواتساب الخاص بك' : 'Booking details will be sent to your WhatsApp number' }}
                </p>
            </div>

            <div class="mt-6 text-center">
                <a href="{{ route('booking.my-bookings') }}" class="bg-primary-600 hover:bg-primary-700 text-white font-semibold px-6 py-3 rounded-lg transition shadow-lg inline-block">
                    <i class="fas fa-ticket-alt {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ __('my_bookings') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
