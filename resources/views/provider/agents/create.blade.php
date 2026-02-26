@extends('layouts.admin')
@section('title', __('add_new') . ' - ' . __('manage_agents'))
@section('content')
<div class="max-w-2xl">
    <div class="flex items-center mb-6">
        <a href="{{ route('provider.agents.index') }}" class="text-gray-500 hover:text-primary-600 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"><i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i></a>
        <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ __('add_new') }} {{ app()->getLocale() === 'ar' ? 'وكيل' : 'Agent' }}</h2>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <form action="{{ route('provider.agents.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'الاسم التجاري' : 'Company Name' }} *</label><input type="text" name="company_name" value="{{ old('company_name') }}" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500"></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'اسم المسؤول' : 'Contact Person' }}</label><input type="text" name="contact_person" value="{{ old('contact_person') }}" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"></div>
                    <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'رقم الواتساب' : 'WhatsApp' }}</label><input type="tel" name="phone" value="{{ old('phone') }}" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"></div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'نوع العمولة' : 'Commission Type' }}</label><select name="commission_type" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"><option value="fixed">{{ app()->getLocale() === 'ar' ? 'مبلغ ثابت' : 'Fixed' }}</option><option value="percentage">{{ app()->getLocale() === 'ar' ? 'نسبة مئوية' : 'Percentage' }}</option></select></div>
                    <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'قيمة العمولة' : 'Commission Value' }}</label><input type="number" name="commission_value" value="{{ old('commission_value', 0) }}" step="0.01" min="0" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"></div>
                </div>
                <div><label class="flex items-center"><input type="checkbox" name="api_enabled" value="1" class="rounded text-primary-600 focus:ring-primary-500"><span class="text-sm text-gray-700 dark:text-gray-300 {{ app()->getLocale() === 'ar' ? 'mr-2' : 'ml-2' }}">{{ app()->getLocale() === 'ar' ? 'تفعيل API' : 'Enable API' }}</span></label></div>
                <div class="border-t border-gray-200 dark:border-gray-600 pt-4"><h4 class="font-medium dark:text-white mb-3">{{ app()->getLocale() === 'ar' ? 'بيانات الدخول' : 'Login Credentials' }}</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('username') }} *</label><input type="text" name="username" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"></div>
                        <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('password') }} *</label><input type="password" name="password" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"></div>
                    </div>
                </div>
            </div>
            <button type="submit" class="w-full mt-6 bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 rounded-lg transition shadow-lg"><i class="fas fa-save {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ __('save') }}</button>
        </form>
    </div>
</div>
@endsection
