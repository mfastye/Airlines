<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="{{ session('dark_mode') ? 'dark' : '' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('admin_panel')) - {{ __('site_name') }}</title>
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
                    fontFamily: { sans: ["{{ app()->getLocale() === 'ar' ? 'Tajawal' : 'Inter' }}", 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        [dir="rtl"] { text-align: right; }
        .gradient-primary { background: linear-gradient(135deg, #7a1230 0%, #4d0e22 100%); }
        .sidebar-link { @apply flex items-center px-4 py-2.5 text-sm text-gray-300 hover:bg-white/10 hover:text-white rounded-lg transition-all duration-200; }
        .sidebar-link.active { @apply bg-white/20 text-white font-semibold; }
    </style>
    @yield('styles')
</head>
<body class="font-sans bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside id="sidebar" class="fixed inset-y-0 {{ app()->getLocale() === 'ar' ? 'right-0' : 'left-0' }} z-50 w-64 gradient-primary text-white transform transition-transform duration-300 lg:translate-x-0 {{ app()->getLocale() === 'ar' ? 'translate-x-full lg:translate-x-0' : '-translate-x-full lg:translate-x-0' }}">
            <div class="flex items-center justify-between h-16 px-4 border-b border-white/10">
                <a href="{{ route('home') }}" class="flex items-center space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                    <i class="fas fa-plane-departure text-accent-400 text-xl"></i>
                    <span class="font-bold text-lg">{{ __('site_name') }}</span>
                </a>
                <button id="close-sidebar" class="lg:hidden text-white hover:text-accent-400">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <nav class="mt-4 px-3 space-y-1 overflow-y-auto" style="max-height: calc(100vh - 4rem);">
                @php $prefix = request()->is('admin/*') ? 'admin' : (request()->is('provider/*') ? 'provider' : 'agent'); @endphp

                @if($prefix === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt w-5 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"></i>{{ __('dashboard') }}
                </a>
                <a href="{{ route('admin.airlines.index') }}" class="sidebar-link {{ request()->routeIs('admin.airlines.*') ? 'active' : '' }}">
                    <i class="fas fa-plane w-5 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"></i>{{ __('manage_airlines') }}
                </a>
                <a href="{{ route('admin.airports.index') }}" class="sidebar-link {{ request()->routeIs('admin.airports.*') ? 'active' : '' }}">
                    <i class="fas fa-map-marker-alt w-5 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"></i>{{ __('manage_airports') }}
                </a>
                <a href="{{ route('admin.flights.index') }}" class="sidebar-link {{ request()->routeIs('admin.flights.*') ? 'active' : '' }}">
                    <i class="fas fa-route w-5 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"></i>{{ __('manage_flights') }}
                </a>
                <a href="{{ route('admin.bookings.index') }}" class="sidebar-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
                    <i class="fas fa-ticket-alt w-5 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"></i>{{ __('manage_bookings') }}
                </a>
                <a href="{{ route('admin.providers.index') }}" class="sidebar-link {{ request()->routeIs('admin.providers.*') ? 'active' : '' }}">
                    <i class="fas fa-building w-5 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"></i>{{ __('manage_providers') }}
                </a>
                <a href="{{ route('admin.agents.index') }}" class="sidebar-link {{ request()->routeIs('admin.agents.*') ? 'active' : '' }}">
                    <i class="fas fa-user-tie w-5 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"></i>{{ __('manage_agents') }}
                </a>
                <a href="{{ route('admin.payments.index') }}" class="sidebar-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                    <i class="fas fa-credit-card w-5 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"></i>{{ __('payment') }}
                </a>
                <a href="{{ route('admin.payment-gateways.index') }}" class="sidebar-link {{ request()->routeIs('admin.payment-gateways.*') ? 'active' : '' }}">
                    <i class="fas fa-money-check-alt w-5 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"></i>{{ __('payment_gateways') }}
                </a>
                <a href="{{ route('admin.whatsapp.index') }}" class="sidebar-link {{ request()->routeIs('admin.whatsapp.*') ? 'active' : '' }}">
                    <i class="fab fa-whatsapp w-5 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"></i>{{ __('whatsapp_gateway') }}
                </a>
                <a href="{{ route('admin.advertisements.index') }}" class="sidebar-link {{ request()->routeIs('admin.advertisements.*') ? 'active' : '' }}">
                    <i class="fas fa-ad w-5 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"></i>{{ __('advertisements') }}
                </a>
                <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users w-5 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"></i>{{ __('users') }}
                </a>
                <a href="{{ route('admin.customers.index') }}" class="sidebar-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                    <i class="fas fa-user-friends w-5 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"></i>{{ __('customers') }}
                </a>
                <a href="{{ route('admin.financial.index') }}" class="sidebar-link {{ request()->routeIs('admin.financial.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-line w-5 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"></i>{{ __('financial') }}
                </a>
                <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="fas fa-cog w-5 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"></i>{{ __('settings') }}
                </a>

                @elseif($prefix === 'provider')
                <a href="{{ route('provider.dashboard') }}" class="sidebar-link {{ request()->routeIs('provider.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt w-5 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"></i>{{ __('dashboard') }}
                </a>
                <a href="{{ route('provider.flights.index') }}" class="sidebar-link {{ request()->routeIs('provider.flights.*') ? 'active' : '' }}">
                    <i class="fas fa-route w-5 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"></i>{{ __('manage_flights') }}
                </a>
                <a href="{{ route('provider.bookings.index') }}" class="sidebar-link {{ request()->routeIs('provider.bookings.*') ? 'active' : '' }}">
                    <i class="fas fa-ticket-alt w-5 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"></i>{{ __('manage_bookings') }}
                </a>
                <a href="{{ route('provider.agents.index') }}" class="sidebar-link {{ request()->routeIs('provider.agents.*') ? 'active' : '' }}">
                    <i class="fas fa-user-tie w-5 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"></i>{{ __('manage_agents') }}
                </a>
                <a href="{{ route('provider.financial') }}" class="sidebar-link {{ request()->routeIs('provider.financial') ? 'active' : '' }}">
                    <i class="fas fa-chart-line w-5 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"></i>{{ __('financial') }}
                </a>
                <a href="{{ route('provider.reports') }}" class="sidebar-link {{ request()->routeIs('provider.reports') ? 'active' : '' }}">
                    <i class="fas fa-file-alt w-5 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"></i>{{ __('reports') }}
                </a>

                @elseif($prefix === 'agent')
                <a href="{{ route('agent.dashboard') }}" class="sidebar-link {{ request()->routeIs('agent.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt w-5 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"></i>{{ __('dashboard') }}
                </a>
                <a href="{{ route('agent.bookings') }}" class="sidebar-link {{ request()->routeIs('agent.bookings') ? 'active' : '' }}">
                    <i class="fas fa-ticket-alt w-5 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"></i>{{ __('my_bookings') }}
                </a>
                <a href="{{ route('agent.financial') }}" class="sidebar-link {{ request()->routeIs('agent.financial') ? 'active' : '' }}">
                    <i class="fas fa-chart-line w-5 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"></i>{{ __('financial') }}
                </a>
                <a href="{{ route('agent.settings') }}" class="sidebar-link {{ request()->routeIs('agent.settings') ? 'active' : '' }}">
                    <i class="fas fa-cog w-5 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"></i>{{ __('settings') }}
                </a>
                @endif

                <div class="border-t border-white/10 my-4"></div>
                <form action="{{ route('language.set') }}" method="POST">
                    @csrf
                    <input type="hidden" name="lang" value="{{ app()->getLocale() === 'ar' ? 'en' : 'ar' }}">
                    <button type="submit" class="sidebar-link w-full">
                        <i class="fas fa-language w-5 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"></i>
                        {{ app()->getLocale() === 'ar' ? 'English' : 'العربية' }}
                    </button>
                </form>
                <form action="{{ route('dark-mode.toggle') }}" method="POST">
                    @csrf
                    <button type="submit" class="sidebar-link w-full">
                        <i class="fas {{ session('dark_mode') ? 'fa-sun' : 'fa-moon' }} w-5 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"></i>
                        {{ session('dark_mode') ? __('light_mode') : __('dark_mode') }}
                    </button>
                </form>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="sidebar-link w-full text-red-300 hover:text-red-100">
                        <i class="fas fa-sign-out-alt w-5 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"></i>{{ __('logout') }}
                    </button>
                </form>
            </nav>
        </aside>

        {{-- Main Content --}}
        <div class="{{ app()->getLocale() === 'ar' ? 'lg:mr-64' : 'lg:ml-64' }} flex-1 min-h-screen">
            {{-- Top Bar --}}
            <header class="bg-white dark:bg-gray-800 shadow-sm h-16 flex items-center justify-between px-6">
                <div class="flex items-center">
                    <button id="open-sidebar" class="lg:hidden text-gray-600 dark:text-gray-300 hover:text-primary-600 {{ app()->getLocale() === 'ar' ? 'ml-4' : 'mr-4' }}">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-lg font-semibold text-gray-800 dark:text-gray-200">@yield('title', __('dashboard'))</h1>
                </div>
                <div class="flex items-center space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ auth()->user()->name }}</span>
                    <span class="text-xs bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 px-2 py-1 rounded-full">{{ ucfirst(auth()->user()->role) }}</span>
                </div>
            </header>

            {{-- Flash Messages --}}
            @if(session('success'))
            <div class="mx-6 mt-4">
                <div class="bg-green-100 dark:bg-green-900/30 border border-green-400 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg" id="flash-msg">
                    <i class="fas fa-check-circle {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ session('success') }}
                </div>
            </div>
            @endif
            @if(session('error'))
            <div class="mx-6 mt-4">
                <div class="bg-red-100 dark:bg-red-900/30 border border-red-400 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg">
                    <i class="fas fa-exclamation-circle {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ session('error') }}
                </div>
            </div>
            @endif
            @if($errors->any())
            <div class="mx-6 mt-4">
                <div class="bg-red-100 dark:bg-red-900/30 border border-red-400 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg">
                    <ul class="list-disc {{ app()->getLocale() === 'ar' ? 'pr-4' : 'pl-4' }}">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            </div>
            @endif

            {{-- Page Content --}}
            <div class="p-6">
                @yield('content')
            </div>
        </div>
    </div>

    {{-- Sidebar Overlay --}}
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden"></div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const isRtl = document.documentElement.dir === 'rtl';

        document.getElementById('open-sidebar')?.addEventListener('click', () => {
            if (isRtl) { sidebar.classList.remove('translate-x-full'); }
            else { sidebar.classList.remove('-translate-x-full'); }
            overlay.classList.remove('hidden');
        });

        function closeSidebar() {
            if (isRtl) { sidebar.classList.add('translate-x-full'); }
            else { sidebar.classList.add('-translate-x-full'); }
            overlay.classList.add('hidden');
        }

        document.getElementById('close-sidebar')?.addEventListener('click', closeSidebar);
        overlay?.addEventListener('click', closeSidebar);

        setTimeout(() => document.getElementById('flash-msg')?.remove(), 5000);
    </script>
    @yield('scripts')
</body>
</html>
