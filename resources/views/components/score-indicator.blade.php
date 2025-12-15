@props([
    'score' => 0,
    'size' => 'md',
    'showLabel' => true,
])

@php
$percentage = min(100, max(0, $score));

$color = match(true) {
    $score >= 80 => 'emerald',
    $score >= 60 => 'amber',
    $score >= 40 => 'orange',
    $score >= 20 => 'rose',
    default => 'slate',
};

$gradientColors = [
    'emerald' => 'from-emerald-500 to-emerald-600',
    'amber' => 'from-amber-500 to-amber-600',
    'orange' => 'from-orange-500 to-orange-600',
    'rose' => 'from-rose-500 to-rose-600',
    'slate' => 'from-slate-400 to-slate-500',
];

$sizes = [
    'sm' => ['ring' => 'w-12 h-12', 'text' => 'text-sm', 'stroke' => 4],
    'md' => ['ring' => 'w-16 h-16', 'text' => 'text-lg', 'stroke' => 5],
    'lg' => ['ring' => 'w-24 h-24', 'text' => 'text-2xl', 'stroke' => 6],
];

$currentSize = $sizes[$size];
$circumference = 2 * pi() * 45;
$offset = $circumference - ($percentage / 100) * $circumference;
@endphp

<div class="flex flex-col items-center gap-2">
    <div class="relative {{ $currentSize['ring'] }}">
        <svg class="w-full h-full -rotate-90" viewBox="0 0 100 100">
            <circle
                cx="50"
                cy="50"
                r="45"
                fill="none"
                stroke="currentColor"
                stroke-width="{{ $currentSize['stroke'] }}"
                class="text-slate-100"
            />
            <circle
                cx="50"
                cy="50"
                r="45"
                fill="none"
                stroke="url(#gradient-{{ $color }})"
                stroke-width="{{ $currentSize['stroke'] }}"
                stroke-linecap="round"
                stroke-dasharray="{{ $circumference }}"
                stroke-dashoffset="{{ $offset }}"
                class="transition-all duration-500 ease-out"
            />
            <defs>
                <linearGradient id="gradient-{{ $color }}" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" class="[stop-color:var(--tw-gradient-from)]" style="stop-color: {{ $color === 'emerald' ? '#10b981' : ($color === 'amber' ? '#f59e0b' : ($color === 'orange' ? '#f97316' : ($color === 'rose' ? '#f43f5e' : '#64748b'))) }}" />
                    <stop offset="100%" class="[stop-color:var(--tw-gradient-to)]" style="stop-color: {{ $color === 'emerald' ? '#059669' : ($color === 'amber' ? '#d97706' : ($color === 'orange' ? '#ea580c' : ($color === 'rose' ? '#e11d48' : '#475569'))) }}" />
                </linearGradient>
            </defs>
        </svg>
        <div class="absolute inset-0 flex items-center justify-center">
            <span class="{{ $currentSize['text'] }} font-bold text-slate-700">{{ $score }}</span>
        </div>
    </div>

    @if($showLabel)
        <span class="text-sm font-medium text-slate-600">
            @if($score >= 80) Hot Lead
            @elseif($score >= 60) Warm Lead
            @elseif($score >= 40) Lukewarm
            @elseif($score >= 20) Cold Lead
            @else Very Cold
            @endif
        </span>
    @endif
</div>

