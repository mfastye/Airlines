@extends('layouts.admin')
@section('title', __('manage_bookings'))
@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-3 text-center border border-gray-200 dark:border-gray-700"><p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['total'] ?? 0 }}</p><p class="text-xs text-gray-500">{{ __('total') }}</p></div>
        <div class="bg-amber-50 dark:bg-amber-900/20 rounded-lg p-3 text-center border border-amber-200"><p class="text-2xl font-bold text-amber-600">{{ $stats['pending'] ?? 0 }}</p><p class="text-xs text-amber-600">{{ __('pending') }}</p></div>
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 text-center border border-blue-200"><p class="text-2xl font-bold text-blue-600">{{ $stats['confirmed'] ?? 0 }}</p><p class="text-xs text-blue-600">{{ __('confirmed') }}</p></div>
        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3 text-center border border-green-200"><p class="text-2xl font-bold text-green-600">{{ $stats['issued'] ?? 0 }}</p><p class="text-xs text-green-600">{{ __('issued') }}</p></div>
        <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-3 text-center border border-red-200"><p class="text-2xl font-bold text-red-600">{{ $stats['cancelled'] ?? 0 }}</p><p class="text-xs text-red-600">{{ __('cancelled') }}</p></div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('search_booking_reference') }}" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
            <select name="status" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                <option value="">{{ __('all_statuses') }}</option>
                @foreach(['pending','confirmed','issued','cancelled'] as $s)<option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ __(ucfirst($s)) }}</option>@endforeach
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
            <button type="submit" class="bg-primary-600 text-white rounded-lg px-4 py-2 hover:bg-primary-700 text-sm"><i class="fas fa-search {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ __('search') }}</button>
        </form>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-start font-medium text-gray-600 dark:text-gray-300">{{ __('reference') }}</th>
                        <th class="px-4 py-3 text-start font-medium text-gray-600 dark:text-gray-300">{{ __('customer') }}</th>
                        <th class="px-4 py-3 text-start font-medium text-gray-600 dark:text-gray-300">{{ __('flight') }}</th>
                        <th class="px-4 py-3 text-start font-medium text-gray-600 dark:text-gray-300">{{ __('agent') }}</th>
                        <th class="px-4 py-3 text-start font-medium text-gray-600 dark:text-gray-300">{{ __('price') }}</th>
                        <th class="px-4 py-3 text-start font-medium text-gray-600 dark:text-gray-300">{{ __('status') }}</th>
                        <th class="px-4 py-3 text-start font-medium text-gray-600 dark:text-gray-300">{{ __('date') }}</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-300">{{ __('actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($bookings as $booking)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-medium text-primary-600">{{ $booking->booking_reference }}</td>
                        <td class="px-4 py-3">{{ optional($booking->customer)->first_name }} {{ optional($booking->customer)->last_name }}</td>
                        <td class="px-4 py-3"><span class="text-xs">{{ optional($booking->flight)->flight_number }}</span><br><span class="text-xs text-gray-500">{{ optional(optional($booking->flight)->departureAirport)->code }} &rarr; {{ optional(optional($booking->flight)->arrivalAirport)->code }}</span></td>
                        <td class="px-4 py-3 text-xs">{{ optional($booking->agent)->name ?? '-' }}</td>
                        <td class="px-4 py-3 font-medium">${{ number_format($booking->total_price, 2) }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-1 text-xs rounded-full {{ $booking->status === 'issued' ? 'bg-green-100 text-green-700' : ($booking->status === 'confirmed' ? 'bg-blue-100 text-blue-700' : ($booking->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700')) }}">{{ ucfirst($booking->status) }}</span></td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $booking->created_at->format('Y-m-d') }}</td>
                        <td class="px-4 py-3 text-center"><a href="{{ route('provider.bookings.show', $booking) }}" class="text-primary-600 hover:text-primary-700"><i class="fas fa-eye"></i></a></td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">{{ __('no_bookings') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($bookings, 'links'))<div class="p-4">{{ $bookings->withQueryString()->links() }}</div>@endif
    </div>
</div>
@endsection
