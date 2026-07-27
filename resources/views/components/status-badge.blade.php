@props(['status'])

@php
    $normalized = is_bool($status) ? ($status ? 'published' : 'draft') : strtolower((string) $status);

    $styles = [
        'published' => 'bg-green-100 text-green-800',
        'draft' => 'bg-yellow-100 text-yellow-800',
        'active' => 'bg-green-100 text-green-800',
        'archived' => 'bg-gray-100 text-gray-800',
    ];

    $class = $styles[$normalized] ?? 'bg-gray-100 text-gray-800';
    $label = ucfirst($normalized);
@endphp

<span {{ $attributes->merge(['class' => "px-2 py-1 text-xs font-bold rounded-full uppercase {$class}"]) }}>
    {{ $label }}
</span>
