<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>JobiBot Community — Your AI Job Coach</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 text-gray-900 antialiased select-none">
    {{-- Native title bar drag region --}}
    <div class="titlebar-drag-region h-9 bg-indigo-600 flex items-center px-4">
        <span class="text-white text-sm font-semibold tracking-wide">🤖 JobiBot Community</span>
    </div>

    {{-- Desktop sidebar navigation --}}
    <div class="flex h-[calc(100vh-2.25rem)]">
        <aside class="w-56 bg-white border-r border-gray-200 flex flex-col py-4">
            <nav class="flex-1 space-y-1 px-3">
                <a href="/" wire:navigate class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                    {{ request()->is('/') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                    <span class="text-lg">🏠</span> Dashboard
                </a>
                <a href="/cv" wire:navigate class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                    {{ request()->is('cv') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                    <span class="text-lg">📄</span> CV Upload
                </a>
                <a href="/jobs" wire:navigate class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                    {{ request()->is('jobs') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                    <span class="text-lg">🔍</span> Job Search
                </a>
                <a href="/interview" wire:navigate class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                    {{ request()->is('interview') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                    <span class="text-lg">🎯</span> Interview
                </a>
            </nav>
            <div class="px-3 pt-3 border-t border-gray-100">
                <a href="/settings" wire:navigate class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                    {{ request()->is('settings') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                    <span class="text-lg">⚙️</span> Settings
                </a>
            </div>
            <div class="px-4 pt-4">
                <p class="text-[10px] text-gray-400 leading-tight">
                    JobiBot Community Edition<br>
                    MIT License · v{{ config('app.version', '0.2.0') }}
                </p>
            </div>
        </aside>

        {{-- Main content area --}}
        <main class="flex-1 overflow-y-auto px-6 py-5">
            @if (session('message'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                    {{ session('message') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @livewireScripts
</body>
</html>