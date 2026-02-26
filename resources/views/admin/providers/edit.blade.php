@extends('layouts.admin')
@section('title', __('edit') . ' - ' . __('manage_providers'))
@section('content')
<div class="max-w-3xl">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.providers.index') }}" class="text-gray-500 hover:text-primary-600 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"><i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i></a>
        <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ __('edit') }} - {{ $provider->company_name }}</h2>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <form action="{{ route('admin.providers.update', $provider) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'الاسم التجاري' : 'Company Name' }} *</label><input type="text" name="company_name" value="{{ old('company_name', $provider->company_name) }}" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500"></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'اسم المسؤول' : 'Contact Person' }} *</label><input type="text" name="contact_person" value="{{ old('contact_person', $provider->contact_person) }}" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'رقم الواتساب' : 'WhatsApp' }}</label><input type="tel" name="phone" value="{{ old('phone', $provider->phone) }}" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500"></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('email') }}</label><input type="email" name="email" value="{{ old('email', $provider->email) }}" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'نوع العمولة' : 'Commission Type' }}</label><select name="commission_type" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"><option value="fixed" {{ $provider->commission_type === 'fixed' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'مبلغ ثابت' : 'Fixed' }}</option><option value="percentage" {{ $provider->commission_type === 'percentage' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'نسبة مئوية' : 'Percentage' }}</option></select></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'قيمة العمولة' : 'Commission Value' }}</label><input type="number" name="commission_value" value="{{ old('commission_value', $provider->commission_value) }}" step="0.01" min="0" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('status') }}</label><select name="status" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"><option value="active" {{ $provider->status === 'active' ? 'selected' : '' }}>{{ __('active') }}</option><option value="suspended" {{ $provider->status === 'suspended' ? 'selected' : '' }}>{{ __('suspended') }}</option><option value="inactive" {{ $provider->status === 'inactive' ? 'selected' : '' }}>{{ __('inactive') }}</option></select></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'الشعار' : 'Logo' }}</label>@if($provider->logo)<div class="mb-1"><img src="{{ asset('storage/' . $provider->logo) }}" class="w-10 h-10 rounded object-contain"></div>@endif<input type="file" name="logo" accept="image/*" class="w-full text-sm file:bg-primary-600 file:text-white file:border-0 file:rounded-lg file:px-3 file:py-1.5 file:cursor-pointer file:text-xs"></div>
            </div>
            <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 rounded-lg transition shadow-lg"><i class="fas fa-save {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ __('save') }}</button>
        </form>
    </div>
</div>
@endsection
