@extends('layouts.admin')
@section('title', __('manage_advertisements'))
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ __('manage_advertisements') }}</h2>
    <a href="{{ route('admin.advertisements.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm transition shadow"><i class="fas fa-plus {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>{{ __('add_new') }}</a>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($advertisements ?? [] as $ad)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
        @if($ad->image)<img src="{{ asset('storage/' . $ad->image) }}" class="w-full h-40 object-cover">@else<div class="w-full h-40 bg-gray-200 dark:bg-gray-700 flex items-center justify-center"><i class="fas fa-image text-3xl text-gray-400"></i></div>@endif
        <div class="p-4">
            <h3 class="font-semibold dark:text-white mb-1">{{ $ad->title }}</h3>
            @if($ad->description)<p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ Str::limit($ad->description, 80) }}</p>@endif
            <div class="flex items-center justify-between">
                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $ad->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ __($ad->status) }}</span>
                <span class="text-xs text-gray-500">{{ $ad->type === 'banner' ? (app()->getLocale() === 'ar' ? 'بانر' : 'Banner') : (app()->getLocale() === 'ar' ? 'واتساب' : 'WhatsApp') }}</span>
            </div>
            <div class="flex justify-end space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }} mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('admin.advertisements.edit', $ad) }}" class="text-blue-600 hover:text-blue-800 text-sm p-1"><i class="fas fa-edit"></i></a>
                <form action="{{ route('admin.advertisements.destroy', $ad) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('are_you_sure') }}')">@csrf @method('DELETE')<button class="text-red-600 hover:text-red-800 text-sm p-1"><i class="fas fa-trash"></i></button></form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-3 text-center py-12 text-gray-500">{{ __('no_data') }}</div>
    @endforelse
</div>
@endsection
