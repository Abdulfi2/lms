@props(['priority'])
@php
    $map = [
        'tinggi' => ['label' => 'Tinggi', 'class' => 'bg-red-100 text-red-700'],
        'sedang' => ['label' => 'Sedang', 'class' => 'bg-yellow-100 text-yellow-700'],
        'rendah' => ['label' => 'Rendah', 'class' => 'bg-gray-100 text-gray-600'],
    ];
    $info = $map[$priority] ?? ['label' => ucfirst($priority), 'class' => 'bg-gray-100 text-gray-800'];
@endphp
<span {{ $attributes->merge(['class' => 'px-2 py-1 text-xs rounded-full whitespace-nowrap ' . $info['class']]) }}>{{ $info['label'] }}</span>
