@extends('layouts.admin')
@section('title', __('manage_providers'))
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ __('manage_providers') }}</h2>
    <a href="{{ route('admin.providers.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm transition shadow"><i class="fas fa-plus {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ __('add_new') }}</a>
</div>
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">#</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'الاسم التجاري' : 'Company Name' }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'المسؤول' : 'Contact' }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'الهاتف' : 'Phone' }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'العمولة' : 'Commission' }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'الرصيد' : 'Balance' }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ __('status') }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ __('actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($providers as $provider)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3 text-gray-500">{{ $loop->iteration }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                            @if($provider->logo)<img src="{{ asset('storage/' . $provider->logo) }}" class="w-8 h-8 rounded object-contain">@endif
                            <span class="font-medium text-gray-800 dark:text-white">{{ $provider->company_name }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $provider->contact_person }}</td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400 font-mono text-xs">{{ $provider->phone }}</td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $provider->commission_type === 'percentage' ? $provider->commission_value . '%' : '$' . number_format($provider->commission_value, 2) }}</td>
                    <td class="px-4 py-3 font-semibold text-primary-600">${{ number_format($provider->financialAccount->balance ?? 0, 2) }}</td>
                    <td class="px-4 py-3"><span class="px-2 py-1 rounded-full text-xs font-medium {{ $provider->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800' }}">{{ __($provider->status) }}</span></td>
                    <td class="px-4 py-3">
                        <div class="flex items-center space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                            <a href="{{ route('admin.providers.edit', $provider) }}" class="text-blue-600 hover:text-blue-800 p-1"><i class="fas fa-edit"></i></a>
                            <a href="{{ route('admin.providers.show', $provider) }}" class="text-green-600 hover:text-green-800 p-1"><i class="fas fa-eye"></i></a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">{{ __('no_data') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
