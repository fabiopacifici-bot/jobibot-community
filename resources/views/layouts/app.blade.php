<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>JobiBot Community — Your AI Job Coach</title>
    <link rel="icon" href="{{ asset('images/favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    {{-- Dark mode: respect system preference, allow override via localStorage --}}
    <script>
        (function () {
            const stored = localStorage.getItem('jobibot-theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (stored === 'dark' || (!stored && prefersDark)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
</head>
<body class="h-full bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 antialiased">

<div class="flex h-full">
    {{-- Sidebar (desktop) — hidden on mobile --}}
    <aside class="hidden md:flex md:flex-col md:w-64 bg-gray-50 dark:bg-gray-950 border-r border-gray-200 dark:border-gray-800">
        {{-- Titlebar drag region --}}
        <div class="titlebar-region h-10 shrink-0"></div>

        {{-- Logo + Brand --}}
        <a href="/" class="flex items-center gap-3 px-4 py-3" wire:navigate>
            <img src="{{ asset('images/logo.png') }}" alt="JobiBot" class="w-8 h-8 rounded-lg">
            <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">JobiBot</span>
            <span class="text-[10px] bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 px-1.5 py-0.5 rounded-full font-medium">CE</span>
        </a>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-2 space-y-1">
            <x-nav-link href="/" :active="request()->is('/')">🏠 Dashboard</x-nav-link>
            <x-nav-link href="/cv" :active="request()->is('cv*')">📄 CV</x-nav-link>
            <x-nav-link href="/jobs" :active="request()->is('jobs*')">🔍 Job Search</x-nav-link>
            <x-nav-link href="/interview" :active="request()->is('interview*')">🎯 Interview</x-nav-link>
            <x-nav-link href="/settings" :active="request()->is('settings*')">⚙️ Settings</x-nav-link>
        </nav>

        {{-- Theme toggle --}}
        <div class="px-3 py-3 border-t border-gray-200 dark:border-gray-800">
            <button onclick="toggleTheme()" class="flex items-center gap-2 w-full px-3 py-2 text-sm rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-800 transition-colors">
                <span id="theme-icon">🌙</span>
                <span id="theme-label">Dark Mode</span>
            </button>
        </div>

        {{-- Footer --}}
        <div class="px-4 py-3 text-[10px] text-gray-400 dark:text-gray-600">
            JobiBot Community Edition<br>MIT License
        </div>
    </aside>

    {{-- Main content area --}}
    <div class="flex-1 flex flex-col min-w-0">
        {{-- Mobile header (visible only on mobile) --}}
        <header class="md:hidden bg-white dark:bg-gray-950 border-b border-gray-200 dark:border-gray-800 px-4 py-3 flex items-center gap-3">
            <img src="{{ asset('images/logo.png') }}" alt="JobiBot" class="w-7 h-7 rounded-lg">
            <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">JobiBot</span>
        </header>

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto px-4 py-6 md:px-8 md:py-8 pb-24 md:pb-8">
            @if (session('message'))
                <div class="mb-4 p-3 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 rounded-lg text-sm">
                    {{ session('message') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

{{-- Mobile Bottom Navigation — visible only on mobile --}}
<x-mobile-nav />

{{-- Theme toggle script --}}
<script>
    function toggleTheme() {
        const html = document.documentElement;
        const icon = document.getElementById('theme-icon');
        const label = document.getElementById('theme-label');
        const isDark = html.classList.toggle('dark');
        localStorage.setItem('jobibot-theme', isDark ? 'dark' : 'light');
        icon.textContent = isDark ? '☀️' : '🌙';
        label.textContent = isDark ? 'Light Mode' : 'Dark Mode';
    }
    // Set initial icon state
    (function () {
        const icon = document.getElementById('theme-icon');
        const label = document.getElementById('theme-label');
        if (icon && document.documentElement.classList.contains('dark')) {
            icon.textContent = '☀️';
            label.textContent = 'Light Mode';
        }
    })();
</script>

@livewireScripts
</body>
</html>