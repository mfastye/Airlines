@extends('layouts.admin')
@section('title', __('add_new') . ' - ' . __('manage_providers'))
@section('content')
<div class="max-w-3xl">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.providers.index') }}" class="text-gray-500 hover:text-primary-600 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"><i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i></a>
        <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ __('add_new') }} {{ app()->getLocale() === 'ar' ? 'مزود' : 'Provider' }}</h2>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <form action="{{ route('admin.providers.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <h3 class="font-semibold dark:text-white mb-4 text-primary-600"><i class="fas fa-building {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ app()->getLocale() === 'ar' ? 'البيانات الأساسية' : 'Basic Info' }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'الاسم التجاري' : 'Company Name' }} *</label><input type="text" name="company_name" value="{{ old('company_name') }}" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500"></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'اسم المسؤول' : 'Contact Person' }} *</label><input type="text" name="contact_person" value="{{ old('contact_person') }}" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'رقم الواتساب' : 'WhatsApp' }} *</label><input type="tel" name="phone" value="{{ old('phone') }}" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500"></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('email') }}</label><input type="email" name="email" value="{{ old('email') }}" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500"></div>
            </div>
            <div class="mb-4"><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'العنوان' : 'Address' }}</label><textarea name="address" rows="2" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500">{{ old('address') }}</textarea></div>
            <div class="mb-4"><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'الشعار' : 'Logo' }}</label><input type="file" name="logo" accept="image/*" class="w-full text-sm file:bg-primary-600 file:text-white file:border-0 file:rounded-lg file:px-4 file:py-2 file:cursor-pointer"></div>

            <h3 class="font-semibold dark:text-white mb-4 mt-6 text-primary-600 border-t border-gray-200 dark:border-gray-600 pt-4"><i class="fas fa-cog {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ app()->getLocale() === 'ar' ? 'البيانات التشغيلية' : 'Operations' }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'نوع العمولة' : 'Commission Type' }}</label><select name="commission_type" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"><option value="fixed">{{ app()->getLocale() === 'ar' ? 'مبلغ ثابت' : 'Fixed Amount' }}</option><option value="percentage">{{ app()->getLocale() === 'ar' ? 'نسبة مئوية' : 'Percentage' }}</option></select></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'قيمة العمولة' : 'Commission Value' }}</label><input type="number" name="commission_value" value="{{ old('commission_value', 0) }}" step="0.01" min="0" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500"></div>
            </div>
            <div class="mb-4"><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'شركات الطيران المسموحة' : 'Allowed Airlines' }}</label><select name="allowed_airlines[]" multiple class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm h-32">@foreach($airlines ?? [] as $airline)<option value="{{ $airline->id }}">{{ app()->getLocale() === 'ar' ? $airline->name_ar : $airline->name_en }}</option>@endforeach</select><p class="text-xs text-gray-500 mt-1">{{ app()->getLocale() === 'ar' ? 'اضغط Ctrl للاختيار المتعدد' : 'Hold Ctrl to select multiple' }}</p></div>

            <h3 class="font-semibold dark:text-white mb-4 mt-6 text-primary-600 border-t border-gray-200 dark:border-gray-600 pt-4"><i class="fas fa-key {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ app()->getLocale() === 'ar' ? 'بيانات الدخول' : 'Login Credentials' }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('username') }} *</label><input type="text" name="username" value="{{ old('username') }}" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500"></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('password') }} *</label><input type="password" name="password" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500"></div>
            </div>
            <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 rounded-lg transition shadow-lg"><i class="fas fa-save {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ __('save') }}</button>
        </form>
    </div>
</div>
@endsection
