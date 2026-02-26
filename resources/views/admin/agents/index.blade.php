@extends('layouts.admin')
@section('title', __('manage_agents'))
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ __('manage_agents') }}</h2>
    <a href="{{ route('admin.agents.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm transition shadow"><i class="fas fa-plus {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ __('add_new') }}</a>
</div>
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">#</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'الاسم التجاري' : 'Company' }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'المزود' : 'Provider' }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'الرصيد' : 'Balance' }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">API</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ __('status') }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ __('actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($agents as $agent)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3 text-gray-500">{{ $loop->iteration }}</td>
                    <td class="px-4 py-3 font-medium text-gray-800 dark:text-white">{{ $agent->company_name }}</td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $agent->provider->company_name ?? '-' }}</td>
                    <td class="px-4 py-3 font-semibold text-primary-600">${{ number_format($agent->financialAccount->balance ?? 0, 2) }}</td>
                    <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full text-xs {{ $agent->api_enabled ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600' }}">{{ $agent->api_enabled ? 'ON' : 'OFF' }}</span></td>
                    <td class="px-4 py-3"><span class="px-2 py-1 rounded-full text-xs font-medium {{ $agent->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800' }}">{{ __($agent->status) }}</span></td>
                    <td class="px-4 py-3">
                        <div class="flex items-center space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                            <a href="{{ route('admin.agents.edit', $agent) }}" class="text-blue-600 hover:text-blue-800 p-1"><i class="fas fa-edit"></i></a>
                            <a href="{{ route('admin.agents.show', $agent) }}" class="text-green-600 hover:text-green-800 p-1"><i class="fas fa-eye"></i></a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">{{ __('no_data') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
