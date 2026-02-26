@extends('layouts.admin')
@section('title', __('manage_airlines'))
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ __('manage_airlines') }}</h2>
    <a href="{{ route('admin.airlines.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm transition shadow">
        <i class="fas fa-plus {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ __('add_new') }}
    </a>
</div>
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">#</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'الشعار' : 'Logo' }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ __('name') }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'الرمز' : 'Code' }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ __('status') }}</th>
                    <th class="px-4 py-3 text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} font-semibold text-gray-600 dark:text-gray-300">{{ __('actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($airlines as $airline)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3 text-gray-500">{{ $loop->iteration }}</td>
                    <td class="px-4 py-3">
                        @if($airline->logo)
                        <img src="{{ asset('storage/' . $airline->logo) }}" class="w-10 h-10 object-contain rounded">
                        @else
                        <div class="w-10 h-10 bg-gray-100 dark:bg-gray-600 rounded flex items-center justify-center"><i class="fas fa-plane text-gray-400"></i></div>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800 dark:text-white">{{ app()->getLocale() === 'ar' ? $airline->name_ar : $airline->name_en }}</div>
                        <div class="text-xs text-gray-500">{{ app()->getLocale() === 'ar' ? $airline->name_en : $airline->name_ar }}</div>
                    </td>
                    <td class="px-4 py-3 font-mono text-gray-600 dark:text-gray-400">{{ $airline->code }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $airline->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                            {{ __($airline->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                            <a href="{{ route('admin.airlines.edit', $airline) }}" class="text-blue-600 hover:text-blue-800 p-1" title="{{ __('edit') }}"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.airlines.destroy', $airline) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('are_you_sure') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 p-1" title="{{ __('delete') }}"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">{{ __('no_data') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
