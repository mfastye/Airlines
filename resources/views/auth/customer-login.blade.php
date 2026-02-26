@extends('layouts.app')
@section('title', __('login'))
@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden">
            <div class="gradient-primary p-6 text-center">
                <i class="fas fa-user-circle text-accent-400 text-3xl mb-2"></i>
                <h2 class="text-xl font-bold text-white">{{ app()->getLocale() === 'ar' ? 'تسجيل دخول العملاء' : 'Customer Login' }}</h2>
            </div>
            <div class="p-8">
                {{-- Google Login --}}
                <a href="#" class="flex items-center justify-center w-full border border-gray-300 dark:border-gray-600 rounded-lg py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition mb-6">
                    <svg class="w-5 h-5 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                    <span class="text-gray-700 dark:text-gray-300 text-sm font-medium">{{ __('sign_in_with_google') }}</span>
                </a>

                <div class="relative mb-6">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-300 dark:border-gray-600"></div></div>
                    <div class="relative flex justify-center"><span class="bg-white dark:bg-gray-800 px-4 text-sm text-gray-500">{{ __('or') }}</span></div>
                </div>

                {{-- Registration Form --}}
                <form action="{{ route('customer.register') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('full_name') }}</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-primary-500 text-sm">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('email') }}</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-primary-500 text-sm">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('phone') }}</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-primary-500 text-sm">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('password') }}</label>
                        <input type="password" name="password" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-primary-500 text-sm">
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('confirm_password') }}</label>
                        <input type="password" name="password_confirmation" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-primary-500 text-sm">
                    </div>
                    <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 rounded-lg transition shadow-lg">
                        {{ __('create_account') }}
                    </button>
                </form>
            </div>
        </div>
        <div class="text-center mt-6">
            <a href="{{ route('login') }}" class="text-primary-600 dark:text-primary-400 hover:underline text-sm">
                {{ app()->getLocale() === 'ar' ? 'بوابة الأعمال' : 'Business Portal' }} <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} text-xs"></i>
            </a>
        </div>
    </div>
</div>
@endsection
