@extends('layouts.app')
@section('title', __('payment'))
@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    {{-- Progress Steps --}}
    <div class="flex items-center justify-center mb-8">
        <div class="flex items-center space-x-4 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <div class="flex items-center"><div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-bold">1</div><span class="text-sm font-medium text-green-600 {{ app()->getLocale() === 'ar' ? 'mr-2' : 'ml-2' }}">{{ __('select_flight') }}</span></div>
            <div class="w-12 h-px bg-green-500"></div>
            <div class="flex items-center"><div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-bold">2</div><span class="text-sm font-medium text-green-600 {{ app()->getLocale() === 'ar' ? 'mr-2' : 'ml-2' }}">{{ __('passenger_details') }}</span></div>
            <div class="w-12 h-px bg-gray-300"></div>
            <div class="flex items-center"><div class="w-8 h-8 bg-primary-600 text-white rounded-full flex items-center justify-center text-sm font-bold">3</div><span class="text-sm font-medium text-primary-600 {{ app()->getLocale() === 'ar' ? 'mr-2' : 'ml-2' }}">{{ __('payment') }}</span></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Payment Form --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-6">
                    <i class="fas fa-credit-card text-primary-600 {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ __('select_payment_method') }}
                </h2>

                <form action="{{ route('booking.process-payment') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4 mb-6">
                        @if(isset($gateways))
                        @foreach($gateways as $gateway)
                        <label class="flex items-center p-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl cursor-pointer hover:border-primary-400 transition gateway-option">
                            <input type="radio" name="gateway_id" value="{{ $gateway->id }}" class="text-primary-600 focus:ring-primary-500" {{ $loop->first ? 'checked' : '' }}>
                            <div class="flex items-center {{ app()->getLocale() === 'ar' ? 'mr-3' : 'ml-3' }} flex-1">
                                @if($gateway->logo)
                                <img src="{{ asset('storage/' . $gateway->logo) }}" alt="{{ $gateway->name }}" class="w-10 h-10 object-contain {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}">
                                @else
                                <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}">
                                    <i class="fas fa-money-check-alt text-gray-500"></i>
                                </div>
                                @endif
                                <div>
                                    <div class="font-semibold text-gray-800 dark:text-white text-sm">{{ $gateway->name }}</div>
                                    @if($gateway->instructions)
                                    <div class="text-xs text-gray-500 mt-1">{{ Str::limit($gateway->instructions, 80) }}</div>
                                    @endif
                                </div>
                            </div>
                        </label>
                        @endforeach
                        @endif
                    </div>

                    {{-- Payment Instructions --}}
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                        <h4 class="text-sm font-semibold text-blue-800 dark:text-blue-300 mb-2">
                            <i class="fas fa-info-circle {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ __('payment_instructions') }}
                        </h4>
                        <p class="text-xs text-blue-600 dark:text-blue-400">
                            {{ app()->getLocale() === 'ar' ? 'قم بتحويل المبلغ المطلوب ثم ارفع صورة إيصال الدفع أدناه' : 'Transfer the required amount then upload the payment receipt below' }}
                        </p>
                    </div>

                    {{-- Receipt Upload --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('upload_receipt') }} *</label>
                        <input type="file" name="receipt" accept="image/*,.pdf" required
                            class="w-full text-sm file:bg-primary-600 file:text-white file:border-0 file:rounded-lg file:px-4 file:py-2 file:cursor-pointer file:hover:bg-primary-700 file:transition">
                    </div>

                    <button type="submit" class="w-full bg-accent-600 hover:bg-accent-700 text-white font-bold py-3.5 rounded-xl transition shadow-lg text-lg">
                        <i class="fas fa-lock {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ __('confirm_payment') }}
                    </button>
                </form>
            </div>
        </div>

        {{-- Order Summary --}}
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 sticky top-20">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">{{ app()->getLocale() === 'ar' ? 'ملخص الحجز' : 'Booking Summary' }}</h3>
                @if(isset($flight))
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ __('flight_number') }}</span>
                        <span class="font-semibold dark:text-white">{{ $flight->flight_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ __('departure') }}</span>
                        <span class="font-semibold dark:text-white">{{ $flight->departureAirport->code ?? '' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ __('arrival') }}</span>
                        <span class="font-semibold dark:text-white">{{ $flight->arrivalAirport->code ?? '' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ __('date') }}</span>
                        <span class="font-semibold dark:text-white">{{ \Carbon\Carbon::parse($flight->departure_time)->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ __('travel_class') }}</span>
                        <span class="font-semibold dark:text-white">{{ __(session('booking.class', 'economy')) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ app()->getLocale() === 'ar' ? 'عدد المسافرين' : 'Passengers' }}</span>
                        <span class="font-semibold dark:text-white">{{ $totalPassengers ?? 1 }}</span>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-600 pt-3">
                        <div class="flex justify-between">
                            <span class="text-gray-500">{{ __('price') }} x {{ $totalPassengers ?? 1 }}</span>
                            <span class="font-semibold dark:text-white">${{ number_format(($seat->price ?? 0) * ($totalPassengers ?? 1), 2) }}</span>
                        </div>
                    </div>
                    <div class="border-t-2 border-primary-200 dark:border-primary-800 pt-3">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold text-gray-800 dark:text-white">{{ __('total_price') }}</span>
                            <span class="text-2xl font-bold text-primary-600">${{ number_format(($seat->price ?? 0) * ($totalPassengers ?? 1), 2) }}</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
