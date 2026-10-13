@extends('layouts.app')

@section('title', 'Testimoni Baru')
@section('page-title', 'Testimoni Baru')
@section('page-subtitle', 'Tambah testimoni baru untuk halaman beranda')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <form action="{{ route('admin.testimonials.store') }}" method="POST" class="space-y-4">
            @csrf
            @include('admin.testimonials._form')

            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('admin.testimonials.index') }}" class="px-4 py-2 border rounded-lg dark:border-gray-600">Batal</a>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
