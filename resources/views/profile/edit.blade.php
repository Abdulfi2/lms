@extends('layouts.app')

@section('title', __('Profile'))
@section('page-title', __('Profile'))
@section('page-subtitle', __('Kelola informasi akun dan keamanan Anda.'))

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Profile Header -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <div class="flex items-center gap-4">
            <img src="{{ $user->avatar_url }}" class="w-20 h-20 rounded-full object-cover ring-2 ring-primary/20">
            <div>
                <h1 class="text-xl font-bold text-gray-800 dark:text-white">{{ $user->name }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                <div class="mt-2 flex items-center gap-2">
                    <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-primary/10 text-primary">
                        {{ ucfirst($user->role_name) }}
                    </span>
                    <span class="text-xs text-gray-400">
                        {{ __('Bergabung') }} {{ $user->created_at->translatedFormat('d M Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <div class="max-w-xl">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <div class="max-w-xl">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-red-100 dark:border-red-900/30">
        @include('profile.partials.delete-user-form')
    </div>
</div>
@endsection
