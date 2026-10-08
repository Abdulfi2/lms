@props(['days', 'current', 'startOffset', 'compact' => false])

@php
    $dayLabels = $compact ? ['S', 'S', 'R', 'K', 'J', 'S', 'M'] : ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
    $cellClass = $compact ? 'h-8 text-xs' : 'h-20 text-sm';
    $typeColors = ['jadwal_support' => '#22C55E', 'maintenance' => '#F97316', 'meeting' => '#3B82F6', 'kegiatan' => '#EF4444'];
@endphp

<div class="flex items-center justify-between mb-3">
    <a href="{{ route('support.schedule.index', ['month' => $current->copy()->subMonth()->month, 'year' => $current->copy()->subMonth()->year]) }}"
        class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    </a>
    <p class="font-semibold text-gray-800 dark:text-white text-sm">{{ $current->translatedFormat('F Y') }}</p>
    <a href="{{ route('support.schedule.index', ['month' => $current->copy()->addMonth()->month, 'year' => $current->copy()->addMonth()->year]) }}"
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
            $isToday = $day['date']->isToday();
            $types = $day['items']->pluck('type')->unique();
            $tooltipParts = $day['items']->map(fn ($s) => $s->typeLabel() . ': ' . $s->title)->implode(', ');
        @endphp
        <div class="{{ $cellClass }} flex flex-col items-center justify-center rounded-lg {{ $isToday ? 'bg-primary text-white font-semibold' : 'text-gray-700 dark:text-gray-300' }}"
            @if ($tooltipParts) title="{{ $day['date']->format('d M') }}: {{ $tooltipParts }}" @endif>
            <span>{{ $day['date']->day }}</span>
            @if ($types->isNotEmpty())
                <span class="flex gap-0.5 mt-0.5">
                    @foreach ($types as $type)
                        <span class="w-1 h-1 rounded-full" style="background-color: {{ $typeColors[$type] ?? '#9CA3AF' }};"></span>
                    @endforeach
                </span>
            @endif
        </div>
    @endforeach
</div>

<div class="flex flex-wrap gap-3 mt-3 text-xs text-gray-500 dark:text-gray-400">
    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full" style="background-color: #22C55E;"></span> Jadwal Support</span>
    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full" style="background-color: #F97316;"></span> Maintenance</span>
    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full" style="background-color: #3B82F6;"></span> Meeting</span>
    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full" style="background-color: #EF4444;"></span> Kegiatan</span>
</div>
