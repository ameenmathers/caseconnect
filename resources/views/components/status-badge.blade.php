@props([
    'status',
])

@php
$config = match($status) {
    'pending' => ['color' => 'slate', 'icon' => '⏳', 'label' => 'Pending'],
    'processing' => ['color' => 'blue', 'icon' => '⚙️', 'label' => 'Processing'],
    'completed' => ['color' => 'emerald', 'icon' => '✓', 'label' => 'Completed'],
    'failed' => ['color' => 'rose', 'icon' => '✕', 'label' => 'Failed'],
    default => ['color' => 'gray', 'icon' => '?', 'label' => ucfirst($status)],
};
@endphp

<x-badge :color="$config['color']">
    <span class="mr-1">{{ $config['icon'] }}</span>
    {{ $config['label'] }}
</x-badge>

