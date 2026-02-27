@extends('layouts.admin')
@section('title', __('search_results'))
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ __('search_results') }}</h2>
        <a href="{{ route('agent.dashboard') }}" class="text-sm text-gray-600 hover:text-gray-800 dark:text-gray-400"><i class="fas fa-arrow-left {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ __('new_search') }}</a>
    </div>
    @forelse($results ?? [] as $result)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex-1">
                <div class="flex items-center space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }} mb-3">
                    <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center"><i class="fas fa-plane text-primary-600"></i></div>
                    <div><p class="font-semibold text-gray-800 dark:text-white">{{ optional(optional($result['flight'] ?? null)->airline)->name_en }}</p><p class="text-sm text-gray-500">{{ optional($result['flight'] ?? null)->flight_number }}</p></div>
                </div>
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div><p class="text-lg font-bold text-gray-800 dark:text-white">{{ optional($result['flight'] ?? null)->departure_time ? $result['flight']->departure_time->format('H:i') : '-' }}</p><p class="text-sm text-gray-500">{{ optional(optional($result['flight'] ?? null)->departureAirport)->code }}</p></div>
                    <div class="flex flex-col items-center justify-center"><div class="w-full border-t border-dashed border-gray-300 dark:border-gray-600 relative"><i class="fas fa-plane absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-primary-600 bg-white dark:bg-gray-800 px-1"></i></div><p class="text-xs text-gray-400 mt-1">{{ __('direct') }}</p></div>
                    <div><p class="text-lg font-bold text-gray-800 dark:text-white">{{ optional($result['flight'] ?? null)->arrival_time ? $result['flight']->arrival_time->format('H:i') : '-' }}</p><p class="text-sm text-gray-500">{{ optional(optional($result['flight'] ?? null)->arrivalAirport)->code }}</p></div>
                </div>
            </div>
            <div class="lg:w-64 space-y-3">
                @foreach($result['seats'] ?? [] as $seat)
                <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                    <div class="flex items-center justify-between mb-2"><span class="text-sm font-medium">{{ ucfirst($seat->class) }}</span><span class="text-xs text-gray-500">{{ $seat->available_seats }} {{ __('available') }}</span></div>
                    <p class="text-xl font-bold text-primary-600 mb-2">${{ number_format($seat->adult_price, 2) }}</p>
                    <form action="{{ route('agent.book') }}" method="POST">@csrf
                        <input type="hidden" name="flight_id" value="{{ optional($result['flight'] ?? null)->id }}">
                        <input type="hidden" name="flight_seat_id" value="{{ $seat->id }}">
                        <div class="space-y-2 mb-2">
                            <input type="text" name="customer_name" required placeholder="{{ __('customer_name') }}" class="w-full text-xs rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                            <input type="text" name="customer_phone" required placeholder="{{ __('customer_phone') }}" class="w-full text-xs rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded p-2 space-y-1">
                                <p class="text-xs font-medium">{{ __('passenger') }} 1</p>
                                <input type="text" name="passengers[0][first_name]" required placeholder="{{ __('first_name') }}" class="w-full text-xs rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                <input type="text" name="passengers[0][last_name]" required placeholder="{{ __('last_name') }}" class="w-full text-xs rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                <input type="text" name="passengers[0][passport_number]" required placeholder="{{ __('passport_number') }}" class="w-full text-xs rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                <input type="date" name="passengers[0][date_of_birth]" required class="w-full text-xs rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                <select name="passengers[0][gender]" required class="w-full text-xs rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700"><option value="male">{{ __('male') }}</option><option value="female">{{ __('female') }}</option></select>
                                <input type="text" name="passengers[0][nationality]" required placeholder="{{ __('nationality') }}" class="w-full text-xs rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                <input type="hidden" name="passengers[0][type]" value="adult">
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-primary-600 text-white rounded px-3 py-2 text-sm hover:bg-primary-700"><i class="fas fa-shopping-cart {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ __('book_now') }}</button>
                        <p class="text-xs text-gray-500 mt-1 text-center">{{ __('your_balance') }}: ${{ number_format($agent->balance ?? 0, 2) }}</p>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center border border-gray-200 dark:border-gray-700">
        <i class="fas fa-search text-4xl text-gray-300 mb-4"></i>
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ __('no_results') }}</h3>
        <p class="text-sm text-gray-500 mt-2">{{ __('try_different_search') }}</p>
    </div>
    @endforelse
</div>
@endsection
