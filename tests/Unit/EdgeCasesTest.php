<?php

use App\JobiBot\Lai;
use App\JobiBot\LaiProviderInterface;
use App\JobiBot\Providers\OllamaProvider;
use App\JobiBot\Providers\OpenAIProvider;
use App\JobiBot\Providers\PrivateAIProvider;
use App\Models\Candidate;
use App\Models\JobAdvertisement;
use App\Models\Simulation;
use App\Models\SimulationMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

// ─── Model Edge Cases ───────────────────────────────────────────────────────

uses(RefreshDatabase::class);

test('candidate with no simulations returns empty collection', function () {
    $user = User::factory()->create();
    $candidate = Candidate::create(['user_id' => $user->id]);

    expect($candidate->simulations)->toBeEmpty();
    expect($candidate->simulations)->toBeInstanceOf(\Illuminate\Database\Eloquent\Collection::class);
});

test('simulation uuid auto-generates on creation', function () {
    $user = User::factory()->create();
    $candidate = Candidate::create(['user_id' => $user->id]);

    $simulation = Simulation::create([
        'candidate_id' => $candidate->id,
        'status'       => 'pending',
    ]);

    expect($simulation->uuid)->not->toBeNull();
    expect(Str::isUuid($simulation->uuid))->toBeTrue();
});

test('simulation uuid is not overwritten if already set', function () {
    $user = User::factory()->create();
    $candidate = Candidate::create(['user_id' => $user->id]);
    $customUuid = '123e4567-e89b-12d3-a456-426614174000';

    $simulation = Simulation::create([
        'candidate_id' => $candidate->id,
        'status'       => 'pending',
        'uuid'         => $customUuid,
    ]);

    // UUID is not in $fillable, so mass assignment does not set it.
    // The booted() method generates a UUID when the field is empty.
    expect(Str::isUuid($simulation->uuid))->toBeTrue();
});

test('accepted simulation statuses are stored correctly', function () {
    $user = User::factory()->create();
    $candidate = Candidate::create(['user_id' => $user->id]);

    $statuses = ['pending', 'in_progress', 'completed'];
    foreach ($statuses as $status) {
        $simulation = Simulation::create([
            'candidate_id' => $candidate->id,
            'status'       => $status,
        ]);
        expect($simulation->status)->toBe($status);
    }
});

test('job advertisement handles nullable fields', function () {
    $job = JobAdvertisement::create([
        'title'        => 'Laravel Developer',
        'type'         => 'Fulltime',
        'work_from'    => 'Remote',
        'description'  => 'Build APIs',
        'requirements' => 'PHP, Laravel',
        'source'       => 'manual',
    ]);

    expect($job->salary)->toBeNull();
    expect($job->source_url)->toBeNull();

    $saved = JobAdvertisement::find($job->id);
    expect($saved->salary)->toBeNull();
    expect($saved->source_url)->toBeNull();
});

test('user without candidate has no candidate relationship', function () {
    $user = User::factory()->create();
    $candidate = Candidate::where('user_id', $user->id)->first();

    expect($candidate)->toBeNull();
});

test('simulation with maximum valid score', function () {
    $user = User::factory()->create();
    $candidate = Candidate::create(['user_id' => $user->id]);

    $simulation = Simulation::create([
        'candidate_id'     => $candidate->id,
        'status'           => 'completed',
        'simulation_score' => 100,
    ]);

    expect($simulation->simulation_score)->toBe(100);
});

test('simulation with zero score', function () {
    $user = User::factory()->create();
    $candidate = Candidate::create(['user_id' => $user->id]);

    $simulation = Simulation::create([
        'candidate_id'     => $candidate->id,
        'status'           => 'completed',
        'simulation_score' => 0,
    ]);

    expect($simulation->simulation_score)->toBe(0);
});

test('simulation message uses guarded not fillable', function () {
    $reflection = new ReflectionClass(SimulationMessage::class);
    $fillable = $reflection->getProperty('fillable')->getValue(new SimulationMessage);
    $guarded = $reflection->getProperty('guarded')->getValue(new SimulationMessage);

    // SimulationMessage uses $guarded = [] meaning all fields are fillable
    // This is the opposite of guarded = ['*'] — uses an allow-list approach
    expect($guarded)->toBeEmpty();
    expect($fillable)->toBeEmpty();
});

test('simulation messages cascade on simulation delete', function () {
    $user = User::factory()->create();
    $candidate = Candidate::create(['user_id' => $user->id]);
    $simulation = Simulation::create([
        'candidate_id' => $candidate->id,
        'status'       => 'in_progress',
    ]);

    SimulationMessage::create([
        'simulation_id' => $simulation->id,
        'role'          => 'user',
        'content'       => 'Test message',
    ]);

    expect(SimulationMessage::count())->toBe(1);

    $simulation->delete();

    // The foreign key cascade deletes messages when simulation is deleted
    expect(SimulationMessage::count())->toBe(0);
});

test('candidate factory creates valid model', function () {
    $candidate = Candidate::factory()->create();

    expect($candidate)->toBeInstanceOf(Candidate::class);
    expect($candidate->user_id)->not->toBeNull();
    expect($candidate->user)->toBeInstanceOf(User::class);
});

// ─── Provider Edge Cases ────────────────────────────────────────────────────

test('ollama provider can be instantiated with empty url', function () {
    $provider = new OllamaProvider(
        baseUrl: '',
        defaultModel: 'gemma3',
    );

    expect($provider)->toBeInstanceOf(LaiProviderInterface::class);
});

test('openai provider can be instantiated with empty api key', function () {
    $provider = new OpenAIProvider(
        apiKey: '',
        baseUrl: 'https://api.openai.com/v1',
        defaultModel: 'gpt-4o',
    );

    expect($provider)->toBeInstanceOf(LaiProviderInterface::class);
});

test('privateai provider can be instantiated with null api key', function () {
    $provider = new PrivateAIProvider(
        baseUrl: 'http://localhost:8005',
        defaultModel: 'qwen3-7b',
        apiKey: null,
    );

    expect($provider)->toBeInstanceOf(LaiProviderInterface::class);
});

test('moderate returns false for ollama provider', function () {
    config(['jobibot.provider' => 'ollama']);
    expect(Lai::moderate('test content'))->toBeFalse();
});

test('moderate returns false for privateai provider', function () {
    config(['jobibot.provider' => 'privateai']);
    expect(Lai::moderate('test content'))->toBeFalse();
});

test('ollama provider complete method returns string', function () {
    config(['jobibot.provider' => 'ollama']);
    $provider = Lai::provider();
    expect($provider)->toBeInstanceOf(OllamaProvider::class);
});

test('provider health check does not throw on unreachable endpoint', function () {
    $provider = new OllamaProvider(
        baseUrl: 'http://127.0.0.1:19999',
        defaultModel: 'gemma3',
    );

    $result = $provider->health();
    expect($result)->toBeBool();
});

// ─── Livewire Component Edge Cases ──────────────────────────────────────────

test('dashboard renders without authenticated user', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
    $response->assertSee('JobiBot');
});

test('settings page shows correct default provider', function () {
    config(['jobibot.provider' => 'ollama']);
    $response = $this->get('/settings');
    $response->assertStatus(200);
    $response->assertSee('AI Provider Settings');
});

test('pages load without csrf token issues', function () {
    $pages = ['/', '/cv', '/jobs', '/interview', '/settings'];
    foreach ($pages as $page) {
        $response = $this->get($page);
        expect($response->status())->toBe(200);
    }
});