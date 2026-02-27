@extends('layouts.admin')
@section('title', __('reports'))
@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border text-center"><p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $summary['total_bookings'] ?? 0 }}</p><p class="text-xs text-gray-500">{{ __('total_bookings') }}</p></div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border text-center"><p class="text-2xl font-bold text-green-600">{{ $summary['total_tickets'] ?? 0 }}</p><p class="text-xs text-gray-500">{{ __('tickets_issued') }}</p></div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border text-center"><p class="text-2xl font-bold text-blue-600">{{ $summary['seats_sold'] ?? 0 }}</p><p class="text-xs text-gray-500">{{ __('seats_sold') }}</p></div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border text-center"><p class="text-2xl font-bold text-gray-800 dark:text-white">${{ number_format($summary['total_revenue'] ?? 0, 2) }}</p><p class="text-xs text-gray-500">{{ __('total_revenue') }}</p></div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border text-center"><p class="text-2xl font-bold text-accent-600">${{ number_format($summary['total_commissions'] ?? 0, 2) }}</p><p class="text-xs text-gray-500">{{ __('total_commissions') }}</p></div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border"><form method="GET" class="flex flex-wrap gap-3"><input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm"><input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm"><select name="status" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm"><option value="">{{ __('all_statuses') }}</option>@foreach(['pending','confirmed','issued','cancelled'] as $s)<option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ __(ucfirst($s)) }}</option>@endforeach</select><button type="submit" class="bg-primary-600 text-white rounded-lg px-4 py-2 hover:bg-primary-700 text-sm"><i class="fas fa-filter {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ __('filter') }}</button></form></div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700"><tr><th class="px-4 py-3 text-start">{{ __('reference') }}</th><th class="px-4 py-3 text-start">{{ __('customer') }}</th><th class="px-4 py-3 text-start">{{ __('flight') }}</th><th class="px-4 py-3 text-start">{{ __('agent') }}</th><th class="px-4 py-3 text-start">{{ __('total_price') }}</th><th class="px-4 py-3 text-start">{{ __('commission') }}</th><th class="px-4 py-3 text-start">{{ __('status') }}</th><th class="px-4 py-3 text-start">{{ __('date') }}</th></tr></thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($bookings ?? [] as $b)
                    <tr><td class="px-4 py-3 font-medium text-primary-600">{{ $b->booking_reference }}</td><td class="px-4 py-3">{{ optional($b->customer)->first_name }} {{ optional($b->customer)->last_name }}</td><td class="px-4 py-3 text-xs">{{ optional($b->flight)->flight_number }}</td><td class="px-4 py-3 text-xs">{{ optional($b->agent)->name ?? '-' }}</td><td class="px-4 py-3 font-medium">${{ number_format($b->total_price, 2) }}</td><td class="px-4 py-3 text-accent-600">${{ number_format($b->commission_amount ?? 0, 2) }}</td><td class="px-4 py-3"><span class="px-2 py-1 text-xs rounded-full {{ $b->status === 'issued' ? 'bg-green-100 text-green-700' : ($b->status === 'confirmed' ? 'bg-blue-100 text-blue-700' : ($b->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700')) }}">{{ ucfirst($b->status) }}</span></td><td class="px-4 py-3 text-xs text-gray-500">{{ $b->created_at->format('Y-m-d') }}</td></tr>
                    @empty
                    <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">{{ __('no_data') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(is_object($bookings ?? null) && method_exists($bookings, 'links'))<div class="p-4">{{ $bookings->withQueryString()->links() }}</div>@endif
    </div>
</div>
@endsection
