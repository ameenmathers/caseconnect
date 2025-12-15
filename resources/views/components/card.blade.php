@props([
    'padding' => true,
    'hover' => false,
])

@php
$classes = 'bg-white rounded-xl border border-slate-200 shadow-sm';

if ($padding) {
    $classes .= ' p-6';
}

if ($hover) {
    $classes .= ' hover:shadow-md hover:border-slate-300 transition-all duration-200';
}
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>

