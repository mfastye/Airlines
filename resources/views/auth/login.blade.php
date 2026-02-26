@extends('layouts.app')
@section('title', __('login'))
@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden">
            <div class="gradient-primary p-6 text-center">
                <i class="fas fa-briefcase text-accent-400 text-3xl mb-2"></i>
                <h2 class="text-xl font-bold text-white">{{ app()->getLocale() === 'ar' ? 'بوابة الأعمال' : 'Business Portal' }}</h2>
                <p class="text-gray-300 text-sm">{{ app()->getLocale() === 'ar' ? 'تسجيل دخول المدراء والمزودين والوكلاء' : 'Login for Admins, Providers & Agents' }}</p>
            </div>
            <div class="p-8">
                <form action="{{ route('login.submit') }}" method="POST">
                    @csrf
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('username') }}</label>
                        <div class="relative">
                            <i class="fas fa-user absolute top-3 {{ app()->getLocale() === 'ar' ? 'right-3' : 'left-3' }} text-gray-400"></i>
                            <input type="text" name="username" value="{{ old('username') }}" required autofocus
                                class="w-full {{ app()->getLocale() === 'ar' ? 'pr-10 pl-3' : 'pl-10 pr-3' }} py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('password') }}</label>
                        <div class="relative">
                            <i class="fas fa-lock absolute top-3 {{ app()->getLocale() === 'ar' ? 'right-3' : 'left-3' }} text-gray-400"></i>
                            <input type="password" name="password" required
                                class="w-full {{ app()->getLocale() === 'ar' ? 'pr-10 pl-3' : 'pl-10 pr-3' }} py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>
                    <div class="flex items-center justify-between mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="rounded text-primary-600 focus:ring-primary-500">
                            <span class="text-sm text-gray-600 dark:text-gray-400 {{ app()->getLocale() === 'ar' ? 'mr-2' : 'ml-2' }}">{{ __('remember_me') }}</span>
                        </label>
                    </div>
                    <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 rounded-lg transition shadow-lg">
                        <i class="fas fa-sign-in-alt {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ __('login') }}
                    </button>
                </form>
            </div>
        </div>
        <div class="text-center mt-6">
            <a href="{{ route('customer.login') }}" class="text-primary-600 dark:text-primary-400 hover:underline text-sm">
                {{ app()->getLocale() === 'ar' ? 'تسجيل دخول العملاء' : 'Customer Login' }} <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} text-xs"></i>
            </a>
        </div>
    </div>
</div>
@endsection
