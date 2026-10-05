@props(['tooltip' => null])

<label {{ $attributes->merge(['class' => 'flex items-center gap-1.5 text-sm font-medium mb-1 text-gray-700 dark:text-gray-300']) }}>
    <span>{{ $slot }}</span>
    @if ($tooltip)
        <span class="group relative inline-flex">
            <svg class="w-4 h-4 text-gray-400 hover:text-primary cursor-help flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m0 3.75h.007v.008H12v-.008zM21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="pointer-events-none absolute left-1/2 -translate-x-1/2 bottom-full mb-2 hidden group-hover:block w-56 rounded-lg bg-gray-800 dark:bg-gray-700 px-3 py-2 text-xs leading-relaxed text-white shadow-lg z-20">
                {{ $tooltip }}
                <span class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-800 dark:border-t-gray-700"></span>
            </span>
        </span>
    @endif
</label>
