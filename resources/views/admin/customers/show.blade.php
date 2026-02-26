@extends('layouts.admin')
@section('title', app()->getLocale() === 'ar' ? 'تفاصيل العميل' : 'Customer Details')
@section('content')
<div class="max-w-4xl">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.customers.index') }}" class="text-gray-500 hover:text-primary-600 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"><i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i></a>
        <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ $customer->name }}</h2>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div><span class="text-gray-500 block">{{ __('email') }}</span><span class="font-medium dark:text-white">{{ $customer->email }}</span></div>
            <div><span class="text-gray-500 block">{{ app()->getLocale() === 'ar' ? 'الهاتف' : 'Phone' }}</span><span class="font-medium dark:text-white">{{ $customer->phone ?? '-' }}</span></div>
            <div><span class="text-gray-500 block">{{ app()->getLocale() === 'ar' ? 'الحجوزات' : 'Bookings' }}</span><span class="font-bold text-primary-600 text-lg">{{ $customer->bookings->count() }}</span></div>
            <div><span class="text-gray-500 block">{{ app()->getLocale() === 'ar' ? 'تاريخ التسجيل' : 'Registered' }}</span><span class="font-medium dark:text-white">{{ $customer->created_at->format('d M Y') }}</span></div>
        </div>
    </div>
    @if($customer->bookings && $customer->bookings->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700"><h3 class="font-semibold dark:text-white">{{ __('manage_bookings') }}</h3></div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700"><tr><th class="px-4 py-2 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}">{{ __('booking_reference') }}</th><th class="px-4 py-2 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}">{{ __('flight_number') }}</th><th class="px-4 py-2 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}">{{ __('total_price') }}</th><th class="px-4 py-2 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}">{{ __('status') }}</th><th class="px-4 py-2 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}">{{ app()->getLocale() === 'ar' ? 'التاريخ' : 'Date' }}</th></tr></thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($customer->bookings as $booking)
                    <tr><td class="px-4 py-2 font-mono text-primary-600">{{ $booking->booking_reference }}</td><td class="px-4 py-2 dark:text-gray-300">{{ $booking->flight->flight_number ?? '-' }}</td><td class="px-4 py-2 font-semibold dark:text-white">${{ number_format($booking->total_price, 2) }}</td><td class="px-4 py-2"><span class="px-2 py-0.5 rounded-full text-xs {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">{{ __($booking->status) }}</span></td><td class="px-4 py-2 text-gray-500 text-xs">{{ $booking->created_at->format('d M Y') }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
