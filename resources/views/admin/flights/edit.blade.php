@extends('layouts.admin')
@section('title', __('edit') . ' - ' . __('manage_flights'))
@section('content')
<div class="max-w-3xl">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.flights.index') }}" class="text-gray-500 hover:text-primary-600 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"><i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i></a>
        <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ __('edit') }} - {{ $flight->flight_number }}</h2>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <form action="{{ route('admin.flights.update', $flight) }}" method="POST">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('airline') }} *</label><select name="airline_id" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm">@foreach($airlines ?? [] as $airline)<option value="{{ $airline->id }}" {{ $flight->airline_id == $airline->id ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? $airline->name_ar : $airline->name_en }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('flight_number') }} *</label><input type="text" name="flight_number" value="{{ old('flight_number', $flight->flight_number) }}" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm font-mono"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('departure_airport') }} *</label><select name="departure_airport_id" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm">@foreach($airports ?? [] as $airport)<option value="{{ $airport->id }}" {{ $flight->departure_airport_id == $airport->id ? 'selected' : '' }}>{{ $airport->code }} - {{ app()->getLocale() === 'ar' ? $airport->name_ar : $airport->name_en }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('arrival_airport') }} *</label><select name="arrival_airport_id" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm">@foreach($airports ?? [] as $airport)<option value="{{ $airport->id }}" {{ $flight->arrival_airport_id == $airport->id ? 'selected' : '' }}>{{ $airport->code }} - {{ app()->getLocale() === 'ar' ? $airport->name_ar : $airport->name_en }}</option>@endforeach</select></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'وقت المغادرة' : 'Departure Time' }} *</label><input type="datetime-local" name="departure_time" value="{{ old('departure_time', $flight->departure_time ? \Carbon\Carbon::parse($flight->departure_time)->format('Y-m-d\TH:i') : '') }}" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'وقت الوصول' : 'Arrival Time' }} *</label><input type="datetime-local" name="arrival_time" value="{{ old('arrival_time', $flight->arrival_time ? \Carbon\Carbon::parse($flight->arrival_time)->format('Y-m-d\TH:i') : '') }}" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'المزود' : 'Provider' }}</label><select name="provider_id" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"><option value="">-</option>@foreach($providers ?? [] as $provider)<option value="{{ $provider->id }}" {{ $flight->provider_id == $provider->id ? 'selected' : '' }}>{{ $provider->company_name }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('status') }}</label><select name="status" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"><option value="active" {{ $flight->status === 'active' ? 'selected' : '' }}>{{ __('active') }}</option><option value="inactive" {{ $flight->status === 'inactive' ? 'selected' : '' }}>{{ __('inactive') }}</option><option value="cancelled" {{ $flight->status === 'cancelled' ? 'selected' : '' }}>{{ __('cancelled') }}</option></select></div>
            </div>
            <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 rounded-lg transition shadow-lg"><i class="fas fa-save {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ __('save') }}</button>
        </form>
    </div>
</div>
@endsection
