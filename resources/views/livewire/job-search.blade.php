<div>
    <div class="mb-6">
        <h2 class="text-xl font-bold mb-1 text-gray-900 dark:text-gray-100">🔍 Job Search</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">Search remote jobs via Remotive API</p>
    </div>

    <div class="mb-4">
        <input type="text" wire:model.live.debounce.500ms="searchTerm"
               placeholder="e.g. Laravel Developer, React, Designer..."
               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>

    <div wire:loading wire:target="searchJobs" class="text-center py-4">
        <div class="animate-spin inline-block w-6 h-6 border-2 border-indigo-600 border-t-transparent rounded-full"></div>
    </div>

    @if($selectedJob)
        <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-xl p-5 mb-4">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="font-bold text-lg text-gray-900 dark:text-gray-100">{{ $selectedJob['title'] ?? 'Untitled' }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $selectedJob['company_name'] ?? '' }} — {{ $selectedJob['candidate_required_location'] ?? 'Remote' }}</p>
                    @if(!empty($selectedJob['salary']))
                        <p class="text-sm text-green-700 dark:text-green-400 mt-1">💰 {{ $selectedJob['salary'] }}</p>
                    @endif
                </div>
                <button wire:click="$set('selectedJob', null)" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">✕</button>
            </div>
            <div class="mt-3 text-sm text-gray-700 dark:text-gray-300 max-h-48 overflow-y-auto">
                {!! nl2br(e(Str::limit($selectedJob['description'] ?? '', 800))) !!}
            </div>
            <button wire:click="startSimulation" class="mt-3 bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 transition-colors">
                🎯 Simulate Interview for This Job
            </button>
        </div>
    @endif

    @if(count($jobs))
        <div class="space-y-3">
            @foreach($jobs as $job)
                <div wire:click="selectJob({{ json_encode($job) }})"
                     class="bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-lg p-4 cursor-pointer hover:border-indigo-300 dark:hover:border-indigo-700 hover:shadow-sm transition-colors">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-medium text-gray-800 dark:text-gray-200">{{ $job['title'] ?? 'Untitled' }}</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $job['company_name'] ?? 'Unknown' }}
                                — {{ $job['candidate_required_location'] ?? 'Remote' }}
                                · {{ $job['job_type'] ?? 'Full-time' }}
                            </p>
                        </div>
                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ \Carbon\Carbon::parse($job['publication_date'] ?? now())->diffForHumans() }}</span>
                    </div>
                    @if(!empty($job['tags']))
                        <div class="flex flex-wrap gap-1 mt-2">
                            @foreach(array_slice($job['tags'], 0, 5) as $tag)
                                <span class="text-xs bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 px-2 py-0.5 rounded-full">{{ $tag }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @elseif(strlen($searchTerm) >= 2 && !$loading)
        <p class="text-center text-gray-500 dark:text-gray-400 py-8">No jobs found for "{{ $searchTerm }}". Try a different search.</p>
    @endif
</div>