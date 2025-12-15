@props([
    'eligibility',
])

@php
$config = match($eligibility) {
    'yes' => ['color' => 'emerald', 'label' => 'Eligible'],
    'no' => ['color' => 'rose', 'label' => 'Not Eligible'],
    'pending' => ['color' => 'amber', 'label' => 'Pending Review'],
    default => ['color' => 'gray', 'label' => 'Unknown'],
};
@endphp

<x-badge :color="$config['color']">
    {{ $config['label'] }}
</x-badge>

