@extends('layouts.admin')
@section('title', __('agent_details'))
@section('content')
<div class="max-w-4xl">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.agents.index') }}" class="text-gray-500 hover:text-primary-600 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"><i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i></a>
        <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ $agent->company_name }}</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 text-center"><div class="text-sm text-gray-500">{{ app()->getLocale() === 'ar' ? 'الرصيد' : 'Balance' }}</div><div class="text-2xl font-bold text-primary-600">${{ number_format($agent->financialAccount->balance ?? 0, 2) }}</div></div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 text-center"><div class="text-sm text-gray-500">{{ app()->getLocale() === 'ar' ? 'الحجوزات' : 'Bookings' }}</div><div class="text-2xl font-bold text-gray-800 dark:text-white">{{ $agent->bookings->count() ?? 0 }}</div></div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 text-center"><div class="text-sm text-gray-500">API</div><div class="text-2xl font-bold {{ $agent->api_enabled ? 'text-green-600' : 'text-gray-400' }}">{{ $agent->api_enabled ? 'ON' : 'OFF' }}</div></div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
        <h3 class="font-semibold dark:text-white mb-4">{{ app()->getLocale() === 'ar' ? 'المعلومات' : 'Details' }}</h3>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><span class="text-gray-500">{{ app()->getLocale() === 'ar' ? 'المزود' : 'Provider' }}:</span> <span class="dark:text-white">{{ $agent->provider->company_name ?? '-' }}</span></div>
            <div><span class="text-gray-500">{{ app()->getLocale() === 'ar' ? 'المسؤول' : 'Contact' }}:</span> <span class="dark:text-white">{{ $agent->contact_person }}</span></div>
            <div><span class="text-gray-500">{{ app()->getLocale() === 'ar' ? 'الهاتف' : 'Phone' }}:</span> <span class="dark:text-white">{{ $agent->phone }}</span></div>
            <div><span class="text-gray-500">{{ __('status') }}:</span> <span class="px-2 py-0.5 rounded-full text-xs {{ $agent->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ __($agent->status) }}</span></div>
        </div>
    </div>
    @if($agent->api_enabled && $agent->api_key)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <h3 class="font-semibold dark:text-white mb-4">{{ app()->getLocale() === 'ar' ? 'بيانات API' : 'API Credentials' }}</h3>
        <div class="space-y-3 text-sm">
            <div><span class="text-gray-500">API Key:</span> <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-xs">{{ $agent->api_key }}</code></div>
            <div><span class="text-gray-500">API Secret:</span> <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-xs">{{ $agent->api_secret }}</code></div>
        </div>
    </div>
    @endif
</div>
@endsection
