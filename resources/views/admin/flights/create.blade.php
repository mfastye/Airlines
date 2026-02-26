@extends('layouts.admin')
@section('title', __('add_new') . ' - ' . __('manage_flights'))
@section('content')
<div class="max-w-3xl">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.flights.index') }}" class="text-gray-500 hover:text-primary-600 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"><i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i></a>
        <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ __('add_new') }} {{ app()->getLocale() === 'ar' ? 'رحلة' : 'Flight' }}</h2>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <form action="{{ route('admin.flights.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('airline') }} *</label><select name="airline_id" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500">@foreach($airlines ?? [] as $airline)<option value="{{ $airline->id }}">{{ app()->getLocale() === 'ar' ? $airline->name_ar : $airline->name_en }} ({{ $airline->code }})</option>@endforeach</select></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('flight_number') }} *</label><input type="text" name="flight_number" value="{{ old('flight_number') }}" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500 font-mono"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('departure_airport') }} *</label><select name="departure_airport_id" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500">@foreach($airports ?? [] as $airport)<option value="{{ $airport->id }}">{{ $airport->code }} - {{ app()->getLocale() === 'ar' ? $airport->name_ar : $airport->name_en }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('arrival_airport') }} *</label><select name="arrival_airport_id" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500">@foreach($airports ?? [] as $airport)<option value="{{ $airport->id }}">{{ $airport->code }} - {{ app()->getLocale() === 'ar' ? $airport->name_ar : $airport->name_en }}</option>@endforeach</select></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'وقت المغادرة' : 'Departure Time' }} *</label><input type="datetime-local" name="departure_time" value="{{ old('departure_time') }}" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500"></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'وقت الوصول' : 'Arrival Time' }} *</label><input type="datetime-local" name="arrival_time" value="{{ old('arrival_time') }}" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'المزود' : 'Provider' }}</label><select name="provider_id" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500"><option value="">{{ app()->getLocale() === 'ar' ? 'اختر المزود' : 'Select Provider' }}</option>@foreach($providers ?? [] as $provider)<option value="{{ $provider->id }}">{{ $provider->company_name }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('status') }}</label><select name="status" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"><option value="active">{{ __('active') }}</option><option value="inactive">{{ __('inactive') }}</option><option value="cancelled">{{ __('cancelled') }}</option></select></div>
            </div>

            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mt-6 mb-4 border-t border-gray-200 dark:border-gray-600 pt-4">{{ app()->getLocale() === 'ar' ? 'المقاعد' : 'Seats' }}</h3>
            <div class="space-y-4">
                @foreach(['economy' => 'Economy', 'business' => 'Business', 'first' => 'First Class'] as $class => $label)
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h4 class="font-medium text-gray-800 dark:text-white mb-3">{{ app()->getLocale() === 'ar' ? __($class) : $label }}</h4>
                    <div class="grid grid-cols-3 gap-3">
                        <div><label class="block text-xs text-gray-500 mb-1">{{ app()->getLocale() === 'ar' ? 'عدد المقاعد' : 'Total Seats' }}</label><input type="number" name="seats[{{ $class }}][total]" min="0" value="0" class="w-full py-2 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-sm"></div>
                        <div><label class="block text-xs text-gray-500 mb-1">{{ __('price') }} ($)</label><input type="number" name="seats[{{ $class }}][price]" min="0" step="0.01" value="0" class="w-full py-2 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-sm"></div>
                        <div><label class="block text-xs text-gray-500 mb-1">{{ app()->getLocale() === 'ar' ? 'الأمتعة (كجم)' : 'Baggage (kg)' }}</label><input type="number" name="seats[{{ $class }}][baggage]" min="0" value="23" class="w-full py-2 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-sm"></div>
                    </div>
                </div>
                @endforeach
            </div>

            <button type="submit" class="w-full mt-6 bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 rounded-lg transition shadow-lg"><i class="fas fa-save {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ __('save') }}</button>
        </form>
    </div>
</div>
@endsection
