@extends('layouts.app')

@section('title', $article->title)
@section('page-title', $article->title)
@section('page-subtitle', $article->category ?? 'Basis Pengetahuan')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <a href="{{ route('knowledge-base.index') }}" class="text-sm text-gray-500 hover:text-primary transition">&larr; Kembali ke Basis Pengetahuan</a>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <div class="prose dark:prose-invert max-w-none">
            {!! nl2br(e($article->content)) !!}
        </div>
    </div>

    <div class="bg-primary/5 border border-primary/20 rounded-xl p-4 text-sm text-gray-700 dark:text-gray-300 flex items-center justify-between flex-wrap gap-3">
        <span>Masih belum terjawab?</span>
        <a href="{{ route('tickets.create') }}" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition text-sm">Buat Tiket Bantuan</a>
    </div>
</div>
@endsection
