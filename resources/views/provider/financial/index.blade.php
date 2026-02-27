@extends('layouts.admin')
@section('title', __('financial'))
@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between"><div><p class="text-sm text-gray-500">{{ __('balance') }}</p><p class="text-2xl font-bold text-gray-800 dark:text-white">${{ number_format($stats['balance'] ?? 0, 2) }}</p></div><div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center"><i class="fas fa-wallet text-green-600 text-xl"></i></div></div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between"><div><p class="text-sm text-gray-500">{{ __('total_revenue') }}</p><p class="text-2xl font-bold text-gray-800 dark:text-white">${{ number_format($stats['total_revenue'] ?? 0, 2) }}</p></div><div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center"><i class="fas fa-chart-line text-blue-600 text-xl"></i></div></div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between"><div><p class="text-sm text-gray-500">{{ __('total_commissions') }}</p><p class="text-2xl font-bold text-accent-600">${{ number_format($stats['total_commissions'] ?? 0, 2) }}</p></div><div class="w-12 h-12 bg-accent-100 dark:bg-accent-900/30 rounded-xl flex items-center justify-center"><i class="fas fa-percentage text-accent-600 text-xl"></i></div></div>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
            <button type="submit" class="bg-primary-600 text-white rounded-lg px-4 py-2 hover:bg-primary-700 text-sm"><i class="fas fa-filter {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ __('filter') }}</button>
        </form>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700"><h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ __('transactions') }}</h3></div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700"><tr><th class="px-4 py-3 text-start">{{ __('date') }}</th><th class="px-4 py-3 text-start">{{ __('description') }}</th><th class="px-4 py-3 text-start">{{ __('type') }}</th><th class="px-4 py-3 text-start">{{ __('amount') }}</th><th class="px-4 py-3 text-start">{{ __('balance_after') }}</th></tr></thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($transactions ?? [] as $t)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $t->created_at->format('Y-m-d H:i') }}</td>
                        <td class="px-4 py-3">{{ app()->getLocale() === 'ar' ? ($t->description_ar ?? $t->description_en) : $t->description_en }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-1 text-xs rounded-full {{ $t->type === 'credit' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $t->type === 'credit' ? __('credit') : __('debit') }}</span></td>
                        <td class="px-4 py-3 font-medium {{ $t->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">{{ $t->type === 'credit' ? '+' : '-' }}${{ number_format($t->amount, 2) }}</td>
                        <td class="px-4 py-3 font-medium">${{ number_format($t->balance_after ?? 0, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">{{ __('no_transactions') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(is_object($transactions) && method_exists($transactions, 'links'))<div class="p-4">{{ $transactions->withQueryString()->links() }}</div>@endif
    </div>
</div>
@endsection
