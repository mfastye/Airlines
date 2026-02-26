@extends('layouts.admin')
@section('title', __('edit') . ' - ' . __('manage_airports'))
@section('content')
<div class="max-w-2xl">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.airports.index') }}" class="text-gray-500 hover:text-primary-600 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"><i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i></a>
        <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ __('edit') }} - {{ $airport->code }}</h2>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <form action="{{ route('admin.airports.update', $airport) }}" method="POST">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'رمز المطار' : 'Code' }} *</label><input type="text" name="code" value="{{ old('code', $airport->code) }}" required maxlength="3" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500 font-mono uppercase"></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('status') }}</label><select name="status" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"><option value="active" {{ $airport->status === 'active' ? 'selected' : '' }}>{{ __('active') }}</option><option value="inactive" {{ $airport->status === 'inactive' ? 'selected' : '' }}>{{ __('inactive') }}</option></select></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'الاسم بالإنجليزية' : 'Name (English)' }} *</label><input type="text" name="name_en" value="{{ old('name_en', $airport->name_en) }}" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500"></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'الاسم بالعربية' : 'Name (Arabic)' }} *</label><input type="text" name="name_ar" value="{{ old('name_ar', $airport->name_ar) }}" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'المدينة بالإنجليزية' : 'City (English)' }} *</label><input type="text" name="city_en" value="{{ old('city_en', $airport->city_en) }}" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500"></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'المدينة بالعربية' : 'City (Arabic)' }} *</label><input type="text" name="city_ar" value="{{ old('city_ar', $airport->city_ar) }}" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'البلد بالإنجليزية' : 'Country (English)' }} *</label><input type="text" name="country_en" value="{{ old('country_en', $airport->country_en) }}" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500"></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'البلد بالعربية' : 'Country (Arabic)' }} *</label><input type="text" name="country_ar" value="{{ old('country_ar', $airport->country_ar) }}" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500"></div>
            </div>
            <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 rounded-lg transition shadow-lg"><i class="fas fa-save {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ __('save') }}</button>
        </form>
    </div>
</div>
@endsection
