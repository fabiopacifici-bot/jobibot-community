<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>JobiBot Community — Your AI Job Coach</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 text-gray-900 antialiased">
    <nav class="bg-white border-b border-gray-200 px-4 py-3 shadow-sm">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <a href="/" class="text-xl font-bold text-indigo-600 flex items-center gap-2">
                🤖 JobiBot <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full">Community</span>
            </a>
            <div class="flex items-center gap-4 text-sm">
                <a href="/" class="hover:text-indigo-600">Dashboard</a>
                <a href="/cv" class="hover:text-indigo-600">CV</a>
                <a href="/jobs" class="hover:text-indigo-600">Job Search</a>
                <a href="/interview" class="hover:text-indigo-600">Interview</a>
                <a href="/settings" class="hover:text-indigo-600">⚙️ Settings</a>
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-4 py-6">
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

    <footer class="text-center text-xs text-gray-400 py-6 border-t border-gray-100 mt-12">
        JobiBot Community Edition — Open source under MIT license. Built with Laravel, Livewire & NativePHP.
    </footer>

    @livewireScripts
</body>
</html>