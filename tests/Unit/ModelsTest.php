<?php

namespace Tests\Unit;

use App\Models\Candidate;
use App\Models\JobAdvertisement;
use App\Models\Simulation;
use App\Models\SimulationMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_candidate_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $candidate = Candidate::create([
            'user_id' => $user->id,
            'cv'      => 'Test CV content',
            'bio'     => 'Software developer',
        ]);

        $this->assertEquals($user->id, $candidate->user->id);
    }

    public function test_candidate_has_simulations(): void
    {
        $user = User::factory()->create();
        $candidate = Candidate::create(['user_id' => $user->id]);
        $simulation = Simulation::create([
            'candidate_id' => $candidate->id,
            'status'       => 'pending',
        ]);

        $this->assertCount(1, $candidate->simulations);
        $this->assertEquals('pending', $candidate->simulations->first()->status);
    }

    public function test_simulation_has_uuid(): void
    {
        $user = User::factory()->create();
        $candidate = Candidate::create(['user_id' => $user->id]);
        $simulation = Simulation::create([
            'candidate_id' => $candidate->id,
            'status'       => 'in_progress',
        ]);

        $this->assertNotNull($simulation->uuid);
        $this->assertIsString($simulation->uuid);
        $this->assertEquals(36, strlen($simulation->uuid)); // UUID v4
    }

    public function test_simulation_has_messages(): void
    {
        $user = User::factory()->create();
        $candidate = Candidate::create(['user_id' => $user->id]);
        $simulation = Simulation::create([
            'candidate_id' => $candidate->id,
            'status'       => 'in_progress',
        ]);

        SimulationMessage::create([
            'simulation_id' => $simulation->id,
            'role'          => 'user',
            'content'       => 'Hello, I am ready for the interview.',
        ]);

        SimulationMessage::create([
            'simulation_id' => $simulation->id,
            'role'          => 'assistant',
            'content'       => 'Great! Let us begin.',
        ]);

        $this->assertCount(2, $simulation->messages);
    }

    public function test_job_advertisement_has_simulations(): void
    {
        $user = User::factory()->create();
        $candidate = Candidate::create(['user_id' => $user->id]);
        $job = JobAdvertisement::create([
            'title'        => 'Laravel Developer',
            'type'         => 'Fulltime',
            'work_from'    => 'Remote',
            'description'  => 'Build APIs with Laravel',
            'requirements' => 'PHP, Laravel, MySQL',
            'source'       => 'manual',
        ]);

        Simulation::create([
            'candidate_id'        => $candidate->id,
            'job_advertisement_id'=> $job->id,
            'status'              => 'completed',
            'simulation_score'    => 85,
        ]);

        $this->assertCount(1, $job->simulations);
        $this->assertEquals(85, $job->simulations->first()->simulation_score);
    }

    public function test_simulation_scoring(): void
    {
        $user = User::factory()->create();
        $candidate = Candidate::create(['user_id' => $user->id]);
        $simulation = Simulation::create([
            'candidate_id'     => $candidate->id,
            'status'           => 'completed',
            'simulation_score' => 92,
            'considerations'   => 'Excellent technical answers.',
        ]);

        $this->assertEquals('completed', $simulation->status);
        $this->assertEquals(92, $simulation->simulation_score);
        $this->assertStringContainsString('Excellent', $simulation->considerations);
    }

    public function test_cv_match_score_is_nullable(): void
    {
        $user = User::factory()->create();
        $candidate = Candidate::create(['user_id' => $user->id]);
        $simulation = Simulation::create([
            'candidate_id' => $candidate->id,
            'status'       => 'pending',
        ]);

        $this->assertNull($simulation->cv_match_score);
    }
}