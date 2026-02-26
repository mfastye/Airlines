@extends('layouts.admin')
@section('title', app()->getLocale() === 'ar' ? 'الحساب المالي' : 'Financial Account')
@section('content')
<h2 class="text-xl font-bold text-gray-800 dark:text-white mb-6">{{ app()->getLocale() === 'ar' ? 'الحساب المالي' : 'Financial Account' }}</h2>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 text-center"><div class="text-sm text-gray-500">{{ app()->getLocale() === 'ar' ? 'الرصيد الحالي' : 'Current Balance' }}</div><div class="text-3xl font-bold text-primary-600">${{ number_format($account->balance ?? 0, 2) }}</div></div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 text-center"><div class="text-sm text-gray-500">{{ app()->getLocale() === 'ar' ? 'إجمالي الحجوزات' : 'Total Bookings' }}</div><div class="text-3xl font-bold text-green-600">{{ $totalBookings ?? 0 }}</div></div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 text-center"><div class="text-sm text-gray-500">{{ app()->getLocale() === 'ar' ? 'إجمالي المصروفات' : 'Total Spent' }}</div><div class="text-3xl font-bold text-red-600">${{ number_format($totalSpent ?? 0, 2) }}</div></div>
</div>
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
    <div class="p-4 border-b border-gray-200 dark:border-gray-700"><h3 class="font-semibold dark:text-white">{{ app()->getLocale() === 'ar' ? 'كشف الحساب' : 'Transaction History' }}</h3></div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'التاريخ' : 'Date' }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'النوع' : 'Type' }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'البيان' : 'Description' }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'دائن' : 'Credit' }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'مدين' : 'Debit' }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'الرصيد' : 'Balance' }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($transactions ?? [] as $tx)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $tx->created_at->format('d M Y H:i') }}</td>
                    <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full text-xs {{ $tx->type === 'credit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $tx->type === 'credit' ? (app()->getLocale() === 'ar' ? 'إضافة' : 'Credit') : (app()->getLocale() === 'ar' ? 'خصم' : 'Debit') }}</span></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $tx->description }}</td>
                    <td class="px-4 py-3 text-green-600 font-semibold">{{ $tx->type === 'credit' ? '$' . number_format($tx->amount, 2) : '' }}</td>
                    <td class="px-4 py-3 text-red-600 font-semibold">{{ $tx->type === 'debit' ? '$' . number_format($tx->amount, 2) : '' }}</td>
                    <td class="px-4 py-3 font-semibold dark:text-white">${{ number_format($tx->balance_after ?? 0, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">{{ __('no_data') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
