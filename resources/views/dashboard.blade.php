<x-layouts.app title="Dashboard">
    <div class="space-y-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Dashboard</h1>
            <p class="mt-1 text-slate-500">Overview of your call analysis and lead scoring metrics</p>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <x-stat-card
                title="Total Calls"
                :value="$stats['total_calls']"
                color="indigo"
            >
                <x-slot:icon>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card
                title="High Value Leads"
                :value="$stats['high_value_leads']"
                color="emerald"
            >
                <x-slot:icon>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card
                title="Avg Lead Score"
                :value="$stats['avg_score']"
                color="amber"
            >
                <x-slot:icon>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card
                title="Conversion Rate"
                :value="$stats['conversion_rate'] . '%'"
                color="violet"
            >
                <x-slot:icon>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </x-slot:icon>
            </x-stat-card>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <x-card class="lg:col-span-1">
                <h3 class="text-lg font-semibold text-slate-900">Processing Status</h3>
                <div class="mt-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-600">Completed</span>
                        <span class="font-medium text-emerald-600">{{ $stats['completed_calls'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-600">Pending/Processing</span>
                        <span class="font-medium text-amber-600">{{ $stats['pending_calls'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-600">Failed</span>
                        <span class="font-medium text-rose-600">{{ $stats['failed_calls'] }}</span>
                    </div>
                </div>
            </x-card>

            <x-card class="lg:col-span-1">
                <h3 class="text-lg font-semibold text-slate-900">Sentiment Analysis</h3>
                <div class="mt-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-lg">😊</span>
                            <span class="text-sm text-slate-600">Positive</span>
                        </div>
                        <span class="font-medium text-emerald-600">{{ $stats['sentiment_breakdown']['positive'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-lg">😐</span>
                            <span class="text-sm text-slate-600">Neutral</span>
                        </div>
                        <span class="font-medium text-slate-600">{{ $stats['sentiment_breakdown']['neutral'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-lg">😟</span>
                            <span class="text-sm text-slate-600">Negative</span>
                        </div>
                        <span class="font-medium text-rose-600">{{ $stats['sentiment_breakdown']['negative'] }}</span>
                    </div>
                </div>
            </x-card>

            <x-card class="lg:col-span-1">
                <h3 class="text-lg font-semibold text-slate-900">Eligibility</h3>
                <div class="mt-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-600">Eligible Leads</span>
                        <span class="font-medium text-emerald-600">{{ $stats['eligible_calls'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-600">Total Processed</span>
                        <span class="font-medium text-slate-900">{{ $stats['completed_calls'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-600">Eligibility Rate</span>
                        <span class="font-medium text-indigo-600">
                            {{ $stats['completed_calls'] > 0 ? round(($stats['eligible_calls'] / $stats['completed_calls']) * 100, 1) : 0 }}%
                        </span>
                    </div>
                </div>
            </x-card>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <x-card>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-slate-900">Recent Calls</h3>
                    <x-button :href="route('calls.index')" variant="ghost" size="sm">
                        View All
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </x-button>
                </div>

                @if($recentCalls->isEmpty())
                    <p class="py-8 text-sm text-center text-slate-500">No calls recorded yet</p>
                @else
                    <div class="space-y-3">
                        @foreach($recentCalls as $call)
                            <a href="{{ route('calls.show', $call) }}" class="flex items-center justify-between p-3 -mx-3 rounded-lg hover:bg-slate-50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-slate-100">
                                        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-slate-900 truncate max-w-[200px]">{{ $call->original_filename }}</p>
                                        <p class="text-xs text-slate-500">{{ $call->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <x-status-badge :status="$call->status" />
                            </a>
                        @endforeach
                    </div>
                @endif
            </x-card>

            <x-card>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-slate-900">High Value Leads</h3>
                    <x-badge color="emerald">Score 70+</x-badge>
                </div>

                @if($highValueLeads->isEmpty())
                    <p class="py-8 text-sm text-center text-slate-500">No high value leads yet</p>
                @else
                    <div class="space-y-3">
                        @foreach($highValueLeads as $lead)
                            <a href="{{ route('calls.show', $lead) }}" class="flex items-center justify-between p-3 -mx-3 rounded-lg hover:bg-slate-50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <x-score-indicator :score="$lead->lead_score" size="sm" :showLabel="false" />
                                    <div>
                                        <p class="text-sm font-medium text-slate-900 truncate max-w-[180px]">{{ $lead->original_filename }}</p>
                                        <p class="text-xs text-slate-500">{{ $lead->processed_at?->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <x-eligibility-badge :eligibility="$lead->eligibility" />
                            </a>
                        @endforeach
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</x-layouts.app>

