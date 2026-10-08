@props(['status'])
@php
    $map = [
        'baru' => ['label' => 'Baru', 'class' => 'bg-blue-100 text-blue-700'],
        'diproses' => ['label' => 'Diproses', 'class' => 'bg-orange-100 text-orange-700'],
        'menunggu_user' => ['label' => 'Menunggu User', 'class' => 'bg-purple-100 text-purple-700'],
        'selesai' => ['label' => 'Selesai', 'class' => 'bg-green-100 text-green-800'],
    ];
    $info = $map[$status] ?? ['label' => ucfirst($status), 'class' => 'bg-gray-100 text-gray-800'];
@endphp
<span {{ $attributes->merge(['class' => 'px-2 py-1 text-xs rounded-full whitespace-nowrap ' . $info['class']]) }}>{{ $info['label'] }}</span>
