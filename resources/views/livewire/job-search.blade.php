<div>
    <div class="mb-6">
        <h2 class="text-xl font-bold mb-1">🔍 Job Search</h2>
        <p class="text-sm text-gray-500">Search remote jobs via Remotive API</p>
    </div>

    <div class="mb-4">
        <input type="text" wire:model.live.debounce.500ms="searchTerm"
               placeholder="e.g. Laravel Developer, React, Designer..."
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>

    <div wire:loading wire:target="searchJobs" class="text-center py-4">
        <div class="animate-spin inline-block w-6 h-6 border-2 border-indigo-600 border-t-transparent rounded-full"></div>
    </div>

    @if($selectedJob)
        <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-5 mb-4">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="font-bold text-lg">{{ $selectedJob['title'] ?? 'Untitled' }}</h3>
                    <p class="text-sm text-gray-500">{{ $selectedJob['company_name'] ?? '' }} — {{ $selectedJob['candidate_required_location'] ?? 'Remote' }}</p>
                    @if(!empty($selectedJob['salary']))
                        <p class="text-sm text-green-700 mt-1">💰 {{ $selectedJob['salary'] }}</p>
                    @endif
                </div>
                <button wire:click="$set('selectedJob', null)" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>
            <div class="mt-3 text-sm text-gray-700 max-h-48 overflow-y-auto">
                {!! nl2br(e(Str::limit($selectedJob['description'] ?? '', 800))) !!}
            </div>
            <button wire:click="startSimulation" class="mt-3 bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700">
                🎯 Simulate Interview for This Job
            </button>
        </div>
    @endif

    @if(count($jobs))
        <div class="space-y-3">
            @foreach($jobs as $job)
                <div wire:click="selectJob({{ json_encode($job) }})"
                     class="bg-white border rounded-lg p-4 cursor-pointer hover:border-indigo-300 hover:shadow-sm transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-medium text-gray-800">{{ $job['title'] ?? 'Untitled' }}</h4>
                            <p class="text-sm text-gray-500">
                                {{ $job['company_name'] ?? 'Unknown' }}
                                — {{ $job['candidate_required_location'] ?? 'Remote' }}
                                · {{ $job['job_type'] ?? 'Full-time' }}
                            </p>
                        </div>
                        <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($job['publication_date'] ?? now())->diffForHumans() }}</span>
                    </div>
                    @if(!empty($job['tags']))
                        <div class="flex flex-wrap gap-1 mt-2">
                            @foreach(array_slice($job['tags'], 0, 5) as $tag)
                                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">{{ $tag }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @elseif(strlen($searchTerm) >= 2 && !$loading)
        <p class="text-center text-gray-500 py-8">No jobs found for "{{ $searchTerm }}". Try a different search.</p>
    @endif
</div>