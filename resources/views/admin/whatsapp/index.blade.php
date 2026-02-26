@extends('layouts.admin')
@section('title', __('whatsapp_gateway'))
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ __('whatsapp_gateway') }}</h2>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <h3 class="font-semibold dark:text-white mb-4"><i class="fas fa-cog text-primary-600 {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ app()->getLocale() === 'ar' ? 'إعدادات البوابة' : 'Gateway Settings' }}</h3>
        <form action="{{ route('admin.whatsapp.update-settings') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'عنوان API' : 'API URL' }}</label><input type="url" name="api_url" value="{{ $gateway->api_url ?? '' }}" placeholder="https://waha-api.example.com" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500"></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'مفتاح API' : 'API Key' }}</label><input type="text" name="api_key" value="{{ $gateway->api_key ?? '' }}" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500"></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'رقم الواتساب' : 'WhatsApp Number' }}</label><input type="tel" name="phone_number" value="{{ $gateway->phone_number ?? '' }}" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500"></div>
                <div class="flex items-center"><label class="flex items-center"><input type="checkbox" name="is_active" value="1" {{ ($gateway->is_active ?? false) ? 'checked' : '' }} class="rounded text-primary-600 focus:ring-primary-500"><span class="text-sm text-gray-700 dark:text-gray-300 {{ app()->getLocale() === 'ar' ? 'mr-2' : 'ml-2' }}">{{ app()->getLocale() === 'ar' ? 'تفعيل البوابة' : 'Enable Gateway' }}</span></label></div>
            </div>
            <button type="submit" class="w-full mt-4 bg-primary-600 hover:bg-primary-700 text-white font-bold py-2.5 rounded-lg transition"><i class="fas fa-save {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ __('save') }}</button>
        </form>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <h3 class="font-semibold dark:text-white mb-4"><i class="fas fa-chart-bar text-primary-600 {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ app()->getLocale() === 'ar' ? 'إحصائيات' : 'Statistics' }}</h3>
        <div class="space-y-4">
            <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"><span class="text-sm text-gray-600 dark:text-gray-400">{{ app()->getLocale() === 'ar' ? 'إجمالي الرسائل' : 'Total Messages' }}</span><span class="font-bold text-gray-800 dark:text-white">{{ $totalMessages ?? 0 }}</span></div>
            <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"><span class="text-sm text-gray-600 dark:text-gray-400">{{ app()->getLocale() === 'ar' ? 'تم الإرسال' : 'Sent' }}</span><span class="font-bold text-green-600">{{ $sentMessages ?? 0 }}</span></div>
            <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"><span class="text-sm text-gray-600 dark:text-gray-400">{{ app()->getLocale() === 'ar' ? 'فشل الإرسال' : 'Failed' }}</span><span class="font-bold text-red-600">{{ $failedMessages ?? 0 }}</span></div>
            <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"><span class="text-sm text-gray-600 dark:text-gray-400">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</span><span class="px-2 py-1 rounded-full text-xs font-medium {{ ($gateway->is_active ?? false) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ ($gateway->is_active ?? false) ? __('active') : __('inactive') }}</span></div>
        </div>
    </div>
</div>
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
    <div class="p-4 border-b border-gray-200 dark:border-gray-700"><h3 class="font-semibold dark:text-white">{{ app()->getLocale() === 'ar' ? 'سجل الرسائل' : 'Message Log' }}</h3></div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'الرقم' : 'To' }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'النوع' : 'Type' }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'الرسالة' : 'Message' }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ __('status') }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'التاريخ' : 'Date' }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($messages ?? [] as $msg)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3 font-mono text-xs dark:text-gray-300">{{ $msg->to_number }}</td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $msg->message_type }}</td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ Str::limit($msg->content, 50) }}</td>
                    <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full text-xs {{ $msg->status === 'sent' ? 'bg-green-100 text-green-800' : ($msg->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">{{ $msg->status }}</span></td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $msg->created_at->format('d M H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">{{ __('no_data') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
