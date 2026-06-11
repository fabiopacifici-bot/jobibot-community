<?php

namespace App\Livewire;

use App\Models\JobAdvertisement;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class JobSearch extends Component
{
    public string $searchTerm = '';

    public array $jobs = [];

    public ?array $selectedJob = null;

    public bool $loading = false;

    public function updatedSearchTerm(): void
    {
        if (strlen($this->searchTerm) >= 2) {
            $this->searchJobs();
        } else {
            $this->jobs = [];
        }
    }

    public function searchJobs(): void
    {
        $this->loading = true;

        try {
            $response = Http::timeout(15)
                ->get(config('jobibot.remotive_api_url'), [
                    'search' => $this->searchTerm,
                    'limit' => 20,
                ]);

            if ($response->successful()) {
                $this->jobs = $response->json('jobs', []);
            }
        } catch (\Throwable $e) {
            session()->flash('error', 'Job search unavailable: '.$e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    public function selectJob(array $job): void
    {
        $this->selectedJob = $job;

        // Save to local DB for history
        JobAdvertisement::firstOrCreate(
            ['source_url' => $job['url'] ?? ''],
            [
                'title' => $job['title'] ?? 'Untitled',
                'type' => $job['job_type'] ?? 'Fulltime',
                'salary' => $job['salary'] ?? null,
                'work_from' => 'Remote',
                'description' => $job['description'] ?? '',
                'requirements' => implode("\n", $job['tags'] ?? []),
                'source' => 'remotive',
            ]
        );
    }

    public function startSimulation(): void
    {
        if (! $this->selectedJob) {
            return;
        }

        $this->dispatch('job-selected', jobDescription: $this->selectedJob['description'] ?? '')
            ->to(JobInterview::class);
    }

    public function render()
    {
        return view('livewire.job-search');
    }
}
