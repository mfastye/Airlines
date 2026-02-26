@extends('layouts.admin')
@section('title', __('manage_bookings'))
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ __('manage_bookings') }}</h2>
</div>
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ __('booking_reference') }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ __('flight_number') }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ __('passengers') }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ __('total_price') }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ __('booking_status') }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'التاريخ' : 'Date' }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ __('actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($bookings ?? [] as $booking)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3 font-mono font-bold text-primary-600">{{ $booking->booking_reference }}</td>
                    <td class="px-4 py-3 dark:text-gray-300">{{ $booking->flight->flight_number ?? '-' }}</td>
                    <td class="px-4 py-3 dark:text-gray-300">{{ $booking->passengers->count() }}</td>
                    <td class="px-4 py-3 font-semibold dark:text-white">${{ number_format($booking->total_price, 2) }}</td>
                    <td class="px-4 py-3"><span class="px-2 py-1 rounded-full text-xs font-medium @if($booking->status === 'confirmed') bg-green-100 text-green-800 @elseif($booking->status === 'cancelled') bg-red-100 text-red-800 @else bg-yellow-100 text-yellow-800 @endif">{{ __($booking->status) }}</span></td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $booking->created_at->format('d M Y H:i') }}</td>
                    <td class="px-4 py-3"><a href="{{ route('agent.bookings.show', $booking) }}" class="text-blue-600 hover:text-blue-800"><i class="fas fa-eye"></i></a></td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">{{ __('no_data') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
