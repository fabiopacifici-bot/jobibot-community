{{-- Mobile Bottom Tab Bar — visible on mobile only --}}
<nav class="mobile-nav fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-lg border-t border-gray-200 z-50 safe-area-bottom">
    <div class="flex items-center justify-around max-w-lg mx-auto">
        <a href="/" wire:navigate
           class="flex flex-col items-center gap-0.5 py-2 px-3 min-h-[50px] justify-center text-xs font-medium transition-colors
                  {{ request()->is('/') ? 'text-indigo-600' : 'text-gray-400 hover:text-gray-600' }}">
            <span class="text-xl">🏠</span>
            <span>Home</span>
        </a>
        <a href="/cv" wire:navigate
           class="flex flex-col items-center gap-0.5 py-2 px-3 min-h-[50px] justify-center text-xs font-medium transition-colors
                  {{ request()->is('cv') ? 'text-indigo-600' : 'text-gray-400 hover:text-gray-600' }}">
            <span class="text-xl">📄</span>
            <span>CV</span>
        </a>
        <a href="/jobs" wire:navigate
           class="flex flex-col items-center gap-0.5 py-2 px-3 min-h-[50px] justify-center text-xs font-medium transition-colors
                  {{ request()->is('jobs') ? 'text-indigo-600' : 'text-gray-400 hover:text-gray-600' }}">
            <span class="text-xl">🔍</span>
            <span>Jobs</span>
        </a>
        <a href="/interview" wire:navigate
           class="flex flex-col items-center gap-0.5 py-2 px-3 min-h-[50px] justify-center text-xs font-medium transition-colors
                  {{ request()->is('interview') ? 'text-indigo-600' : 'text-gray-400 hover:text-gray-600' }}">
            <span class="text-xl">🎯</span>
            <span>Interview</span>
        </a>
        <a href="/settings" wire:navigate
           class="flex flex-col items-center gap-0.5 py-2 px-3 min-h-[50px] justify-center text-xs font-medium transition-colors
                  {{ request()->is('settings') ? 'text-indigo-600' : 'text-gray-400 hover:text-gray-600' }}">
            <span class="text-xl">⚙️</span>
            <span>Settings</span>
        </a>
    </div>
</nav>