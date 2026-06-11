<div>
<div class="grid md:grid-cols-3 gap-6">
    {{-- Stats Cards --}}
    <div class="bg-white rounded-xl shadow-sm border p-5">
        <div class="text-sm text-gray-500">Total Simulations</div>
        <div class="text-3xl font-bold text-indigo-600">{{ $totalSims }}</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border p-5">
        <div class="text-sm text-gray-500">Completed</div>
        <div class="text-3xl font-bold text-green-600">{{ $completedSims }}</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border p-5">
        <div class="text-sm text-gray-500">Average Score</div>
        <div class="text-3xl font-bold {{ $avgScore && $avgScore >= 70 ? 'text-green-600' : 'text-amber-600' }}">
            {{ $avgScore ? round($avgScore) . '%' : '—' }}
        </div>
    </div>
</div>

@if($candidate && $candidate->bio)
    <div class="mt-6 bg-white rounded-xl shadow-sm border p-5">
        <h3 class="font-semibold text-gray-700 mb-2">📄 Your CV Summary</h3>
        <p class="text-gray-600 text-sm">{{ $candidate->bio }}</p>
    </div>
@endif

{{-- Recent Simulations --}}
@if($simulations->isNotEmpty())
    <div class="mt-6">
        <h3 class="font-semibold text-lg mb-3">📋 Recent Simulations</h3>
        <div class="space-y-3">
            @foreach($simulations as $sim)
                <div class="bg-white rounded-xl shadow-sm border p-4 flex items-center justify-between">
                    <div>
                        <span class="text-sm font-medium">
                            {{ $sim->jobAdvertisement->title ?? 'Custom Job' }}
                        </span>
                        <div class="text-xs text-gray-400">{{ $sim->created_at->diffForHumans() }}</div>
                    </div>
                    <div class="flex items-center gap-3">
                        @if($sim->cv_match_score)
                            <span class="text-xs bg-blue-50 text-blue-700 px-2 py-1 rounded-full">
                                Match: {{ $sim->cv_match_score }}%
                            </span>
                        @endif
                        <span class="text-xs px-2 py-1 rounded-full
                            {{ $sim->status === 'completed' ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700' }}">
                            {{ ucfirst($sim->status) }}
                        </span>
                        @if($sim->simulation_score)
                            <span class="text-sm font-bold">Score: {{ $sim->simulation_score }}%</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@else
    <div class="mt-6 bg-white rounded-xl shadow-sm border p-8 text-center">
        <div class="text-4xl mb-3">🚀</div>
        <h3 class="text-lg font-semibold text-gray-700 mb-2">Ready to practice?</h3>
        <p class="text-gray-500 text-sm mb-4">Upload your CV, search for jobs, or start an interview simulation.</p>
        <div class="flex gap-3 justify-center">
            <a href="/cv" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700" wire:navigate>📄 Upload CV</a>
            <a href="/jobs" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-200" wire:navigate>🔍 Search Jobs</a>
            <a href="/interview" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-200" wire:navigate>🎯 Start Interview</a>
        </div>
    </div>
@endif
</div>