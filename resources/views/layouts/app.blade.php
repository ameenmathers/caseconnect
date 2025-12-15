<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'CaseConnect' }} | AI Call Summarizer</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|jetbrains-mono:400,500" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Instrument Sans', sans-serif; }
        code, pre { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="min-h-screen bg-slate-50">
    <nav class="bg-white border-b border-slate-200">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center gap-8">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-violet-600">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <span class="text-lg font-semibold text-slate-900">CaseConnect</span>
                    </a>

                    <div class="hidden sm:flex sm:items-center sm:gap-1">
                        <a href="{{ route('dashboard') }}" class="px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('dashboard') ? 'text-indigo-600 bg-indigo-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('calls.index') }}" class="px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('calls.*') ? 'text-indigo-600 bg-indigo-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            Calls
                        </a>
                    </div>
                </div>

                <div class="flex items-center">
                    <x-button :href="route('calls.create')" variant="primary" size="sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Upload Call
                    </x-button>
                </div>
            </div>
        </div>
    </nav>

    <main class="py-8">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6">
                    <x-alert type="success" dismissible>
                        {{ session('success') }}
                    </x-alert>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6">
                    <x-alert type="error" dismissible>
                        {{ session('error') }}
                    </x-alert>
                </div>
            @endif

            {{ $slot }}
        </div>
    </main>

    <footer class="py-6 mt-12 border-t border-slate-200 bg-white">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <p class="text-sm text-center text-slate-500">
                CaseConnect AI Call Summarizer &mdash; Personal Injury Lead Qualification System
            </p>
        </div>
    </footer>
</body>
</html>

