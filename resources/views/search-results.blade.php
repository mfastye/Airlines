@extends('layouts.app')
@section('title', __('search_results'))
@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    {{-- Search Summary --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center space-x-4 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                <div class="text-center">
                    <div class="text-2xl font-bold text-primary-600">{{ $searchParams['departure_code'] ?? '' }}</div>
                    <div class="text-xs text-gray-500">{{ $searchParams['departure_name'] ?? __('departure') }}</div>
                </div>
                <div class="flex flex-col items-center">
                    <i class="fas fa-plane text-accent-500 text-xl {{ app()->getLocale() === 'ar' ? 'fa-flip-horizontal' : '' }}"></i>
                    <div class="w-24 h-px bg-gray-300 dark:bg-gray-600 mt-1"></div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-primary-600">{{ $searchParams['arrival_code'] ?? '' }}</div>
                    <div class="text-xs text-gray-500">{{ $searchParams['arrival_name'] ?? __('arrival') }}</div>
                </div>
            </div>
            <div class="flex items-center space-x-6 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }} text-sm text-gray-600 dark:text-gray-400">
                <span><i class="fas fa-calendar {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ $searchParams['departure_date'] ?? '' }}</span>
                <span><i class="fas fa-users {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ ($searchParams['adults'] ?? 1) + ($searchParams['children'] ?? 0) }} {{ app()->getLocale() === 'ar' ? 'مسافرين' : 'travelers' }}</span>
                <span><i class="fas fa-chair {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ __($searchParams['class'] ?? 'economy') }}</span>
            </div>
            <a href="{{ route('home') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm transition">
                <i class="fas fa-search {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ app()->getLocale() === 'ar' ? 'بحث جديد' : 'New Search' }}
            </a>
        </div>
    </div>

    {{-- Flight Results --}}
    @if(isset($flights) && count($flights) > 0)
    <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4">
        {{ __('search_results') }} ({{ count($flights) }})
    </h2>
    <div class="space-y-4">
        @foreach($flights as $flight)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden card-hover animate-fade-in">
            <div class="p-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    {{-- Airline Info --}}
                    <div class="flex items-center space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                        @if($flight->airline && $flight->airline->logo)
                        <img src="{{ asset('storage/' . $flight->airline->logo) }}" alt="{{ $flight->airline->name_en }}" class="w-12 h-12 object-contain rounded-lg">
                        @else
                        <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900 rounded-lg flex items-center justify-center">
                            <i class="fas fa-plane text-primary-600"></i>
                        </div>
                        @endif
                        <div>
                            <div class="font-semibold text-gray-800 dark:text-white">
                                {{ app()->getLocale() === 'ar' ? ($flight->airline->name_ar ?? '') : ($flight->airline->name_en ?? '') }}
                            </div>
                            <div class="text-xs text-gray-500">{{ $flight->flight_number }}</div>
                        </div>
                    </div>

                    {{-- Flight Time --}}
                    <div class="flex items-center space-x-6 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                        <div class="text-center">
                            <div class="text-xl font-bold text-gray-800 dark:text-white">{{ \Carbon\Carbon::parse($flight->departure_time)->format('H:i') }}</div>
                            <div class="text-xs text-gray-500">{{ $flight->departureAirport->code ?? '' }}</div>
                        </div>
                        <div class="flex flex-col items-center w-32">
                            <div class="text-xs text-gray-500">
                                @php
                                    $dep = \Carbon\Carbon::parse($flight->departure_time);
                                    $arr = \Carbon\Carbon::parse($flight->arrival_time);
                                    $diff = $dep->diff($arr);
                                @endphp
                                {{ $diff->h }}h {{ $diff->i }}m
                            </div>
                            <div class="w-full h-px bg-gray-300 dark:bg-gray-600 relative my-1">
                                <i class="fas fa-plane absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-primary-600 text-xs {{ app()->getLocale() === 'ar' ? 'fa-flip-horizontal' : '' }}"></i>
                            </div>
                            <div class="text-xs text-green-600 font-medium">{{ __('direct') }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xl font-bold text-gray-800 dark:text-white">{{ \Carbon\Carbon::parse($flight->arrival_time)->format('H:i') }}</div>
                            <div class="text-xs text-gray-500">{{ $flight->arrivalAirport->code ?? '' }}</div>
                        </div>
                    </div>

                    {{-- Price and Select --}}
                    <div class="text-center">
                        @php
                            $seat = $flight->seats->where('class', $searchParams['class'] ?? 'economy')->first();
                            $price = $seat ? $seat->price : 0;
                            $available = $seat ? $seat->available_seats : 0;
                        @endphp
                        <div class="text-2xl font-bold text-primary-600">${{ number_format($price, 2) }}</div>
                        <div class="text-xs text-gray-500 mb-2">{{ __('per_person') }}</div>
                        @if($available > 0)
                        <form action="{{ route('booking.select-flight') }}" method="POST">
                            @csrf
                            <input type="hidden" name="flight_id" value="{{ $flight->id }}">
                            <input type="hidden" name="seat_id" value="{{ $seat->id }}">
                            <input type="hidden" name="adults" value="{{ $searchParams['adults'] ?? 1 }}">
                            <input type="hidden" name="children" value="{{ $searchParams['children'] ?? 0 }}">
                            <input type="hidden" name="class" value="{{ $searchParams['class'] ?? 'economy' }}">
                            <button type="submit" class="bg-accent-600 hover:bg-accent-700 text-white font-semibold px-6 py-2 rounded-lg transition shadow text-sm">
                                {{ __('select_flight') }}
                            </button>
                        </form>
                        <div class="text-xs text-green-600 mt-1">{{ $available }} {{ __('available_seats') }}</div>
                        @else
                        <span class="text-red-500 text-sm font-medium">{{ app()->getLocale() === 'ar' ? 'محجوز بالكامل' : 'Fully Booked' }}</span>
                        @endif
                    </div>
                </div>

                {{-- Extra Info --}}
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex flex-wrap gap-4 text-xs text-gray-500">
                    @if($seat && $seat->baggage_allowance)
                    <span><i class="fas fa-suitcase {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ $seat->baggage_allowance }}kg {{ __('baggage') }}</span>
                    @endif
                    @if($seat && $seat->meal_included)
                    <span><i class="fas fa-utensils {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ __('meal') }}</span>
                    @endif
                    @if($seat && $seat->wifi_available)
                    <span><i class="fas fa-wifi {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ __('wifi') }}</span>
                    @endif
                    @if($seat && $seat->entertainment)
                    <span><i class="fas fa-tv {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ __('entertainment') }}</span>
                    @endif
                    <span><i class="fas fa-calendar {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ \Carbon\Carbon::parse($flight->departure_time)->format('D, d M Y') }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    {{-- No Flights Found --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-12 text-center">
        <i class="fas fa-plane-slash text-gray-300 dark:text-gray-600 text-6xl mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('no_flights_found') }}</h3>
        <p class="text-gray-500 mb-6">{{ app()->getLocale() === 'ar' ? 'نبحث لك عن رحلات قريبة من التاريخ المحدد...' : 'Searching for flights near your selected date...' }}</p>
    </div>
    @endif

    {{-- Suggested Nearby Flights --}}
    @if(isset($suggestedFlights) && count($suggestedFlights) > 0)
    <div class="mt-8">
        <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4">
            <i class="fas fa-lightbulb text-accent-500 {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ __('suggested_flights') }}
        </h2>
        <div class="space-y-4">
            @foreach($suggestedFlights as $flight)
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                        <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                            <i class="fas fa-plane text-yellow-600"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-800 dark:text-white">{{ $flight->flight_number }}</div>
                            <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($flight->departure_time)->format('D, d M Y - H:i') }}</div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                        <span class="text-lg font-bold text-primary-600">${{ number_format($flight->seats->first()->price ?? 0, 2) }}</span>
                        <form action="{{ route('booking.select-flight') }}" method="POST">
                            @csrf
                            <input type="hidden" name="flight_id" value="{{ $flight->id }}">
                            <input type="hidden" name="seat_id" value="{{ $flight->seats->first()->id ?? '' }}">
                            <input type="hidden" name="adults" value="{{ $searchParams['adults'] ?? 1 }}">
                            <input type="hidden" name="children" value="{{ $searchParams['children'] ?? 0 }}">
                            <input type="hidden" name="class" value="{{ $searchParams['class'] ?? 'economy' }}">
                            <button type="submit" class="bg-accent-600 hover:bg-accent-700 text-white px-4 py-2 rounded-lg text-sm transition">
                                {{ __('select_flight') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
