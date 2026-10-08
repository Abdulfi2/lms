@props(['days', 'current', 'startOffset', 'compact' => false])

@php
    $dayLabels = $compact ? ['S', 'S', 'R', 'K', 'J', 'S', 'M'] : ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
    $cellClass = $compact ? 'h-8 text-xs' : 'h-20 text-sm';
@endphp

<div class="flex items-center justify-between mb-3">
    <a href="{{ route('editor.calendar.index', ['month' => $current->copy()->subMonth()->month, 'year' => $current->copy()->subMonth()->year]) }}"
        class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    </a>
    <p class="font-semibold text-gray-800 dark:text-white text-sm">{{ $current->translatedFormat('F Y') }}</p>
    <a href="{{ route('editor.calendar.index', ['month' => $current->copy()->addMonth()->month, 'year' => $current->copy()->addMonth()->year]) }}"
        class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
    </a>
</div>

<div class="grid grid-cols-7 gap-1 text-center text-gray-400 text-xs mb-1">
    @foreach ($dayLabels as $label)
        <div>{{ $label }}</div>
    @endforeach
</div>

<div class="grid grid-cols-7 gap-1">
    @for ($i = 0; $i < $startOffset; $i++)
        <div class="{{ $cellClass }}"></div>
    @endfor

    @foreach ($days as $day)
        @php
            $hasPublished = $day['published']->isNotEmpty();
            $hasDeadline = $day['deadline']->isNotEmpty();
            $hasReview = $day['review']->isNotEmpty();
            $hasRevision = $day['revision']->isNotEmpty();
            $isToday = $day['date']->isToday();
            $tooltipParts = collect([
                $hasPublished ? $day['published']->count() . ' terbit' : null,
                $hasDeadline ? $day['deadline']->count() . ' dijadwalkan' : null,
                $hasReview ? $day['review']->count() . ' masuk review' : null,
                $hasRevision ? $day['revision']->count() . ' revisi' : null,
            ])->filter()->implode(', ');
        @endphp
        <div class="{{ $cellClass }} flex flex-col items-center justify-center rounded-lg {{ $isToday ? 'bg-primary text-white font-semibold' : 'text-gray-700 dark:text-gray-300' }}"
            @if ($tooltipParts) title="{{ $day['date']->format('d M') }}: {{ $tooltipParts }}" @endif>
            <span>{{ $day['date']->day }}</span>
            @if ($hasPublished || $hasDeadline || $hasReview || $hasRevision)
                <span class="flex gap-0.5 mt-0.5">
                    @if ($hasPublished)<span class="w-1 h-1 rounded-full bg-green-500"></span>@endif
                    @if ($hasDeadline)<span class="w-1 h-1 rounded-full bg-orange-400"></span>@endif
                    @if ($hasReview)<span class="w-1 h-1 rounded-full bg-blue-500"></span>@endif
                    @if ($hasRevision)<span class="w-1 h-1 rounded-full bg-red-500"></span>@endif
                </span>
            @endif
        </div>
    @endforeach
</div>

<div class="flex flex-wrap gap-3 mt-3 text-xs text-gray-500 dark:text-gray-400">
    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-green-500"></span> Terbit</span>
    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-orange-400"></span> Deadline</span>
    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-500"></span> Review</span>
    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500"></span> Revisi</span>
</div>
