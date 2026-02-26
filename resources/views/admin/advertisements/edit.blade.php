@extends('layouts.admin')
@section('title', __('edit') . ' - ' . __('manage_advertisements'))
@section('content')
<div class="max-w-2xl">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.advertisements.index') }}" class="text-gray-500 hover:text-primary-600 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}"><i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i></a>
        <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ __('edit') }} - {{ $advertisement->title }}</h2>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <form action="{{ route('admin.advertisements.update', $advertisement) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'العنوان' : 'Title' }} *</label><input type="text" name="title" value="{{ old('title', $advertisement->title) }}" required class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500"></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'الوصف' : 'Description' }}</label><textarea name="description" rows="3" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500">{{ old('description', $advertisement->description) }}</textarea></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'النوع' : 'Type' }}</label><select name="type" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"><option value="banner" {{ $advertisement->type === 'banner' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'بانر' : 'Banner' }}</option><option value="whatsapp" {{ $advertisement->type === 'whatsapp' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'واتساب' : 'WhatsApp' }}</option></select></div>
                @if($advertisement->image)<div><img src="{{ asset('storage/' . $advertisement->image) }}" class="w-full max-h-40 object-cover rounded-lg"></div>@endif
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'تغيير الصورة' : 'Change Image' }}</label><input type="file" name="image" accept="image/*" class="w-full text-sm file:bg-primary-600 file:text-white file:border-0 file:rounded-lg file:px-4 file:py-2 file:cursor-pointer"></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'رابط URL' : 'URL Link' }}</label><input type="url" name="url" value="{{ old('url', $advertisement->url) }}" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-primary-500"></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('status') }}</label><select name="status" class="w-full py-2.5 px-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm"><option value="active" {{ $advertisement->status === 'active' ? 'selected' : '' }}>{{ __('active') }}</option><option value="inactive" {{ $advertisement->status === 'inactive' ? 'selected' : '' }}>{{ __('inactive') }}</option></select></div>
            </div>
            <button type="submit" class="w-full mt-6 bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 rounded-lg transition shadow-lg"><i class="fas fa-save {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>{{ __('save') }}</button>
        </form>
    </div>
</div>
@endsection
