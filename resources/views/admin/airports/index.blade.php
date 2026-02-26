@extends('layouts.admin')
@section('title', __('manage_airports'))
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ __('manage_airports') }}</h2>
    <a href="{{ route('admin.airports.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm transition shadow">
        <i class="fas fa-plus {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ __('add_new') }}
    </a>
</div>
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">#</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'الرمز' : 'Code' }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ __('name') }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'المدينة' : 'City' }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'البلد' : 'Country' }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ __('status') }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ __('actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($airports as $airport)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3 text-gray-500">{{ $loop->iteration }}</td>
                    <td class="px-4 py-3 font-mono font-bold text-primary-600">{{ $airport->code }}</td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800 dark:text-white">{{ app()->getLocale() === 'ar' ? $airport->name_ar : $airport->name_en }}</div>
                    </td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ app()->getLocale() === 'ar' ? $airport->city_ar : $airport->city_en }}</td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ app()->getLocale() === 'ar' ? $airport->country_ar : $airport->country_en }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $airport->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800' }}">{{ __($airport->status) }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                            <a href="{{ route('admin.airports.edit', $airport) }}" class="text-blue-600 hover:text-blue-800 p-1"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.airports.destroy', $airport) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('are_you_sure') }}')">@csrf @method('DELETE')<button type="submit" class="text-red-600 hover:text-red-800 p-1"><i class="fas fa-trash"></i></button></form>
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
