@extends('layouts.admin')
@section('title', __('provider_details'))
@section('content')
<div class="max-w-4xl">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.providers.index') }}" class="text-gray-500 hover:text-primary-600 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"><i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i></a>
        <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ $provider->company_name }}</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 text-center"><div class="text-sm text-gray-500">{{ app()->getLocale() === 'ar' ? 'الرصيد' : 'Balance' }}</div><div class="text-2xl font-bold text-primary-600">${{ number_format($provider->financialAccount->balance ?? 0, 2) }}</div></div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 text-center"><div class="text-sm text-gray-500">{{ app()->getLocale() === 'ar' ? 'المقاعد' : 'Seats' }}</div><div class="text-2xl font-bold text-gray-800 dark:text-white">{{ $provider->flights->sum(fn($f) => $f->seats->sum('total_seats')) }}</div></div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 text-center"><div class="text-sm text-gray-500">{{ app()->getLocale() === 'ar' ? 'الوكلاء' : 'Agents' }}</div><div class="text-2xl font-bold text-gray-800 dark:text-white">{{ $provider->agents->count() }}</div></div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
        <h3 class="font-semibold dark:text-white mb-4">{{ app()->getLocale() === 'ar' ? 'المعلومات' : 'Details' }}</h3>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><span class="text-gray-500">{{ app()->getLocale() === 'ar' ? 'المسؤول' : 'Contact' }}:</span> <span class="dark:text-white">{{ $provider->contact_person }}</span></div>
            <div><span class="text-gray-500">{{ app()->getLocale() === 'ar' ? 'الهاتف' : 'Phone' }}:</span> <span class="dark:text-white">{{ $provider->phone }}</span></div>
            <div><span class="text-gray-500">{{ __('email') }}:</span> <span class="dark:text-white">{{ $provider->email ?? '-' }}</span></div>
            <div><span class="text-gray-500">{{ app()->getLocale() === 'ar' ? 'العمولة' : 'Commission' }}:</span> <span class="dark:text-white">{{ $provider->commission_type === 'percentage' ? $provider->commission_value . '%' : '$' . $provider->commission_value }}</span></div>
            <div><span class="text-gray-500">{{ __('status') }}:</span> <span class="px-2 py-0.5 rounded-full text-xs {{ $provider->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ __($provider->status) }}</span></div>
            <div><span class="text-gray-500">{{ app()->getLocale() === 'ar' ? 'تاريخ التسجيل' : 'Registered' }}:</span> <span class="dark:text-white">{{ $provider->created_at->format('d M Y') }}</span></div>
        </div>
    </div>
</div>
@endsection
