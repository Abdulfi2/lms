@extends('layouts.app')

@section('title', 'Tambah Kupon')
@section('page-title', 'Tambah Kupon')

@section('content')
<div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
    <form method="POST" action="{{ route('admin.coupons.store') }}" class="space-y-4">
        @csrf
        @include('admin.coupons._form')

        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.coupons.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-100">Batal</a>
            <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Simpan</button>
        </div>
    </form>
</div>
@endsection
