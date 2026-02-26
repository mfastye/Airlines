@extends('layouts.admin')
@section('title', __('settings'))
@section('content')
<h2 class="text-xl font-bold text-gray-800 dark:text-white mb-6">{{ __('settings') }}</h2>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <h3 class="font-semibold dark:text-white mb-4 text-primary-600"><i class="fas fa-globe {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ app()->getLocale() === 'ar' ? 'الإعدادات العامة' : 'General Settings' }}</h3>
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'اسم النظام' : 'System Name' }}</label><input type="text" name="system_name" value="{{ $settings['system_name'] ?? 'SkyBooker' }}" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500"></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'شعار النظام' : 'System Logo' }}</label><input type="file" name="logo" accept="image/*" class="w-full text-sm file:bg-primary-600 file:text-white file:border-0 file:rounded-lg file:px-3 file:py-1.5 file:cursor-pointer file:text-xs"></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'اللغة الافتراضية' : 'Default Language' }}</label><select name="default_locale" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"><option value="ar" {{ ($settings['default_locale'] ?? 'ar') === 'ar' ? 'selected' : '' }}>العربية</option><option value="en" {{ ($settings['default_locale'] ?? '') === 'en' ? 'selected' : '' }}>English</option></select></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'العملة' : 'Currency' }}</label><select name="currency" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"><option value="USD" {{ ($settings['currency'] ?? 'USD') === 'USD' ? 'selected' : '' }}>USD - {{ app()->getLocale() === 'ar' ? 'دولار' : 'Dollar' }}</option><option value="SAR" {{ ($settings['currency'] ?? '') === 'SAR' ? 'selected' : '' }}>SAR - {{ app()->getLocale() === 'ar' ? 'ريال' : 'Riyal' }}</option><option value="EUR" {{ ($settings['currency'] ?? '') === 'EUR' ? 'selected' : '' }}>EUR - {{ app()->getLocale() === 'ar' ? 'يورو' : 'Euro' }}</option></select></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'المنطقة الزمنية' : 'Timezone' }}</label><select name="timezone" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"><option value="Asia/Riyadh">Asia/Riyadh (UTC+3)</option><option value="Asia/Dubai">Asia/Dubai (UTC+4)</option><option value="UTC">UTC</option></select></div>
            </div>
            <button type="submit" class="w-full mt-4 bg-primary-600 hover:bg-primary-700 text-white font-bold py-2.5 rounded-lg transition"><i class="fas fa-save {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ __('save') }}</button>
        </form>
    </div>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h3 class="font-semibold dark:text-white mb-4 text-primary-600"><i class="fas fa-envelope {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ app()->getLocale() === 'ar' ? 'إعدادات البريد' : 'Email Settings' }}</h3>
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SMTP Host</label><input type="text" name="mail_host" value="{{ $settings['mail_host'] ?? '' }}" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500"></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SMTP Port</label><input type="number" name="mail_port" value="{{ $settings['mail_port'] ?? 587 }}" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"></div>
                        <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'التشفير' : 'Encryption' }}</label><select name="mail_encryption" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"><option value="tls">TLS</option><option value="ssl">SSL</option></select></div>
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'اسم المستخدم' : 'Username' }}</label><input type="text" name="mail_username" value="{{ $settings['mail_username'] ?? '' }}" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"></div>
                    <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('password') }}</label><input type="password" name="mail_password" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"></div>
                </div>
                <button type="submit" class="w-full mt-4 bg-primary-600 hover:bg-primary-700 text-white font-bold py-2.5 rounded-lg transition"><i class="fas fa-save {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ __('save') }}</button>
            </form>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h3 class="font-semibold dark:text-white mb-4 text-primary-600"><i class="fas fa-database {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ app()->getLocale() === 'ar' ? 'النسخ الاحتياطي' : 'Backup' }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ app()->getLocale() === 'ar' ? 'إنشاء نسخة احتياطية من قاعدة البيانات' : 'Create a backup of the database' }}</p>
            <form action="{{ route('admin.settings.backup') }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 rounded-lg transition"><i class="fas fa-download {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ app()->getLocale() === 'ar' ? 'تحميل النسخة الاحتياطية' : 'Download Backup' }}</button>
            </form>
        </div>
    </div>
</div>
@endsection
