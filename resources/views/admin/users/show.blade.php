@extends('layouts.app')

@section('title', 'Detail User')
@section('page-title', 'Detail User')
@section('page-subtitle', 'Informasi lengkap user')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="p-6">
                <div class="flex items-center space-x-4 mb-6">
                    <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=3B82F6&color=white' }}"
                        class="w-20 h-20 rounded-full object-cover">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $user->name }}</h2>
                        <p class="text-gray-500">{{ $user->email }}</p>
                        <span
                            class="inline-block px-2 py-1 text-xs rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $user->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><strong>Role:</strong> {{ ucfirst($user->roles->first()->name ?? '-') }}</div>
                    <div><strong>Email Verifikasi:</strong> {{ $user->hasVerifiedEmail() ? 'Terverifikasi' : 'Belum' }}
                    </div>
                    <div><strong>Bergabung:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</div>
                    <div><strong>Terakhir Login:</strong>
                        {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : '-' }}</div>
                </div>

                @if ($user->hasRole('instructor') && $user->profile)
                    <div class="mt-4 p-4 rounded-lg border dark:border-gray-700 flex items-center justify-between">
                        <div>
                            <strong>Status Persetujuan Instruktur:</strong>
                            <span class="ml-1 inline-block px-2 py-1 text-xs rounded-full
                                @if ($user->profile->approval_status === 'approved') bg-green-100 text-green-800
                                @elseif ($user->profile->approval_status === 'rejected') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($user->profile->approval_status ?? 'pending') }}
                            </span>
                        </div>
                        @if ($user->profile->approval_status !== 'approved')
                            <form method="POST" action="{{ route('admin.users.approval-status', $user) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="approval_status" value="approved">
                                <button type="submit" class="px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">Setujui</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.users.approval-status', $user) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="approval_status" value="rejected">
                                <button type="submit" class="px-3 py-1.5 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">Cabut Persetujuan</button>
                            </form>
                        @endif
                    </div>
                @endif

                <hr class="my-4 dark:border-gray-700">
                <h3 class="text-lg font-semibold mb-3">Profil Lengkap</h3>
                @if ($user->profile)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><strong>Nama Depan:</strong> {{ $user->profile->first_name ?? '-' }}</div>
                        <div><strong>Nama Belakang:</strong> {{ $user->profile->last_name ?? '-' }}</div>
                        <div><strong>Nickname:</strong> {{ $user->profile->nickname ?? '-' }}</div>
                        <div><strong>Telepon:</strong> {{ $user->profile->phone ?? '-' }}</div>
                        <div><strong>Jenis Kelamin:</strong> {{ $user->profile->gender ?? '-' }}</div>
                        <div><strong>Tanggal Lahir:</strong>
                            {{ $user->profile->birth_date ? \Carbon\Carbon::parse($user->profile->birth_date)->format('d/m/Y') : '-' }}
                        </div>
                    </div>
                @else
                    <p class="text-gray-500">Profil belum dilengkapi.</p>
                @endif
            </div>
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 flex justify-end">
                <a href="{{ route('admin.users.edit', $user) }}"
                    class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 mr-2">Edit</a>
                <a href="{{ route('admin.users.index') }}"
                    class="px-4 py-2 border rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600">Kembali</a>
            </div>
        </div>
    </div>
@endsection
