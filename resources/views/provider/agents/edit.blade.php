@extends('layouts.admin')
@section('title', __('edit') . ' - ' . __('manage_agents'))
@section('content')
<div class="max-w-2xl">
    <div class="flex items-center mb-6">
        <a href="{{ route('provider.agents.index') }}" class="text-gray-500 hover:text-primary-600 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"><i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i></a>
        <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ __('edit') }} - {{ $agent->company_name }}</h2>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <form action="{{ route('provider.agents.update', $agent) }}" method="POST">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'الاسم التجاري' : 'Company Name' }} *</label><input type="text" name="company_name" value="{{ old('company_name', $agent->company_name) }}" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'اسم المسؤول' : 'Contact' }}</label><input type="text" name="contact_person" value="{{ old('contact_person', $agent->contact_person) }}" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"></div>
                    <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'رقم الواتساب' : 'WhatsApp' }}</label><input type="tel" name="phone" value="{{ old('phone', $agent->phone) }}" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"></div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'نوع العمولة' : 'Commission Type' }}</label><select name="commission_type" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"><option value="fixed" {{ $agent->commission_type === 'fixed' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'مبلغ ثابت' : 'Fixed' }}</option><option value="percentage" {{ $agent->commission_type === 'percentage' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'نسبة مئوية' : 'Percentage' }}</option></select></div>
                    <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'قيمة العمولة' : 'Commission Value' }}</label><input type="number" name="commission_value" value="{{ old('commission_value', $agent->commission_value) }}" step="0.01" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"></div>
                </div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('status') }}</label><select name="status" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"><option value="active" {{ $agent->status === 'active' ? 'selected' : '' }}>{{ __('active') }}</option><option value="suspended" {{ $agent->status === 'suspended' ? 'selected' : '' }}>{{ __('suspended') }}</option></select></div>
                <div><label class="flex items-center"><input type="checkbox" name="api_enabled" value="1" {{ $agent->api_enabled ? 'checked' : '' }} class="rounded text-primary-600"><span class="text-sm text-gray-700 dark:text-gray-300 {{ app()->getLocale() === 'ar' ? 'mr-2' : 'ml-2' }}">{{ app()->getLocale() === 'ar' ? 'تفعيل API' : 'Enable API' }}</span></label></div>
            </div>
            <button type="submit" class="w-full mt-6 bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 rounded-lg transition shadow-lg"><i class="fas fa-save {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ __('save') }}</button>
        </form>
    </div>
</div>
@endsection
