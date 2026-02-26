@extends('layouts.admin')
@section('title', app()->getLocale() === 'ar' ? 'إدارة العملاء' : 'Manage Customers')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ app()->getLocale() === 'ar' ? 'إدارة العملاء' : 'Manage Customers' }}</h2>
</div>
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">#</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ __('name') }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ __('email') }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'الهاتف' : 'Phone' }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'الحجوزات' : 'Bookings' }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'تاريخ التسجيل' : 'Registered' }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ __('actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($customers ?? [] as $customer)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3 text-gray-500">{{ $loop->iteration }}</td>
                    <td class="px-4 py-3 font-medium text-gray-800 dark:text-white">{{ $customer->name }}</td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $customer->email }}</td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400 font-mono text-xs">{{ $customer->phone ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-800 dark:text-white font-semibold">{{ $customer->bookings_count ?? 0 }}</td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">{{ $customer->created_at->format('d M Y') }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.customers.show', $customer) }}" class="text-blue-600 hover:text-blue-800 p-1"><i class="fas fa-eye"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">{{ __('no_data') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
