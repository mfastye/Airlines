@extends('layouts.admin')
@section('title', app()->getLocale() === 'ar' ? 'بوابات الدفع' : 'Payment Gateways')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ app()->getLocale() === 'ar' ? 'بوابات الدفع' : 'Payment Gateways' }}</h2>
    <button onclick="document.getElementById('addGatewayModal').classList.toggle('hidden')" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm transition shadow"><i class="fas fa-plus {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ __('add_new') }}</button>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($gateways ?? [] as $gateway)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                @if($gateway->logo)<img src="{{ asset('storage/' . $gateway->logo) }}" class="w-10 h-10 rounded object-contain">@else<div class="w-10 h-10 rounded bg-primary-100 flex items-center justify-center"><i class="fas fa-credit-card text-primary-600"></i></div>@endif
                <div><h3 class="font-semibold dark:text-white">{{ $gateway->name }}</h3><p class="text-xs text-gray-500">{{ $gateway->type === 'manual' ? (app()->getLocale() === 'ar' ? 'يدوية' : 'Manual') : (app()->getLocale() === 'ar' ? 'آلية' : 'Automatic') }}</p></div>
            </div>
            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $gateway->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ __($gateway->status) }}</span>
        </div>
        @if($gateway->instructions)<p class="text-xs text-gray-500 dark:text-gray-400 mb-4">{{ Str::limit($gateway->instructions, 100) }}</p>@endif
        <div class="flex justify-end space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <a href="{{ route('admin.payment-gateways.edit', $gateway) }}" class="text-blue-600 hover:text-blue-800 text-sm"><i class="fas fa-edit"></i></a>
            <form action="{{ route('admin.payment-gateways.destroy', $gateway) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('are_you_sure') }}')">@csrf @method('DELETE')<button class="text-red-600 hover:text-red-800 text-sm"><i class="fas fa-trash"></i></button></form>
        </div>
    </div>
    @empty
    <div class="col-span-3 text-center py-12 text-gray-500">{{ __('no_data') }}</div>
    @endforelse
</div>
<div id="addGatewayModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-lg w-full p-6">
        <h3 class="text-lg font-bold dark:text-white mb-4">{{ __('add_new') }}</h3>
        <form action="{{ route('admin.payment-gateways.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('name') }} *</label><input type="text" name="name" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'النوع' : 'Type' }}</label><select name="type" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"><option value="manual">{{ app()->getLocale() === 'ar' ? 'يدوية' : 'Manual' }}</option><option value="automatic">{{ app()->getLocale() === 'ar' ? 'آلية (API)' : 'Automatic (API)' }}</option></select></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'التعليمات' : 'Instructions' }}</label><textarea name="instructions" rows="3" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"></textarea></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'الشعار' : 'Logo' }}</label><input type="file" name="logo" accept="image/*" class="w-full text-sm"></div>
            </div>
            <div class="flex space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }} mt-6">
                <button type="submit" class="flex-1 bg-primary-600 hover:bg-primary-700 text-white font-bold py-2.5 rounded-lg transition">{{ __('save') }}</button>
                <button type="button" onclick="document.getElementById('addGatewayModal').classList.add('hidden')" class="flex-1 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-white font-bold py-2.5 rounded-lg transition">{{ __('cancel') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
