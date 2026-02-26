@extends('layouts.app')
@section('title', __('home'))
@section('content')
{{-- Hero Section with Search --}}
<section class="gradient-primary relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <svg viewBox="0 0 1200 600" class="w-full h-full"><path d="M0,300 Q300,100 600,300 T1200,300" fill="none" stroke="white" stroke-width="2"/><circle cx="200" cy="200" r="100" fill="white" opacity="0.05"/><circle cx="900" cy="150" r="150" fill="white" opacity="0.03"/></svg>
    </div>
    <div class="max-w-7xl mx-auto px-4 py-12 relative z-10">
        <div class="text-center mb-8">
            <h1 class="text-3xl md:text-5xl font-bold text-white mb-3">
                {{ app()->getLocale() === 'ar' ? 'احجز رحلتك بذكاء' : 'Book Your Flight Smart' }}
            </h1>
            <p class="text-gray-300 text-lg">
                {{ app()->getLocale() === 'ar' ? 'ابحث وقارن واحجز أفضل الرحلات بأفضل الأسعار' : 'Search, compare and book the best flights at the best prices' }}
            </p>
        </div>

        {{-- Search Form --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 md:p-8 max-w-5xl mx-auto">
            <form action="{{ route('search') }}" method="GET" id="search-form">
                {{-- Trip Type --}}
                <div class="flex items-center space-x-4 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }} mb-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="trip_type" value="one_way" checked class="text-primary-600 focus:ring-primary-500">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 {{ app()->getLocale() === 'ar' ? 'mr-2' : 'ml-2' }}">{{ __('one_way') }}</span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="trip_type" value="round_trip" class="text-primary-600 focus:ring-primary-500">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 {{ app()->getLocale() === 'ar' ? 'mr-2' : 'ml-2' }}">{{ __('round_trip') }}</span>
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    {{-- Departure Airport --}}
                    <div class="relative">
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase">{{ __('departure_airport') }}</label>
                        <div class="relative">
                            <i class="fas fa-plane-departure absolute top-3 {{ app()->getLocale() === 'ar' ? 'right-3' : 'left-3' }} text-primary-600"></i>
                            <input type="text" name="departure_display" id="departure-input" autocomplete="off"
                                class="w-full {{ app()->getLocale() === 'ar' ? 'pr-10 pl-3' : 'pl-10 pr-3' }} py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm"
                                placeholder="{{ app()->getLocale() === 'ar' ? 'اكتب اسم المطار...' : 'Type airport name...' }}">
                            <input type="hidden" name="departure" id="departure-id">
                        </div>
                        <div id="departure-results" class="absolute z-50 w-full bg-white dark:bg-gray-800 rounded-lg shadow-xl mt-1 hidden max-h-60 overflow-y-auto"></div>
                    </div>

                    {{-- Arrival Airport --}}
                    <div class="relative">
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase">{{ __('arrival_airport') }}</label>
                        <div class="relative">
                            <i class="fas fa-plane-arrival absolute top-3 {{ app()->getLocale() === 'ar' ? 'right-3' : 'left-3' }} text-primary-600"></i>
                            <input type="text" name="arrival_display" id="arrival-input" autocomplete="off"
                                class="w-full {{ app()->getLocale() === 'ar' ? 'pr-10 pl-3' : 'pl-10 pr-3' }} py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm"
                                placeholder="{{ app()->getLocale() === 'ar' ? 'اكتب اسم المطار...' : 'Type airport name...' }}">
                            <input type="hidden" name="arrival" id="arrival-id">
                        </div>
                        <div id="arrival-results" class="absolute z-50 w-full bg-white dark:bg-gray-800 rounded-lg shadow-xl mt-1 hidden max-h-60 overflow-y-auto"></div>
                    </div>

                    {{-- Departure Date --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase">{{ __('departure_date') }}</label>
                        <div class="relative">
                            <i class="fas fa-calendar-alt absolute top-3 {{ app()->getLocale() === 'ar' ? 'right-3' : 'left-3' }} text-primary-600"></i>
                            <input type="date" name="departure_date" required min="{{ date('Y-m-d') }}"
                                class="w-full {{ app()->getLocale() === 'ar' ? 'pr-10 pl-3' : 'pl-10 pr-3' }} py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm">
                        </div>
                    </div>

                    {{-- Return Date --}}
                    <div id="return-date-field" class="hidden">
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase">{{ __('return_date') }}</label>
                        <div class="relative">
                            <i class="fas fa-calendar-alt absolute top-3 {{ app()->getLocale() === 'ar' ? 'right-3' : 'left-3' }} text-primary-600"></i>
                            <input type="date" name="return_date" min="{{ date('Y-m-d') }}"
                                class="w-full {{ app()->getLocale() === 'ar' ? 'pr-10 pl-3' : 'pl-10 pr-3' }} py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    {{-- Adults --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase">{{ __('adults') }}</label>
                        <select name="adults" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm">
                            @for($i = 1; $i <= 9; $i++)<option value="{{ $i }}">{{ $i }}</option>@endfor
                        </select>
                    </div>
                    {{-- Children --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase">{{ __('children') }}</label>
                        <select name="children" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm">
                            @for($i = 0; $i <= 6; $i++)<option value="{{ $i }}">{{ $i }}</option>@endfor
                        </select>
                    </div>
                    {{-- Travel Class --}}
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase">{{ __('travel_class') }}</label>
                        <select name="class" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm">
                            <option value="economy">{{ __('economy') }}</option>
                            <option value="business">{{ __('business') }}</option>
                            <option value="first">{{ __('first_class') }}</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3.5 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl text-lg">
                    <i class="fas fa-search {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ __('search_flights') }}
                </button>
            </form>
        </div>
    </div>
</section>

{{-- Advertisements Banner --}}
@if(isset($advertisements) && $advertisements->count() > 0)
<section class="max-w-7xl mx-auto px-4 py-8">
    <div class="relative overflow-hidden rounded-2xl shadow-lg" id="ad-carousel">
        @foreach($advertisements as $index => $ad)
        <div class="ad-slide {{ $index > 0 ? 'hidden' : '' }} transition-opacity duration-500">
            @if($ad->url)
            <a href="{{ route('ad.click', $ad) }}" target="_blank" class="block">
            @endif
                <div class="relative">
                    @if($ad->image)
                    <img src="{{ asset('storage/' . $ad->image) }}" alt="{{ $ad->title }}" class="w-full h-48 md:h-64 object-cover">
                    @else
                    <div class="w-full h-48 md:h-64 gradient-primary flex items-center justify-center">
                        <span class="text-white text-2xl font-bold">{{ $ad->title }}</span>
                    </div>
                    @endif
                    @if($ad->description)
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4">
                        <p class="text-white text-sm md:text-base">{{ $ad->description }}</p>
                    </div>
                    @endif
                </div>
            @if($ad->url)
            </a>
            @endif
        </div>
        @endforeach
    </div>
</section>
@endif

{{-- Popular Routes Section --}}
@if(isset($popularRoutes) && count($popularRoutes) > 0)
<section class="max-w-7xl mx-auto px-4 py-12">
    <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6 text-center">
        {{ app()->getLocale() === 'ar' ? 'الوجهات الشائعة' : 'Popular Destinations' }}
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($popularRoutes as $route)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden card-hover">
            <div class="gradient-primary p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-lg font-bold">{{ $route->departureAirport->code ?? '' }}</span>
                        <i class="fas fa-long-arrow-alt-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} mx-2"></i>
                        <span class="text-lg font-bold">{{ $route->arrivalAirport->code ?? '' }}</span>
                    </div>
                    <span class="text-accent-400 font-bold">${{ number_format($route->seats->min('price') ?? 0) }}</span>
                </div>
            </div>
            <div class="p-4">
                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                    <span>{{ app()->getLocale() === 'ar' ? ($route->departureAirport->city_ar ?? '') : ($route->departureAirport->city_en ?? '') }}</span>
                    <span>{{ app()->getLocale() === 'ar' ? ($route->arrivalAirport->city_ar ?? '') : ($route->arrivalAirport->city_en ?? '') }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>
@endif

{{-- Features Section --}}
<section class="bg-gray-100 dark:bg-gray-800 py-16">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center p-6">
                <div class="w-16 h-16 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-robot text-2xl text-primary-600"></i>
                </div>
                <h3 class="text-lg font-semibold mb-2 dark:text-white">{{ app()->getLocale() === 'ar' ? 'بحث ذكي بالذكاء الاصطناعي' : 'AI Smart Search' }}</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">{{ app()->getLocale() === 'ar' ? 'يقترح لك أقرب الرحلات المتاحة تلقائيا' : 'Automatically suggests the nearest available flights' }}</p>
            </div>
            <div class="text-center p-6">
                <div class="w-16 h-16 bg-accent-100 dark:bg-accent-900 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-passport text-2xl text-accent-600"></i>
                </div>
                <h3 class="text-lg font-semibold mb-2 dark:text-white">{{ app()->getLocale() === 'ar' ? 'قارئ الجوازات الذكي' : 'Smart Passport Reader' }}</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">{{ app()->getLocale() === 'ar' ? 'استخراج بيانات الجواز تلقائيا بالذكاء الاصطناعي' : 'Automatically extract passport data using AI' }}</p>
            </div>
            <div class="text-center p-6">
                <div class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fab fa-whatsapp text-2xl text-green-600"></i>
                </div>
                <h3 class="text-lg font-semibold mb-2 dark:text-white">{{ app()->getLocale() === 'ar' ? 'إشعارات واتساب فورية' : 'Instant WhatsApp Notifications' }}</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">{{ app()->getLocale() === 'ar' ? 'استلم تأكيد الحجز وتحديثاته عبر واتساب' : 'Receive booking confirmations and updates via WhatsApp' }}</p>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    // Trip type toggle
    document.querySelectorAll('input[name="trip_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.getElementById('return-date-field').classList.toggle('hidden', this.value !== 'round_trip');
        });
    });

    // Airport autocomplete
    function setupAutocomplete(inputId, resultsId, hiddenId) {
        const input = document.getElementById(inputId);
        const results = document.getElementById(resultsId);
        const hidden = document.getElementById(hiddenId);
        let debounce;

        input.addEventListener('input', function() {
            clearTimeout(debounce);
            const q = this.value.trim();
            if (q.length < 2) { results.classList.add('hidden'); return; }

            debounce = setTimeout(() => {
                fetch(`{{ route('airports.search') }}?q=${encodeURIComponent(q)}`)
                    .then(r => r.json())
                    .then(data => {
                        if (data.length === 0) { results.classList.add('hidden'); return; }
                        results.innerHTML = '';
                        data.forEach(airport => {
                            const name = '{{ app()->getLocale() }}' === 'ar' ? airport.name_ar : airport.name_en;
                            const city = '{{ app()->getLocale() }}' === 'ar' ? airport.city_ar : airport.city_en;
                            const div = document.createElement('div');
                            div.className = 'px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer border-b border-gray-100 dark:border-gray-700 last:border-0';
                            div.innerHTML = `<div class="font-semibold text-sm text-gray-800 dark:text-gray-200">${airport.code} - ${name}</div><div class="text-xs text-gray-500">${city}, ${airport.country_en || ''}</div>`;
                            div.addEventListener('click', () => {
                                input.value = `${airport.code} - ${name}`;
                                hidden.value = airport.id;
                                results.classList.add('hidden');
                            });
                            results.appendChild(div);
                        });
                        results.classList.remove('hidden');
                    });
            }, 300);
        });

        document.addEventListener('click', e => {
            if (!input.contains(e.target) && !results.contains(e.target)) results.classList.add('hidden');
        });
    }

    setupAutocomplete('departure-input', 'departure-results', 'departure-id');
    setupAutocomplete('arrival-input', 'arrival-results', 'arrival-id');

    // Ad carousel
    const slides = document.querySelectorAll('.ad-slide');
    if (slides.length > 1) {
        let current = 0;
        setInterval(() => {
            slides[current].classList.add('hidden');
            current = (current + 1) % slides.length;
            slides[current].classList.remove('hidden');
        }, 5000);
    }
</script>
@endsection
