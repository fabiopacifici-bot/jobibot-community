<?php

namespace App\Livewire;

use App\JobiBot\Lai;
use App\JobiBot\Exceptions\LaiException;
use App\Models\Candidate;
use App\Models\Simulation;
use App\Models\SimulationMessage;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class JobInterview extends Component
{
    public string $fullname = '';
    public string $job = '';
    public string $prompt = '';
    public bool $simulationStarted = false;
    public bool $loading = false;
    public array $conversation = [];
    public ?int $simulationId = null;
    public ?int $cvMatchScore = null;
    public ?int $simulationScore = null;
    public string $considerations = '';

    public function mount(): void
    {
        if (Auth::check()) {
            $this->fullname = Auth::user()->name ?? '';
        }

        $name = $this->fullname ? ' ' . $this->fullname : '';

        $this->conversation[] = [
            'role'    => 'assistant',
            'content' => "👋 Hi{$name}! Paste a job description below and start your interview simulation. Or search for jobs in the Job Search tab.",
        ];
    }

    public function updatedConversation(): void
    {
        $this->dispatch('scroll-conversation');
    }

    /**
     * Start the interview simulation with the given job description.
     */
    public function startSimulation(): void
    {
        $this->validate([
            'fullname' => 'required|min:2|max:50',
            'job'      => 'required|min:10|max:10000',
        ]);

        // Check for CV match if candidate has uploaded a CV
        if (Auth::check()) {
            $candidate = Candidate::where('user_id', Auth::id())->first();
            if ($candidate && $candidate->cv) {
                try {
                    $matchResult = Lai::match($candidate->cv, $this->job);
                    $this->cvMatchScore = (int) $matchResult['job_match_percent'];
                } catch (LaiException) {
                    // Match scoring is optional — continue without it
                }
            }
        }

        $this->simulationStarted = true;
        $this->loading = true;

        // Add the initial user data to conversation
        $this->conversation[] = [
            'role'    => 'user',
            'content' => "Candidate: {$this->fullname} | Applying for: {$this->job}",
        ];

        // Create simulation record if authenticated
        if (Auth::check()) {
            $candidate = Candidate::firstOrCreate(
                ['user_id' => Auth::id()],
                ['cv' => '', 'bio' => '']
            );

            $simulation = Simulation::create([
                'candidate_id'        => $candidate->id,
                'status'              => 'in_progress',
                'cv_match_score'      => $this->cvMatchScore,
                'job_advertisement_id' => null,
            ]);

            $this->simulationId = $simulation->id;
        }

        $this->sendRequest();
    }

    /**
     * Submit the candidate's answer to the AI interviewer.
     */
    public function submitAnswer(): void
    {
        $this->validate([
            'prompt' => 'required|min:2|max:5000',
        ]);

        $this->conversation[] = [
            'role'    => 'user',
            'content' => $this->prompt,
        ];

        $prompt = $this->prompt;
        $this->prompt = '';

        // Persist message if authenticated
        if (Auth::check() && $this->simulationId) {
            SimulationMessage::create([
                'simulation_id' => $this->simulationId,
                'role'          => 'user',
                'content'       => $prompt,
            ]);
        }

        $this->loading = true;
        $this->sendRequest();
    }

    /**
     * End the simulation and score it.
     */
    public function endSimulation(): void
    {
        if (! $this->simulationStarted) {
            return;
        }

        $this->simulationStarted = false;

        if (Auth::check() && $this->simulationId) {
            try {
                $result = Lai::scoreSimulation(
                    ['description' => $this->job],
                    $this->conversation
                );

                $simulation = Simulation::find($this->simulationId);
                if ($simulation) {
                    $simulation->update([
                        'status'            => 'completed',
                        'simulation_score'  => $result['simulation_score'],
                        'considerations'    => $result['considerations'],
                    ]);
                }

                $this->simulationScore = (int) $result['simulation_score'];
                $this->considerations = (string) $result['considerations'];
            } catch (LaiException $e) {
                session()->flash('error', 'Scoring failed: ' . $e->getMessage());
            }
        }

        session()->flash('message', '✅ Simulation completed!');
    }

    /**
     * Start a new simulation (reset state).
     */
    public function newSimulation(): void
    {
        $this->reset([
            'job', 'prompt', 'simulationStarted', 'loading',
            'simulationId', 'cvMatchScore', 'simulationScore', 'considerations',
        ]);
        $this->conversation = [[
            'role'    => 'assistant',
            'content' => "👋 Hi{$this->fullname}! Paste a job description below to start a new simulation.",
        ]];
    }

    #[On('job-selected')]
    public function setJobFromSearch(string $jobDescription): void
    {
        $this->job = $jobDescription;
        $this->startSimulation();
    }

    /**
     * Send the conversation to the AI provider for the next interviewer response.
     */
    protected function sendRequest(): void
    {
        try {
            $response = Lai::submitAnswer([
                'messages' => [
                    ...Lai::getRecruiterSysMessages(),
                    ...$this->conversation,
                ],
            ]);

            $replyMessage = $response['reply_message'];

            $this->conversation[] = $replyMessage;

            // Persist assistant message if authenticated
            if (Auth::check() && $this->simulationId) {
                SimulationMessage::create([
                    'simulation_id' => $this->simulationId,
                    'role'          => $replyMessage['role'],
                    'content'       => $replyMessage['content'],
                ]);
            }

            // Auto-end after ~10 exchanges (5 questions + answers)
            $assistantMessages = collect($this->conversation)
                ->where('role', 'assistant')
                ->count();

            if ($assistantMessages >= 12) {
                $this->endSimulation();
            }

        } catch (LaiException $e) {
            $this->conversation[] = [
                'role'    => 'assistant',
                'content' => '⚠️ AI service error: ' . $e->getMessage() . '. Please check your provider settings.',
            ];
        } finally {
            $this->loading = false;
        }
    }

    public function render()
    {
        return view('livewire.job-interview');
    }
}