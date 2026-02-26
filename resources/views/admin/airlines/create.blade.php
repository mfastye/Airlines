@extends('layouts.admin')
@section('title', __('add_new') . ' - ' . __('manage_airlines'))
@section('content')
<div class="max-w-2xl">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.airlines.index') }}" class="text-gray-500 hover:text-primary-600 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"><i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i></a>
        <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ __('add_new') }} {{ __('airline') }}</h2>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <form action="{{ route('admin.airlines.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'الاسم بالإنجليزية' : 'Name (English)' }} *</label>
                    <input type="text" name="name_en" value="{{ old('name_en') }}" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'الاسم بالعربية' : 'Name (Arabic)' }} *</label>
                    <input type="text" name="name_ar" value="{{ old('name_ar') }}" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'رمز الطيران' : 'Airline Code' }} *</label>
                    <input type="text" name="code" value="{{ old('code') }}" required maxlength="3" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500 font-mono uppercase">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'البلد' : 'Country' }}</label>
                    <input type="text" name="country" value="{{ old('country') }}" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500">
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'الشعار' : 'Logo' }}</label>
                <input type="file" name="logo" accept="image/*" class="w-full text-sm file:bg-primary-600 file:text-white file:border-0 file:rounded-lg file:px-4 file:py-2 file:cursor-pointer">
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('status') }}</label>
                <select name="status" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm">
                    <option value="active">{{ __('active') }}</option>
                    <option value="inactive">{{ __('inactive') }}</option>
                </select>
            </div>
            <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 rounded-lg transition shadow-lg">
                <i class="fas fa-save {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ __('save') }}
            </button>
        </form>
    </div>
</div>
@endsection
