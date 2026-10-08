@extends('layouts.app')

@section('title', 'Kalender Editorial')
@section('page-title', 'Kalender Editorial')
@section('page-subtitle', 'Jadwal terbit, review, dan revisi artikel')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <x-editor-calendar-grid :days="$days" :current="$current" :start-offset="$startOffset" />
    </div>

    <div class="mt-6 space-y-3">
        @foreach ($days as $day)
            @php
                $items = $day['published']->map(fn ($a) => ['label' => 'Terbit', 'color' => 'text-green-600', 'title' => $a->title])
                    ->concat($day['deadline']->map(fn ($a) => ['label' => 'Dijadwalkan', 'color' => 'text-orange-500', 'title' => $a->title]))
                    ->concat($day['review']->map(fn ($a) => ['label' => 'Masuk Review', 'color' => 'text-blue-600', 'title' => $a->title]))
                    ->concat($day['revision']->map(fn ($a) => ['label' => 'Revisi', 'color' => 'text-red-500', 'title' => $a->title]));
            @endphp
            @if ($items->isNotEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                    <p class="text-sm font-semibold text-gray-800 dark:text-white mb-2">{{ $day['date']->translatedFormat('d F Y') }}</p>
                    <ul class="space-y-1 text-sm">
                        @foreach ($items as $item)
                            <li>
                                <span class="{{ $item['color'] }} font-medium">{{ $item['label'] }}</span>
                                <span class="text-gray-600 dark:text-gray-400"> &middot; {{ $item['title'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endforeach
    </div>
</div>
@endsection
