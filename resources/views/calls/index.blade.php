<x-layouts.app title="Calls">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Call Recordings</h1>
                <p class="mt-1 text-slate-500">Manage and analyze your uploaded call recordings</p>
            </div>
            <x-button :href="route('calls.create')" variant="primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Upload Call
            </x-button>
        </div>

        <x-card :padding="false">
            @if($calls->isEmpty())
                <div class="py-16 text-center">
                    <svg class="w-12 h-12 mx-auto text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-slate-900">No calls uploaded</h3>
                    <p class="mt-2 text-sm text-slate-500">Get started by uploading your first call recording.</p>
                    <div class="mt-6">
                        <x-button :href="route('calls.create')" variant="primary">
                            Upload Your First Call
                        </x-button>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left uppercase text-slate-500">File</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left uppercase text-slate-500">Status</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left uppercase text-slate-500">Score</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left uppercase text-slate-500">Eligibility</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left uppercase text-slate-500">Sentiment</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left uppercase text-slate-500">Date</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-right uppercase text-slate-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($calls as $call)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-slate-100">
                                                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-slate-900 truncate max-w-[200px]">{{ $call->original_filename }}</p>
                                                <p class="text-xs text-slate-500">{{ $call->formatted_file_size }} · {{ $call->formatted_duration }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-status-badge :status="$call->status" />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($call->lead_score !== null)
                                            <div class="flex items-center gap-2">
                                                <span class="text-lg font-bold text-slate-900">{{ $call->lead_score }}</span>
                                                <x-badge :color="$call->getScoreColor()" size="sm">
                                                    {{ $call->getScoreLabel() }}
                                                </x-badge>
                                            </div>
                                        @else
                                            <span class="text-sm text-slate-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-eligibility-badge :eligibility="$call->eligibility" />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($call->sentiment)
                                            <span class="text-xl" title="{{ ucfirst($call->sentiment) }}">{{ $call->getSentimentEmoji() }}</span>
                                        @else
                                            <span class="text-sm text-slate-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm whitespace-nowrap text-slate-500">
                                        {{ $call->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <x-button :href="route('calls.show', $call)" variant="ghost" size="sm">
                                            View
                                        </x-button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($calls->hasPages())
                    <div class="px-6 py-4 border-t border-slate-200">
                        {{ $calls->links() }}
                    </div>
                @endif
            @endif
        </x-card>
    </div>
</x-layouts.app>

