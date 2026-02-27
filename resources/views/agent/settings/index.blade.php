@extends('layouts.admin')
@section('title', __('settings'))
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4"><i class="fas fa-user-cog {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }} text-primary-600"></i>{{ __('account_settings') }}</h3>
        <form action="{{ route('agent.settings.update') }}" method="POST">@csrf
            <div class="space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('name') }}</label><input type="text" name="name" value="{{ old('name', $agent->name ?? '') }}" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm"></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('phone') }}</label><input type="text" name="phone" value="{{ old('phone', $agent->phone ?? '') }}" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm"></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('email') }}</label><input type="email" name="email" value="{{ old('email', $agent->email ?? '') }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm"></div>
                <button type="submit" class="w-full bg-primary-600 text-white rounded-lg px-4 py-2.5 hover:bg-primary-700"><i class="fas fa-save {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ __('save') }}</button>
            </div>
        </form>
    </div>
    @if($agent->api_enabled ?? false)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4"><i class="fas fa-key {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }} text-accent-600"></i>{{ __('api_credentials') }}</h3>
        <div class="space-y-3">
            <div><label class="block text-xs font-medium text-gray-500 mb-1">API Key</label><div class="flex"><input type="text" value="{{ $agent->api_key ?? '' }}" readonly class="flex-1 rounded-l-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm font-mono"><button onclick="navigator.clipboard.writeText('{{ $agent->api_key ?? '' }}')" class="px-3 bg-gray-100 dark:bg-gray-600 border border-gray-300 dark:border-gray-600 rounded-r-lg hover:bg-gray-200"><i class="fas fa-copy"></i></button></div></div>
            <div><label class="block text-xs font-medium text-gray-500 mb-1">API Secret</label><div class="flex"><input type="password" value="{{ $agent->api_secret ?? '' }}" readonly class="flex-1 rounded-l-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm font-mono"><button onclick="navigator.clipboard.writeText('{{ $agent->api_secret ?? '' }}')" class="px-3 bg-gray-100 dark:bg-gray-600 border border-gray-300 dark:border-gray-600 rounded-r-lg hover:bg-gray-200"><i class="fas fa-copy"></i></button></div></div>
            <p class="text-xs text-gray-500">{{ __('api_base_url') }}: <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ url('/api/agent') }}</code></p>
        </div>
    </div>
    @endif
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4"><i class="fas fa-info-circle {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }} text-blue-600"></i>{{ __('account_info') }}</h3>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between"><span class="text-gray-500">{{ __('provider') }}</span><span class="font-medium">{{ optional($agent->provider)->business_name ?? '-' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">{{ __('commission_type') }}</span><span class="font-medium">{{ ucfirst($agent->commission_type ?? 'percentage') }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">{{ __('commission_rate') }}</span><span class="font-medium">{{ $agent->commission_value ?? 0 }}{{ ($agent->commission_type ?? 'percentage') === 'percentage' ? '%' : ' USD' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">{{ __('status') }}</span><span class="px-2 py-1 text-xs rounded-full {{ ($agent->status ?? 'active') === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ ucfirst($agent->status ?? 'active') }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">{{ __('registered_date') }}</span><span class="font-medium">{{ $agent->created_at ? $agent->created_at->format('Y-m-d') : '-' }}</span></div>
        </div>
    </div>
</div>
@endsection
