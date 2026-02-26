@extends('layouts.app')
@section('title', __('passenger_details'))
@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    {{-- Progress Steps --}}
    <div class="flex items-center justify-center mb-8">
        <div class="flex items-center space-x-4 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <div class="flex items-center"><div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-bold">1</div><span class="text-sm font-medium text-green-600 {{ app()->getLocale() === 'ar' ? 'mr-2' : 'ml-2' }}">{{ __('select_flight') }}</span></div>
            <div class="w-12 h-px bg-gray-300"></div>
            <div class="flex items-center"><div class="w-8 h-8 bg-primary-600 text-white rounded-full flex items-center justify-center text-sm font-bold">2</div><span class="text-sm font-medium text-primary-600 {{ app()->getLocale() === 'ar' ? 'mr-2' : 'ml-2' }}">{{ __('passenger_details') }}</span></div>
            <div class="w-12 h-px bg-gray-300"></div>
            <div class="flex items-center"><div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center text-sm font-bold">3</div><span class="text-sm text-gray-500 {{ app()->getLocale() === 'ar' ? 'mr-2' : 'ml-2' }}">{{ __('payment') }}</span></div>
        </div>
    </div>

    {{-- Flight Summary --}}
    @if(isset($flight))
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 mb-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                <i class="fas fa-plane text-primary-600 text-xl"></i>
                <div>
                    <span class="font-bold text-gray-800 dark:text-white">{{ $flight->flight_number }}</span>
                    <span class="text-sm text-gray-500 {{ app()->getLocale() === 'ar' ? 'mr-2' : 'ml-2' }}">{{ app()->getLocale() === 'ar' ? ($flight->airline->name_ar ?? '') : ($flight->airline->name_en ?? '') }}</span>
                </div>
            </div>
            <div class="text-sm text-gray-600 dark:text-gray-400">
                {{ $flight->departureAirport->code ?? '' }} <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} mx-2 text-primary-600"></i> {{ $flight->arrivalAirport->code ?? '' }}
                <span class="{{ app()->getLocale() === 'ar' ? 'mr-3' : 'ml-3' }}">{{ \Carbon\Carbon::parse($flight->departure_time)->format('d M Y, H:i') }}</span>
            </div>
            <div class="text-lg font-bold text-primary-600">${{ number_format(($seat->price ?? 0) * $totalPassengers, 2) }}</div>
        </div>
    </div>
    @endif

    {{-- Passenger Forms --}}
    <form action="{{ route('booking.store-passengers') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @for($i = 0; $i < ($totalPassengers ?? 1); $i++)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6 animate-fade-in">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center">
                <i class="fas fa-user text-primary-600 {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                {{ __('passenger') }} {{ $i + 1 }}
            </h3>

            {{-- Passport Upload with OCR --}}
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                <h4 class="text-sm font-semibold text-blue-800 dark:text-blue-300 mb-2">
                    <i class="fas fa-passport {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ __('smart_passport_reader') }}
                </h4>
                <p class="text-xs text-blue-600 dark:text-blue-400 mb-3">{{ app()->getLocale() === 'ar' ? 'ارفع صورة الجواز وسيتم استخراج البيانات تلقائيا' : 'Upload passport image and data will be extracted automatically' }}</p>
                <input type="file" name="passport_image_{{ $i }}" accept="image/*" class="text-sm file:bg-primary-600 file:text-white file:border-0 file:rounded-lg file:px-4 file:py-2 file:cursor-pointer file:hover:bg-primary-700 file:transition" onchange="uploadPassport(this, {{ $i }})">
                <div id="ocr-status-{{ $i }}" class="mt-2 text-sm hidden"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('first_name') }} *</label>
                    <input type="text" name="passengers[{{ $i }}][first_name]" id="first_name_{{ $i }}" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('last_name') }} *</label>
                    <input type="text" name="passengers[{{ $i }}][last_name]" id="last_name_{{ $i }}" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('passport_number') }} *</label>
                    <input type="text" name="passengers[{{ $i }}][passport_number]" id="passport_number_{{ $i }}" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('nationality') }} *</label>
                    <input type="text" name="passengers[{{ $i }}][nationality]" id="nationality_{{ $i }}" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('date_of_birth') }} *</label>
                    <input type="date" name="passengers[{{ $i }}][date_of_birth]" id="dob_{{ $i }}" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('gender') }} *</label>
                    <select name="passengers[{{ $i }}][gender]" id="gender_{{ $i }}" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500">
                        <option value="male">{{ __('male') }}</option>
                        <option value="female">{{ __('female') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('passport_expiry') }} *</label>
                    <input type="date" name="passengers[{{ $i }}][passport_expiry]" id="passport_expiry_{{ $i }}" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500">
                    <div id="expiry-warning-{{ $i }}" class="text-xs text-red-500 mt-1 hidden">{{ __('passport_expired_warning') }}</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('has_visa') }}</label>
                    <select name="passengers[{{ $i }}][has_visa]" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500">
                        <option value="1">{{ __('yes') }}</option>
                        <option value="0">{{ __('no') }}</option>
                    </select>
                </div>
            </div>

            @if($i === 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('whatsapp_number') }} *</label>
                    <div class="flex space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                        <select name="country_code" class="w-28 py-2.5 px-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm">
                            <option value="+966">+966</option>
                            <option value="+971">+971</option>
                            <option value="+974">+974</option>
                            <option value="+973">+973</option>
                            <option value="+968">+968</option>
                            <option value="+965">+965</option>
                            <option value="+20">+20</option>
                            <option value="+962">+962</option>
                            <option value="+1">+1</option>
                            <option value="+44">+44</option>
                        </select>
                        <input type="tel" name="whatsapp_number" required class="flex-1 py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endfor

        <div class="flex justify-between">
            <a href="{{ route('home') }}" class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold px-6 py-3 rounded-lg transition">
                <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ __('back') }}
            </a>
            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold px-8 py-3 rounded-lg transition shadow-lg">
                {{ __('continue') }} <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} {{ app()->getLocale() === 'ar' ? 'mr-2' : 'ml-2' }}"></i>
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
function uploadPassport(input, index) {
    const file = input.files[0];
    if (!file) return;

    const status = document.getElementById('ocr-status-' + index);
    status.classList.remove('hidden');
    status.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> {{ app()->getLocale() === "ar" ? "جاري قراءة الجواز..." : "Reading passport..." }}';
    status.className = 'mt-2 text-sm text-blue-600';

    const formData = new FormData();
    formData.append('passport_image', file);
    formData.append('_token', '{{ csrf_token() }}');

    fetch('{{ route("booking.upload-passport") }}', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                if (data.data.first_name) document.getElementById('first_name_' + index).value = data.data.first_name;
                if (data.data.last_name) document.getElementById('last_name_' + index).value = data.data.last_name;
                if (data.data.passport_number) document.getElementById('passport_number_' + index).value = data.data.passport_number;
                if (data.data.nationality) document.getElementById('nationality_' + index).value = data.data.nationality;
                if (data.data.date_of_birth) document.getElementById('dob_' + index).value = data.data.date_of_birth;
                if (data.data.gender) document.getElementById('gender_' + index).value = data.data.gender;
                if (data.data.expiry_date) document.getElementById('passport_expiry_' + index).value = data.data.expiry_date;
                status.innerHTML = '<i class="fas fa-check-circle mr-1"></i> {{ app()->getLocale() === "ar" ? "تم استخراج البيانات بنجاح" : "Data extracted successfully" }}';
                status.className = 'mt-2 text-sm text-green-600';
                if (data.data.expiry_warning) {
                    document.getElementById('expiry-warning-' + index).classList.remove('hidden');
                }
            } else {
                status.innerHTML = '<i class="fas fa-info-circle mr-1"></i> {{ app()->getLocale() === "ar" ? "يرجى إدخال البيانات يدويا" : "Please enter data manually" }}';
                status.className = 'mt-2 text-sm text-yellow-600';
            }
        })
        .catch(() => {
            status.innerHTML = '<i class="fas fa-info-circle mr-1"></i> {{ app()->getLocale() === "ar" ? "يرجى إدخال البيانات يدويا" : "Please enter data manually" }}';
            status.className = 'mt-2 text-sm text-yellow-600';
        });
}

// Check passport expiry
document.querySelectorAll('[id^="passport_expiry_"]').forEach(input => {
    input.addEventListener('change', function() {
        const idx = this.id.split('_').pop();
        const warning = document.getElementById('expiry-warning-' + idx);
        const expiry = new Date(this.value);
        const sixMonths = new Date();
        sixMonths.setMonth(sixMonths.getMonth() + 6);
        warning.classList.toggle('hidden', expiry > sixMonths);
    });
});
</script>
@endsection
