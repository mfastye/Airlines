@extends('layouts.admin')
@section('title', __('my_bookings'))
@section('content')
<div class="space-y-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('search_booking_reference') }}" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
            <select name="status" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm"><option value="">{{ __('all_statuses') }}</option>@foreach(['pending','confirmed','issued','cancelled'] as $s)<option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ __(ucfirst($s)) }}</option>@endforeach</select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
            <button type="submit" class="bg-primary-600 text-white rounded-lg px-4 py-2 hover:bg-primary-700 text-sm"><i class="fas fa-search {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ __('search') }}</button>
        </form>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700"><tr><th class="px-4 py-3 text-start">{{ __('reference') }}</th><th class="px-4 py-3 text-start">{{ __('customer') }}</th><th class="px-4 py-3 text-start">{{ __('flight') }}</th><th class="px-4 py-3 text-start">{{ __('price') }}</th><th class="px-4 py-3 text-start">{{ __('status') }}</th><th class="px-4 py-3 text-start">{{ __('date') }}</th><th class="px-4 py-3 text-center">{{ __('actions') }}</th></tr></thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($bookings as $booking)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-medium text-primary-600">{{ $booking->booking_reference }}</td>
                        <td class="px-4 py-3">{{ optional($booking->customer)->first_name }} {{ optional($booking->customer)->last_name }}</td>
                        <td class="px-4 py-3"><span class="text-xs">{{ optional($booking->flight)->flight_number }}</span><br><span class="text-xs text-gray-500">{{ optional(optional($booking->flight)->departureAirport)->code }} &rarr; {{ optional(optional($booking->flight)->arrivalAirport)->code }}</span></td>
                        <td class="px-4 py-3 font-medium">${{ number_format($booking->total_price, 2) }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-1 text-xs rounded-full {{ $booking->status === 'issued' ? 'bg-green-100 text-green-700' : ($booking->status === 'confirmed' ? 'bg-blue-100 text-blue-700' : ($booking->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700')) }}">{{ ucfirst($booking->status) }}</span></td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $booking->created_at->format('Y-m-d') }}</td>
                        <td class="px-4 py-3 text-center"><a href="{{ route('agent.booking.show', $booking) }}" class="text-primary-600 hover:text-primary-700"><i class="fas fa-eye"></i></a></td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">{{ __('no_bookings') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($bookings, 'links'))<div class="p-4">{{ $bookings->withQueryString()->links() }}</div>@endif
    </div>
</div>
@endsection
