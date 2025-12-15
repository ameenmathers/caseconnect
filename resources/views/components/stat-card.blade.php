@props([
    'title',
    'value',
    'icon' => null,
    'trend' => null,
    'color' => 'indigo',
])

@php
$iconColors = [
    'indigo' => 'bg-indigo-100 text-indigo-600',
    'emerald' => 'bg-emerald-100 text-emerald-600',
    'amber' => 'bg-amber-100 text-amber-600',
    'rose' => 'bg-rose-100 text-rose-600',
    'sky' => 'bg-sky-100 text-sky-600',
    'violet' => 'bg-violet-100 text-violet-600',
];
@endphp

<x-card>
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm font-medium text-slate-500">{{ $title }}</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $value }}</p>
            @if($trend)
                <p class="mt-2 text-sm {{ $trend > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                    @if($trend > 0)
                        <svg class="inline w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    @else
                        <svg class="inline w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                        </svg>
                    @endif
                    {{ abs($trend) }}% from last period
                </p>
            @endif
        </div>
        @if($icon)
            <div class="p-3 rounded-lg {{ $iconColors[$color] ?? $iconColors['indigo'] }}">
                {!! $icon !!}
            </div>
        @endif
    </div>
</x-card>

