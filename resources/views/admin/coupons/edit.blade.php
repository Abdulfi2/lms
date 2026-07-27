@extends('layouts.app')

@section('title', 'Edit Kupon')
@section('page-title', 'Edit Kupon')

@section('content')
<div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
    <form method="POST" action="{{ route('admin.coupons.update', $coupon) }}" class="space-y-4">
        @csrf
        @method('PUT')
        @include('admin.coupons._form')

        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.coupons.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-100">Batal</a>
            <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Perbarui</button>
        </div>
    </form>
</div>
@endsection
