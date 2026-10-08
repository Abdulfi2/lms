@props(['status'])
@php
    $map = [
        'draft' => ['label' => 'Draft', 'class' => 'bg-yellow-100 text-yellow-800'],
        'revision' => ['label' => 'Revisi', 'class' => 'bg-red-100 text-red-700'],
        'ready_to_publish' => ['label' => 'Siap Terbit', 'class' => 'bg-blue-100 text-blue-700'],
        'published' => ['label' => 'Published', 'class' => 'bg-green-100 text-green-800'],
        'archived' => ['label' => 'Archived', 'class' => 'bg-gray-100 text-gray-800'],
    ];
    $info = $map[$status] ?? ['label' => ucfirst($status), 'class' => 'bg-gray-100 text-gray-800'];
@endphp
<span {{ $attributes->merge(['class' => 'px-2 py-1 text-xs rounded-full whitespace-nowrap ' . $info['class']]) }}>{{ $info['label'] }}</span>
