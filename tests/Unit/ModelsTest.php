<?php

use App\Models\Candidate;
use App\Models\JobAdvertisement;
use App\Models\Simulation;
use App\Models\SimulationMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('candidate belongs to user', function () {
    $user = User::factory()->create();
    $candidate = Candidate::create([
        'user_id' => $user->id,
        'cv'      => 'Test CV content',
        'bio'     => 'Software developer',
    ]);

    expect($candidate->user->id)->toBe($user->id);
});

test('candidate has simulations', function () {
    $user = User::factory()->create();
    $candidate = Candidate::create(['user_id' => $user->id]);
    $simulation = Simulation::create([
        'candidate_id' => $candidate->id,
        'status'       => 'pending',
    ]);

    expect($candidate->simulations)->toHaveCount(1);
    expect($candidate->simulations->first()->status)->toBe('pending');
});

test('simulation has uuid', function () {
    $user = User::factory()->create();
    $candidate = Candidate::create(['user_id' => $user->id]);
    $simulation = Simulation::create([
        'candidate_id' => $candidate->id,
        'status'       => 'in_progress',
    ]);

    expect($simulation->uuid)->not->toBeNull();
    expect($simulation->uuid)->toBeString();
    expect(strlen($simulation->uuid))->toBe(36); // UUID v4
});

test('simulation has messages', function () {
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

    expect($simulation->messages)->toHaveCount(2);
});

test('job advertisement has simulations', function () {
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
        'job_advertisement_id' => $job->id,
        'status'              => 'completed',
        'simulation_score'    => 85,
    ]);

    expect($job->simulations)->toHaveCount(1);
    expect($job->simulations->first()->simulation_score)->toBe(85);
});

test('simulation scoring', function () {
    $user = User::factory()->create();
    $candidate = Candidate::create(['user_id' => $user->id]);
    $simulation = Simulation::create([
        'candidate_id'     => $candidate->id,
        'status'           => 'completed',
        'simulation_score' => 92,
        'considerations'   => 'Excellent technical answers.',
    ]);

    expect($simulation->status)->toBe('completed');
    expect($simulation->simulation_score)->toBe(92);
    expect($simulation->considerations)->toContain('Excellent');
});

test('cv match score is nullable', function () {
    $user = User::factory()->create();
    $candidate = Candidate::create(['user_id' => $user->id]);
    $simulation = Simulation::create([
        'candidate_id' => $candidate->id,
        'status'       => 'pending',
    ]);

    expect($simulation->cv_match_score)->toBeNull();
});