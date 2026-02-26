@extends('layouts.admin')
@section('title', __('add_new') . ' - ' . __('manage_advertisements'))
@section('content')
<div class="max-w-2xl">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.advertisements.index') }}" class="text-gray-500 hover:text-primary-600 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"><i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i></a>
        <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ __('add_new') }} {{ app()->getLocale() === 'ar' ? 'إعلان' : 'Advertisement' }}</h2>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <form action="{{ route('admin.advertisements.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'العنوان' : 'Title' }} *</label><input type="text" name="title" value="{{ old('title') }}" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500"></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'الوصف' : 'Description' }}</label><textarea name="description" rows="3" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500">{{ old('description') }}</textarea></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'النوع' : 'Type' }}</label><select name="type" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"><option value="banner">{{ app()->getLocale() === 'ar' ? 'بانر الصفحة الرئيسية' : 'Homepage Banner' }}</option><option value="whatsapp">{{ app()->getLocale() === 'ar' ? 'إعلان واتساب' : 'WhatsApp Ad' }}</option></select></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'صورة الإعلان' : 'Ad Image' }} *</label><input type="file" name="image" accept="image/*" required class="w-full text-sm file:bg-primary-600 file:text-white file:border-0 file:rounded-lg file:px-4 file:py-2 file:cursor-pointer"></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'رابط URL (اختياري)' : 'URL Link (optional)' }}</label><input type="url" name="url" value="{{ old('url') }}" placeholder="https://..." class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500"></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'تاريخ البداية' : 'Start Date' }}</label><input type="date" name="start_date" value="{{ old('start_date') }}" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"></div>
                    <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'تاريخ النهاية' : 'End Date' }}</label><input type="date" name="end_date" value="{{ old('end_date') }}" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"></div>
                </div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('status') }}</label><select name="status" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"><option value="active">{{ __('active') }}</option><option value="inactive">{{ __('inactive') }}</option></select></div>
            </div>
            <button type="submit" class="w-full mt-6 bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 rounded-lg transition shadow-lg"><i class="fas fa-save {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ __('save') }}</button>
        </form>
    </div>
</div>
@endsection
