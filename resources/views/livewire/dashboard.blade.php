<div>
<div class="grid md:grid-cols-3 gap-6">
    {{-- Stats Cards --}}
    <div class="bg-white dark:bg-gray-950 rounded-xl border border-gray-200 dark:border-gray-800 p-5 shadow-sm">
        <div class="text-sm text-gray-500 dark:text-gray-400">Total Simulations</div>
        <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $totalSims }}</div>
    </div>
    <div class="bg-white dark:bg-gray-950 rounded-xl border border-gray-200 dark:border-gray-800 p-5 shadow-sm">
        <div class="text-sm text-gray-500 dark:text-gray-400">Completed</div>
        <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $completedSims }}</div>
    </div>
    <div class="bg-white dark:bg-gray-950 rounded-xl border border-gray-200 dark:border-gray-800 p-5 shadow-sm">
        <div class="text-sm text-gray-500 dark:text-gray-400">Average Score</div>
        <div class="text-3xl font-bold {{ $avgScore && $avgScore >= 70 ? 'text-green-600 dark:text-green-400' : 'text-amber-600 dark:text-amber-400' }}">
            {{ $avgScore ? round($avgScore) . '%' : '—' }}
        </div>
    </div>
</div>

@if($candidate && $candidate->bio)
    <div class="mt-6 bg-white dark:bg-gray-950 rounded-xl border border-gray-200 dark:border-gray-800 p-5 shadow-sm">
        <h3 class="font-semibold text-gray-700 dark:text-gray-300 mb-2">📄 Your CV Summary</h3>
        <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $candidate->bio }}</p>
    </div>
@endif

{{-- Recent Simulations --}}
@if($simulations->isNotEmpty())
    <div class="mt-6">
        <h3 class="font-semibold text-lg mb-3 text-gray-900 dark:text-gray-100">📋 Recent Simulations</h3>
        <div class="space-y-3">
            @foreach($simulations as $sim)
                <div class="bg-white dark:bg-gray-950 rounded-xl border border-gray-200 dark:border-gray-800 p-4 flex items-center justify-between shadow-sm">
                    <div>
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $sim->jobAdvertisement->title ?? 'Custom Job' }}
                        </span>
                        <div class="text-xs text-gray-400 dark:text-gray-500">{{ $sim->created_at->diffForHumans() }}</div>
                    </div>
                    <div class="flex items-center gap-3">
                        @if($sim->cv_match_score)
                            <span class="text-xs bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-2 py-1 rounded-full">
                                Match: {{ $sim->cv_match_score }}%
                            </span>
                        @endif
                        <span class="text-xs px-2 py-1 rounded-full
                            {{ $sim->status === 'completed' ? 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300' : 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300' }}">
                            {{ ucfirst($sim->status) }}
                        </span>
                        @if($sim->simulation_score)
                            <span class="text-sm font-bold text-gray-700 dark:text-gray-300">Score: {{ $sim->simulation_score }}%</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@else
    <div class="mt-6 bg-white dark:bg-gray-950 rounded-xl border border-gray-200 dark:border-gray-800 p-8 text-center shadow-sm">
        <div class="text-4xl mb-3">🚀</div>
        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">Ready to practice?</h3>
        <p class="text-gray-500 dark:text-gray-400 text-sm mb-4">Upload your CV, search for jobs, or start an interview simulation.</p>
        <div class="flex gap-3 justify-center flex-wrap">
            <a href="/cv" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 transition-colors" wire:navigate>📄 Upload CV</a>
            <a href="/jobs" class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg text-sm hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors" wire:navigate>🔍 Search Jobs</a>
            <a href="/interview" class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg text-sm hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors" wire:navigate>🎯 Start Interview</a>
        </div>
    </div>
@endif
</div>