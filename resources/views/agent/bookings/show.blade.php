@extends('layouts.admin')
@section('title', __('booking_details') . ' - ' . $booking->booking_reference)
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div><h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ $booking->booking_reference }}</h2><span class="px-3 py-1 text-sm rounded-full {{ $booking->status === 'issued' ? 'bg-green-100 text-green-700' : ($booking->status === 'confirmed' ? 'bg-blue-100 text-blue-700' : ($booking->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700')) }}">{{ ucfirst($booking->status) }}</span></div>
        <a href="{{ route('agent.bookings') }}" class="text-sm text-gray-600 hover:text-gray-800 dark:text-gray-400"><i class="fas fa-arrow-left {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ __('back') }}</a>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4"><i class="fas fa-plane {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }} text-primary-600"></i>{{ __('flight_info') }}</h3>
            <div class="space-y-3">
                <div class="flex justify-between"><span class="text-gray-500">{{ __('flight_number') }}</span><span class="font-medium">{{ optional($booking->flight)->flight_number }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">{{ __('airline') }}</span><span class="font-medium">{{ optional(optional($booking->flight)->airline)->name_en }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">{{ __('from') }}</span><span class="font-medium">{{ optional(optional($booking->flight)->departureAirport)->code }} - {{ optional(optional($booking->flight)->departureAirport)->city_en }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">{{ __('to') }}</span><span class="font-medium">{{ optional(optional($booking->flight)->arrivalAirport)->code }} - {{ optional(optional($booking->flight)->arrivalAirport)->city_en }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">{{ __('departure') }}</span><span class="font-medium">{{ optional($booking->flight)->departure_time ? $booking->flight->departure_time->format('Y-m-d H:i') : '-' }}</span></div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4"><i class="fas fa-dollar-sign {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }} text-primary-600"></i>{{ __('price_details') }}</h3>
            <div class="space-y-3">
                <div class="flex justify-between"><span class="text-gray-500">{{ __('total_price') }}</span><span class="font-bold text-lg">${{ number_format($booking->total_price, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">{{ __('payment_status') }}</span><span class="px-2 py-1 text-xs rounded-full {{ ($booking->payment_status ?? 'pending') === 'paid' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">{{ ucfirst($booking->payment_status ?? 'pending') }}</span></div>
                @if($booking->ticket_number)<div class="flex justify-between"><span class="text-gray-500">{{ __('ticket_number') }}</span><span class="font-medium text-green-600">{{ $booking->ticket_number }}</span></div>@endif
                @if($booking->ticket_image)<a href="{{ asset('storage/' . $booking->ticket_image) }}" target="_blank" class="inline-flex items-center text-primary-600 hover:text-primary-700 text-sm"><i class="fas fa-download {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ __('download_ticket') }}</a>@endif
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700"><h3 class="text-lg font-semibold text-gray-800 dark:text-white"><i class="fas fa-users {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }} text-primary-600"></i>{{ __('passengers') }}</h3></div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700"><tr><th class="px-4 py-3 text-start">{{ __('name') }}</th><th class="px-4 py-3 text-start">{{ __('passport') }}</th><th class="px-4 py-3 text-start">{{ __('type') }}</th><th class="px-4 py-3 text-start">{{ __('nationality') }}</th></tr></thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($booking->passengers ?? [] as $p)
                    <tr><td class="px-4 py-3 font-medium">{{ $p->first_name }} {{ $p->last_name }}</td><td class="px-4 py-3">{{ $p->passport_number }}</td><td class="px-4 py-3"><span class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-700">{{ ucfirst($p->type ?? 'adult') }}</span></td><td class="px-4 py-3">{{ $p->nationality ?? '-' }}</td></tr>
                    @empty
                    <tr><td colspan="4" class="px-4 py-4 text-center text-gray-500">{{ __('no_passengers') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
