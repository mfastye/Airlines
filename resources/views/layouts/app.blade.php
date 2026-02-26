<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="{{ session('dark_mode') ? 'dark' : '' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('site_name')) - {{ __('site_name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @if(app()->getLocale() === 'ar')
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    @else
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @endif
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: { 50:'#fdf2f4', 100:'#fce7ea', 200:'#f9d0d9', 300:'#f4a9b8', 400:'#ed7a93', 500:'#e04d6f', 600:'#7a1230', 700:'#6b102a', 800:'#5a0f25', 900:'#4d0e22' },
                        accent: { 50:'#fefce8', 100:'#fef9c3', 200:'#fef08a', 300:'#fde047', 400:'#facc15', 500:'#d4a017', 600:'#b8860b', 700:'#a16207', 800:'#854d0e', 900:'#713f12' },
                    },
                    fontFamily: {
                        sans: ["{{ app()->getLocale() === 'ar' ? 'Tajawal' : 'Inter' }}", 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        [dir="rtl"] { text-align: right; }
        .gradient-primary { background: linear-gradient(135deg, #7a1230 0%, #4d0e22 100%); }
        .gradient-gold { background: linear-gradient(135deg, #d4a017 0%, #b8860b 100%); }
        .glass { background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.15); }
        .dark .glass { background: rgba(0,0,0,0.3); }
        .animate-fade-in { animation: fadeIn 0.5s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .btn-primary { @apply bg-primary-600 hover:bg-primary-700 text-white font-semibold py-2.5 px-6 rounded-lg transition-all duration-300 shadow-md hover:shadow-lg; }
        .btn-gold { @apply bg-accent-600 hover:bg-accent-700 text-white font-semibold py-2.5 px-6 rounded-lg transition-all duration-300 shadow-md hover:shadow-lg; }
    </style>
    @yield('styles')
</head>
<body class="font-sans bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 min-h-screen">
    {{-- Navigation --}}
    <nav class="gradient-primary text-white shadow-xl sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                    <i class="fas fa-plane-departure text-accent-400 text-2xl"></i>
                    <span class="text-xl font-bold text-white">{{ __('site_name') }}</span>
                </a>

                {{-- Navigation Links --}}
                <div class="hidden md:flex items-center space-x-4 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                    <a href="{{ route('home') }}" class="hover:text-accent-400 transition px-3 py-2 text-sm font-medium">
                        <i class="fas fa-home {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ __('home') }}
                    </a>

                    @auth
                        @if(auth()->user()->isAdmin() || auth()->user()->isEmployee())
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-accent-400 transition px-3 py-2 text-sm font-medium">
                            <i class="fas fa-tachometer-alt {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ __('admin_panel') }}
                        </a>
                        @elseif(auth()->user()->isProvider())
                        <a href="{{ route('provider.dashboard') }}" class="hover:text-accent-400 transition px-3 py-2 text-sm font-medium">
                            <i class="fas fa-building {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ __('dashboard') }}
                        </a>
                        @elseif(auth()->user()->isAgent())
                        <a href="{{ route('agent.dashboard') }}" class="hover:text-accent-400 transition px-3 py-2 text-sm font-medium">
                            <i class="fas fa-user-tie {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ __('dashboard') }}
                        </a>
                        @else
                        <a href="{{ route('booking.my-bookings') }}" class="hover:text-accent-400 transition px-3 py-2 text-sm font-medium">
                            <i class="fas fa-ticket-alt {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ __('my_bookings') }}
                        </a>
                        @endif
                    @endauth
                </div>

                {{-- Right Side --}}
                <div class="flex items-center space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                    {{-- Language Switcher --}}
                    <form action="{{ route('language.set') }}" method="POST" class="inline">
                        @csrf
                        @if(app()->getLocale() === 'ar')
                        <input type="hidden" name="lang" value="en">
                        <button type="submit" class="text-xs bg-white/20 hover:bg-white/30 px-3 py-1.5 rounded-full transition">EN</button>
                        @else
                        <input type="hidden" name="lang" value="ar">
                        <button type="submit" class="text-xs bg-white/20 hover:bg-white/30 px-3 py-1.5 rounded-full transition">عربي</button>
                        @endif
                    </form>

                    {{-- Dark Mode Toggle --}}
                    <form action="{{ route('dark-mode.toggle') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-lg hover:text-accent-400 transition p-1">
                            @if(session('dark_mode'))
                            <i class="fas fa-sun"></i>
                            @else
                            <i class="fas fa-moon"></i>
                            @endif
                        </button>
                    </form>

                    {{-- Auth Links --}}
                    @auth
                    <div class="relative group">
                        <button class="flex items-center space-x-1 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }} hover:text-accent-400 transition">
                            <i class="fas fa-user-circle text-lg"></i>
                            <span class="text-sm hidden sm:inline">{{ auth()->user()->name }}</span>
                        </button>
                        <div class="absolute {{ app()->getLocale() === 'ar' ? 'left-0' : 'right-0' }} mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-xl py-2 hidden group-hover:block z-50">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <i class="fas fa-sign-out-alt {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ __('logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                    @else
                    <a href="{{ route('login') }}" class="bg-accent-600 hover:bg-accent-700 text-white text-sm px-4 py-2 rounded-lg transition shadow">
                        <i class="fas fa-sign-in-alt {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ __('login') }}
                    </a>
                    @endauth

                    {{-- Mobile Menu Button --}}
                    <button id="mobile-menu-btn" class="md:hidden text-white hover:text-accent-400">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div id="mobile-menu" class="hidden md:hidden bg-primary-800 pb-4">
            <div class="px-4 space-y-2">
                <a href="{{ route('home') }}" class="block py-2 text-sm hover:text-accent-400">{{ __('home') }}</a>
                @auth
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="block py-2 text-sm hover:text-accent-400">{{ __('admin_panel') }}</a>
                    @elseif(auth()->user()->isProvider())
                    <a href="{{ route('provider.dashboard') }}" class="block py-2 text-sm hover:text-accent-400">{{ __('dashboard') }}</a>
                    @elseif(auth()->user()->isAgent())
                    <a href="{{ route('agent.dashboard') }}" class="block py-2 text-sm hover:text-accent-400">{{ __('dashboard') }}</a>
                    @endif
                @endauth
            </div>
        </div>
    </nav>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="max-w-7xl mx-auto px-4 mt-4">
        <div class="bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg animate-fade-in" id="flash-success">
            <div class="flex items-center justify-between">
                <span><i class="fas fa-check-circle {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ session('success') }}</span>
                <button onclick="document.getElementById('flash-success').remove()" class="text-green-700 dark:text-green-400 hover:text-green-900"><i class="fas fa-times"></i></button>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="max-w-7xl mx-auto px-4 mt-4">
        <div class="bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg animate-fade-in" id="flash-error">
            <div class="flex items-center justify-between">
                <span><i class="fas fa-exclamation-circle {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ session('error') }}</span>
                <button onclick="document.getElementById('flash-error').remove()" class="text-red-700 dark:text-red-400 hover:text-red-900"><i class="fas fa-times"></i></button>
            </div>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="max-w-7xl mx-auto px-4 mt-4">
        <div class="bg-red-100 dark:bg-red-900/30 border border-red-400 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg">
            <ul class="list-disc {{ app()->getLocale() === 'ar' ? 'list-inside pr-4' : 'list-inside pl-4' }}">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="gradient-primary text-white mt-16">
        <div class="max-w-7xl mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }} mb-4">
                        <i class="fas fa-plane-departure text-accent-400 text-2xl"></i>
                        <span class="text-xl font-bold">{{ __('site_name') }}</span>
                    </div>
                    <p class="text-gray-300 text-sm leading-relaxed">
                        {{ app()->getLocale() === 'ar' ? 'نظام حجز تذاكر الطيران الذكي - نوفر لك أفضل الرحلات بأفضل الأسعار مع خدمة عملاء متميزة على مدار الساعة.' : 'Smart Flight Booking System - We provide you with the best flights at the best prices with excellent 24/7 customer service.' }}
                    </p>
                </div>
                <div>
                    <h3 class="text-accent-400 font-semibold mb-4">{{ app()->getLocale() === 'ar' ? 'روابط سريعة' : 'Quick Links' }}</h3>
                    <ul class="space-y-2 text-sm text-gray-300">
                        <li><a href="{{ route('home') }}" class="hover:text-accent-400 transition">{{ __('home') }}</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-accent-400 transition">{{ __('login') }}</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-accent-400 font-semibold mb-4">{{ app()->getLocale() === 'ar' ? 'تواصل معنا' : 'Contact Us' }}</h3>
                    <ul class="space-y-2 text-sm text-gray-300">
                        <li><i class="fas fa-envelope {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>support@skybooking.com</li>
                        <li><i class="fab fa-whatsapp {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>+1 234 567 8900</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-white/20 mt-8 pt-6 text-center text-sm text-gray-400">
                &copy; {{ date('Y') }} {{ __('site_name') }}. {{ app()->getLocale() === 'ar' ? 'جميع الحقوق محفوظة.' : 'All rights reserved.' }}
            </div>
        </div>
    </footer>

    <script>
        // Mobile Menu Toggle
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });

        // Auto-hide flash messages
        setTimeout(() => {
            document.getElementById('flash-success')?.remove();
            document.getElementById('flash-error')?.remove();
        }, 5000);
    </script>
    @yield('scripts')
</body>
</html>
