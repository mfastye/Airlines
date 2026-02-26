@extends('layouts.admin')
@section('title', __('payment_details'))
@section('content')
<div class="max-w-3xl">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.payments.index') }}" class="text-gray-500 hover:text-primary-600 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"><i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i></a>
        <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ __('payment_details') }}</h2>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><span class="text-gray-500">{{ __('booking_reference') }}:</span> <span class="font-mono font-bold text-primary-600">{{ $payment->booking->booking_reference ?? '-' }}</span></div>
            <div><span class="text-gray-500">{{ app()->getLocale() === 'ar' ? 'المبلغ' : 'Amount' }}:</span> <span class="font-bold text-lg dark:text-white">${{ number_format($payment->amount, 2) }}</span></div>
            <div><span class="text-gray-500">{{ app()->getLocale() === 'ar' ? 'بوابة الدفع' : 'Gateway' }}:</span> <span class="dark:text-white">{{ $payment->gateway->name ?? '-' }}</span></div>
            <div><span class="text-gray-500">{{ __('payment_status') }}:</span> <span class="px-2 py-0.5 rounded-full text-xs font-medium @if($payment->status === 'confirmed') bg-green-100 text-green-800 @elseif($payment->status === 'rejected') bg-red-100 text-red-800 @else bg-yellow-100 text-yellow-800 @endif">{{ __($payment->status) }}</span></div>
            <div><span class="text-gray-500">{{ app()->getLocale() === 'ar' ? 'التاريخ' : 'Date' }}:</span> <span class="dark:text-white">{{ $payment->created_at->format('d M Y H:i') }}</span></div>
            @if($payment->transaction_id)<div><span class="text-gray-500">{{ app()->getLocale() === 'ar' ? 'رقم العملية' : 'Transaction ID' }}:</span> <span class="font-mono dark:text-white">{{ $payment->transaction_id }}</span></div>@endif
        </div>
        @if($payment->receipt_image)<div class="mt-4 border-t border-gray-200 dark:border-gray-600 pt-4"><h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ app()->getLocale() === 'ar' ? 'صورة الإيصال' : 'Receipt Image' }}</h4><img src="{{ asset('storage/' . $payment->receipt_image) }}" class="max-w-sm rounded-lg shadow"></div>@endif
    </div>
    @if($payment->status === 'pending')
    <div class="flex space-x-4 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
        <form action="{{ route('admin.payments.approve', $payment) }}" method="POST" class="flex-1">@csrf<button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg transition"><i class="fas fa-check {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ app()->getLocale() === 'ar' ? 'قبول الدفع' : 'Approve Payment' }}</button></form>
        <form action="{{ route('admin.payments.reject', $payment) }}" method="POST" class="flex-1" onsubmit="return confirm('{{ __('are_you_sure') }}')">@csrf<div class="mb-2"><input type="text" name="rejection_reason" placeholder="{{ app()->getLocale() === 'ar' ? 'سبب الرفض' : 'Rejection reason' }}" class="w-full py-2 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"></div><button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg transition"><i class="fas fa-times {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ app()->getLocale() === 'ar' ? 'رفض الدفع' : 'Reject Payment' }}</button></form>
    </div>
    @endif
</div>
@endsection
