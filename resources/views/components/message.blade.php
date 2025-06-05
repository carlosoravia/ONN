@props(['type' => 'info'])

@php
    $colors = [
        'success' => 'bg-green-100 text-green-800',
        'error' => 'bg-red-100 text-red-800',
        'info' => 'bg-blue-100 text-blue-800',
    ];
@endphp

<div class="{{ $colors[$type] ?? $colors['info'] }} p-4 rounded mb-4 w-max h-max">
    {{ $slot }}
</div>
