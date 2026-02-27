@extends('layouts.admin')
@section('title', __('booking_details') . ' - ' . $booking->booking_reference)
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div><h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ $booking->booking_reference }}</h2>
            <span class="px-3 py-1 text-sm rounded-full {{ $booking->status === 'issued' ? 'bg-green-100 text-green-700' : ($booking->status === 'confirmed' ? 'bg-blue-100 text-blue-700' : ($booking->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700')) }}">{{ ucfirst($booking->status) }}</span>
        </div>
        <a href="{{ route('provider.bookings.index') }}" class="text-sm text-gray-600 hover:text-gray-800 dark:text-gray-400"><i class="fas fa-arrow-left {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ __('back') }}</a>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4"><i class="fas fa-plane {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }} text-primary-600"></i>{{ __('flight_info') }}</h3>
            <div class="space-y-3">
                <div class="flex justify-between"><span class="text-gray-500">{{ __('flight_number') }}</span><span class="font-medium">{{ optional($booking->flight)->flight_number }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">{{ __('airline') }}</span><span class="font-medium">{{ optional(optional($booking->flight)->airline)->name_en }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">{{ __('from') }}</span><span class="font-medium">{{ optional(optional($booking->flight)->departureAirport)->city_en }} ({{ optional(optional($booking->flight)->departureAirport)->code }})</span></div>
                <div class="flex justify-between"><span class="text-gray-500">{{ __('to') }}</span><span class="font-medium">{{ optional(optional($booking->flight)->arrivalAirport)->city_en }} ({{ optional(optional($booking->flight)->arrivalAirport)->code }})</span></div>
                <div class="flex justify-between"><span class="text-gray-500">{{ __('departure') }}</span><span class="font-medium">{{ optional($booking->flight)->departure_time ? $booking->flight->departure_time->format('Y-m-d H:i') : '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">{{ __('arrival') }}</span><span class="font-medium">{{ optional($booking->flight)->arrival_time ? $booking->flight->arrival_time->format('Y-m-d H:i') : '-' }}</span></div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4"><i class="fas fa-user {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }} text-primary-600"></i>{{ __('customer_info') }}</h3>
            <div class="space-y-3">
                <div class="flex justify-between"><span class="text-gray-500">{{ __('name') }}</span><span class="font-medium">{{ optional($booking->customer)->first_name }} {{ optional($booking->customer)->last_name }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">{{ __('phone') }}</span><span class="font-medium">{{ optional($booking->customer)->phone ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">{{ __('email') }}</span><span class="font-medium">{{ optional($booking->customer)->email ?? '-' }}</span></div>
                @if($booking->agent)
                <div class="border-t dark:border-gray-600 pt-3 mt-3">
                    <p class="text-sm font-semibold text-gray-800 dark:text-white mb-2"><i class="fas fa-user-tie {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ __('agent') }}</p>
                    <div class="flex justify-between"><span class="text-gray-500">{{ __('name') }}</span><span class="font-medium">{{ $booking->agent->name }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">{{ __('phone') }}</span><span class="font-medium">{{ $booking->agent->phone ?? '-' }}</span></div>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700"><h3 class="text-lg font-semibold text-gray-800 dark:text-white"><i class="fas fa-users {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }} text-primary-600"></i>{{ __('passengers') }}</h3></div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700"><tr><th class="px-4 py-3 text-start">{{ __('name') }}</th><th class="px-4 py-3 text-start">{{ __('passport') }}</th><th class="px-4 py-3 text-start">{{ __('type') }}</th><th class="px-4 py-3 text-start">{{ __('nationality') }}</th><th class="px-4 py-3 text-start">{{ __('date_of_birth') }}</th></tr></thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($booking->passengers ?? [] as $p)
                    <tr><td class="px-4 py-3 font-medium">{{ $p->first_name }} {{ $p->last_name }}</td><td class="px-4 py-3">{{ $p->passport_number }}</td><td class="px-4 py-3"><span class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-700">{{ ucfirst($p->type ?? 'adult') }}</span></td><td class="px-4 py-3">{{ $p->nationality ?? '-' }}</td><td class="px-4 py-3">{{ $p->date_of_birth ?? '-' }}</td></tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-4 text-center text-gray-500">{{ __('no_passengers') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4"><i class="fas fa-dollar-sign {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }} text-primary-600"></i>{{ __('price_details') }}</h3>
            <div class="space-y-3">
                <div class="flex justify-between"><span class="text-gray-500">{{ __('total_price') }}</span><span class="font-bold text-lg">${{ number_format($booking->total_price, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">{{ __('net_price') }}</span><span class="font-medium">${{ number_format($booking->net_price ?? 0, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">{{ __('commission') }}</span><span class="font-medium text-accent-600">${{ number_format($booking->commission_amount ?? 0, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">{{ __('payment_status') }}</span><span class="px-2 py-1 text-xs rounded-full {{ ($booking->payment_status ?? 'pending') === 'paid' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">{{ ucfirst($booking->payment_status ?? 'pending') }}</span></div>
                @if($booking->ticket_number)<div class="flex justify-between"><span class="text-gray-500">{{ __('ticket_number') }}</span><span class="font-medium text-green-600">{{ $booking->ticket_number }}</span></div>@endif
            </div>
        </div>
        <div class="space-y-4">
            @if($booking->status === 'pending')
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">{{ __('confirm_booking') }}</h3>
                <form action="{{ route('provider.bookings.confirm', $booking) }}" method="POST">@csrf
                    <button type="submit" class="w-full bg-blue-600 text-white rounded-lg px-4 py-2.5 hover:bg-blue-700"><i class="fas fa-check {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ __('confirm') }}</button>
                </form>
            </div>
            @endif
            @if(in_array($booking->status, ['pending', 'confirmed']))
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4"><i class="fas fa-ticket-alt {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }} text-green-600"></i>{{ __('issue_ticket') }}</h3>
                <form action="{{ route('provider.bookings.issue-ticket', $booking) }}" method="POST" enctype="multipart/form-data">@csrf
                    <div class="space-y-3">
                        <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('ticket_number') }} *</label><input type="text" name="ticket_number" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm"></div>
                        <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('ticket_image') }}</label><input type="file" name="ticket_image" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-primary-100 file:text-primary-700 hover:file:bg-primary-200"><p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG (max 10MB)</p></div>
                        <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('notes') }}</label><textarea name="notes" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm"></textarea></div>
                        <button type="submit" class="w-full bg-green-600 text-white rounded-lg px-4 py-2.5 hover:bg-green-700"><i class="fas fa-file-upload {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ __('issue_ticket') }}</button>
                    </div>
                </form>
            </div>
            @endif
            @if($booking->ticket_image)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4"><i class="fas fa-file text-green-600 {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ __('ticket_document') }}</h3>
                <a href="{{ asset('storage/' . $booking->ticket_image) }}" target="_blank" class="inline-flex items-center text-primary-600 hover:text-primary-700"><i class="fas fa-download {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ __('download_ticket') }}</a>
            </div>
            @endif
            @if(!in_array($booking->status, ['cancelled', 'issued']))
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-red-200 dark:border-red-700">
                <h3 class="text-lg font-semibold text-red-600 mb-4">{{ __('cancel_booking') }}</h3>
                <form action="{{ route('provider.bookings.cancel', $booking) }}" method="POST">@csrf
                    <div class="mb-3"><textarea name="reason" required rows="2" placeholder="{{ __('cancellation_reason') }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm"></textarea></div>
                    <button type="submit" class="w-full bg-red-600 text-white rounded-lg px-4 py-2.5 hover:bg-red-700" onclick="return confirm('{{ __('confirm_cancel') }}')"><i class="fas fa-times {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ __('cancel') }}</button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
