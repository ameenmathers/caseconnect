<x-layouts.app title="Upload Call">
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('calls.index') }}" class="inline-flex items-center text-sm text-slate-500 hover:text-slate-700">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Calls
            </a>
        </div>

        <x-card>
            <div class="mb-6">
                <h1 class="text-xl font-bold text-slate-900">Upload Call Recording</h1>
                <p class="mt-1 text-sm text-slate-500">Upload an audio file to transcribe and analyze for lead scoring</p>
            </div>

            <form action="{{ route('calls.store') }}" method="POST" enctype="multipart/form-data" id="upload-form">
                @csrf

                <div class="space-y-6">
                    <div
                        id="drop-zone"
                        class="relative border-2 border-dashed rounded-xl p-8 text-center transition-colors border-slate-300 hover:border-indigo-400 hover:bg-indigo-50/50"
                    >
                        <input
                            type="file"
                            name="audio"
                            id="audio"
                            accept=".mp3,.wav,.m4a,.ogg,.webm,.flac,.mp4,.mpeg"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                            required
                        />

                        <div id="drop-zone-content">
                            <svg class="w-12 h-12 mx-auto text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <p class="mt-4 text-sm font-medium text-slate-700">
                                Drop your audio file here, or <span class="text-indigo-600">browse</span>
                            </p>
                            <p class="mt-2 text-xs text-slate-500">
                                Supported formats: MP3, WAV, M4A, OGG, WEBM, FLAC (max 50MB)
                            </p>
                        </div>

                        <div id="file-preview" class="hidden">
                            <div class="flex items-center justify-center gap-3">
                                <div class="flex items-center justify-center w-12 h-12 rounded-full bg-indigo-100">
                                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                    </svg>
                                </div>
                                <div class="text-left">
                                    <p id="file-name" class="text-sm font-medium text-slate-900"></p>
                                    <p id="file-size" class="text-xs text-slate-500"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @error('audio')
                        <x-alert type="error">{{ $message }}</x-alert>
                    @enderror

                    <div class="flex items-center justify-end gap-3">
                        <x-button :href="route('calls.index')" variant="secondary">
                            Cancel
                        </x-button>
                        <x-button type="submit" variant="primary" id="submit-btn">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            Upload & Analyze
                        </x-button>
                    </div>
                </div>
            </form>
        </x-card>

        <x-card class="mt-6">
            <h3 class="text-sm font-semibold text-slate-900">What happens after upload?</h3>
            <div class="mt-4 space-y-4">
                <div class="flex gap-3">
                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 text-sm font-bold shrink-0">1</div>
                    <div>
                        <p class="text-sm font-medium text-slate-900">Transcription</p>
                        <p class="text-sm text-slate-500">AI transcribes the audio using AssemblyAI</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 text-sm font-bold shrink-0">2</div>
                    <div>
                        <p class="text-sm font-medium text-slate-900">Analysis</p>
                        <p class="text-sm text-slate-500">Keywords, sentiment, and lead intent are analyzed</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 text-sm font-bold shrink-0">3</div>
                    <div>
                        <p class="text-sm font-medium text-slate-900">Scoring</p>
                        <p class="text-sm text-slate-500">Lead score (0-100) and eligibility are calculated</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 text-sm font-bold shrink-0">4</div>
                    <div>
                        <p class="text-sm font-medium text-slate-900">Recommendations</p>
                        <p class="text-sm text-slate-500">Next actions for agents are generated</p>
                    </div>
                </div>
            </div>
        </x-card>
    </div>

    <script>
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('audio');
        const filePreview = document.getElementById('file-preview');
        const dropZoneContent = document.getElementById('drop-zone-content');
        const fileName = document.getElementById('file-name');
        const fileSize = document.getElementById('file-size');

        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                const file = this.files[0];
                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);
                dropZoneContent.classList.add('hidden');
                filePreview.classList.remove('hidden');
                dropZone.classList.add('border-indigo-500', 'bg-indigo-50');
            }
        });

        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('border-indigo-500', 'bg-indigo-50');
        });

        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            if (!fileInput.files.length) {
                this.classList.remove('border-indigo-500', 'bg-indigo-50');
            }
        });

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
    </script>
</x-layouts.app>

