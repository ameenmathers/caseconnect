<x-layouts.app :title="$call->original_filename">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('calls.index') }}" class="inline-flex items-center text-sm text-slate-500 hover:text-slate-700">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Calls
                </a>
                <h1 class="mt-2 text-2xl font-bold text-slate-900 truncate max-w-xl">{{ $call->original_filename }}</h1>
            </div>
            <div class="flex items-center gap-3">
                @if($call->transcript)
                    <form action="{{ route('calls.reanalyze', $call) }}" method="POST" class="inline">
                        @csrf
                        <x-button type="submit" variant="secondary" size="sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Reanalyze
                        </x-button>
                    </form>
                @endif
                <form action="{{ route('calls.destroy', $call) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this call?')">
                    @csrf
                    @method('DELETE')
                    <x-button type="submit" variant="danger" size="sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete
                    </x-button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <x-card>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-slate-900">File Details</h3>
                    <x-status-badge :status="$call->status" />
                </div>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm text-slate-500">Size</dt>
                        <dd class="text-sm font-medium text-slate-900">{{ $call->formatted_file_size }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-slate-500">Duration</dt>
                        <dd class="text-sm font-medium text-slate-900">{{ $call->formatted_duration }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-slate-500">Type</dt>
                        <dd class="text-sm font-medium text-slate-900">{{ $call->mime_type ?? 'Unknown' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-slate-500">Uploaded</dt>
                        <dd class="text-sm font-medium text-slate-900">{{ $call->created_at->format('M d, Y H:i') }}</dd>
                    </div>
                    @if($call->processed_at)
                        <div class="flex justify-between">
                            <dt class="text-sm text-slate-500">Processed</dt>
                            <dd class="text-sm font-medium text-slate-900">{{ $call->processed_at->format('M d, Y H:i') }}</dd>
                        </div>
                    @endif
                </dl>
            </x-card>

            <x-card class="flex flex-col items-center justify-center text-center">
                <h3 class="text-sm font-medium text-slate-500 mb-4">Lead Score</h3>
                @if($call->lead_score !== null)
                    <x-score-indicator :score="$call->lead_score" size="lg" />
                @else
                    <div class="py-8">
                        <p class="text-slate-400">Not yet scored</p>
                    </div>
                @endif
            </x-card>

            <x-card>
                <h3 class="text-lg font-semibold text-slate-900 mb-4">Analysis Results</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between items-center">
                        <dt class="text-sm text-slate-500">Eligibility</dt>
                        <dd><x-eligibility-badge :eligibility="$call->eligibility" /></dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-sm text-slate-500">Sentiment</dt>
                        <dd class="flex items-center gap-2">
                            @if($call->sentiment)
                                <span class="text-xl">{{ $call->getSentimentEmoji() }}</span>
                                <span class="text-sm font-medium text-slate-900 capitalize">{{ $call->sentiment }}</span>
                            @else
                                <span class="text-sm text-slate-400">—</span>
                            @endif
                        </dd>
                    </div>
                    @if($call->sentiment_score !== null)
                        <div class="flex justify-between items-center">
                            <dt class="text-sm text-slate-500">Sentiment Score</dt>
                            <dd class="text-sm font-medium text-slate-900">{{ number_format($call->sentiment_score, 2) }}</dd>
                        </div>
                    @endif
                </dl>
            </x-card>
        </div>

        @if($call->status === 'failed')
            <x-alert type="error">
                <strong>Processing Failed:</strong> {{ $call->error_message ?? 'An unknown error occurred during processing.' }}
            </x-alert>
        @endif

        @if($call->status === 'pending' || $call->status === 'processing')
            <x-alert type="info">
                <strong>Processing in progress...</strong> The call is being transcribed and analyzed. This page will update automatically when complete.
            </x-alert>
        @endif

        @if($call->keywords_detected && count($call->keywords_detected) > 0)
            <x-card>
                <h3 class="text-lg font-semibold text-slate-900 mb-4">Keywords Detected</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($call->keywords_detected as $keyword)
                        <x-badge color="indigo" size="lg">{{ $keyword }}</x-badge>
                    @endforeach
                </div>
            </x-card>
        @endif

        @if($call->next_actions && count($call->next_actions) > 0)
            <x-card>
                <h3 class="text-lg font-semibold text-slate-900 mb-4">Recommended Next Actions</h3>
                <ul class="space-y-2">
                    @foreach($call->next_actions as $action)
                        <li class="flex items-start gap-3">
                            <div class="flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                            <span class="text-slate-700">{{ $action }}</span>
                        </li>
                    @endforeach
                </ul>
            </x-card>
        @endif

        @if($call->summary)
            <x-card>
                <h3 class="text-lg font-semibold text-slate-900 mb-4">Call Summary</h3>
                <p class="text-slate-700 leading-relaxed">{{ $call->summary }}</p>
            </x-card>
        @endif

        @if($call->transcript)
            <x-card>
                <h3 class="text-lg font-semibold text-slate-900 mb-4">Full Transcript</h3>
                <div class="p-4 bg-slate-50 rounded-lg border border-slate-200 max-h-96 overflow-y-auto">
                    <p class="text-sm text-slate-700 whitespace-pre-wrap leading-relaxed">{{ $call->transcript }}</p>
                </div>
            </x-card>
        @endif
    </div>

    @if($call->status === 'pending' || $call->status === 'processing')
        <script>
            setTimeout(function() {
                window.location.reload();
            }, 10000);
        </script>
    @endif
</x-layouts.app>

