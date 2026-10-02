@props(['items' => []])
{{--
    $items: array of steps, in order. Each step is one of:
    - ['label' => string, 'url' => string|null]                 plain link (url null => current page, plain text)
    - ['label' => string, 'dropdown' => [                       clickable label that opens a list of siblings
          ['label' => string, 'url' => string|null, 'locked' => bool, 'active' => bool],
          ...
      ]]
--}}
<nav aria-label="Breadcrumb" class="flex items-center flex-wrap gap-1.5 text-sm">
    @foreach ($items as $item)
        @if (!$loop->first)
            <svg class="w-3.5 h-3.5 shrink-0 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        @endif

        @if (isset($item['dropdown']))
            <div class="relative" x-data="{ open: false }">
                <button type="button" @click="open = !open" @click.away="open = false"
                    class="flex items-center gap-1 text-gray-500 dark:text-gray-400 hover:text-primary transition">
                    <span class="truncate max-w-[160px]">{{ $item['label'] }}</span>
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-transition.duration.150 x-cloak
                    class="absolute left-0 mt-1 w-64 max-h-80 overflow-y-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg border dark:border-gray-700 py-1 z-50">
                    @foreach ($item['dropdown'] as $sub)
                        @if (!empty($sub['locked']))
                            <span title="Selesaikan materi sebelumnya untuk membuka ini"
                                class="flex items-center gap-2 px-3 py-2 text-sm text-gray-400 dark:text-gray-500 cursor-not-allowed">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <span class="truncate">{{ $sub['label'] }}</span>
                            </span>
                        @else
                            <a href="{{ $sub['url'] }}"
                                class="flex items-center gap-2 px-3 py-2 text-sm truncate transition {{ !empty($sub['active']) ? 'bg-primary/10 text-primary font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                                {{ $sub['label'] }}
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        @elseif (!empty($item['url']) && !$loop->last)
            <a href="{{ $item['url'] }}" class="text-gray-500 dark:text-gray-400 hover:text-primary transition truncate max-w-[200px]">
                {{ $item['label'] }}
            </a>
        @else
            <span class="text-gray-800 dark:text-white font-medium truncate max-w-[240px]">{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>
