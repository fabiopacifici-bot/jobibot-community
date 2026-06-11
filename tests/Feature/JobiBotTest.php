<?php

use App\Models\Candidate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('dashboard loads', function () {
    $this->get('/')->assertStatus(200)->assertSee('JobiBot');
});

test('cv page loads', function () {
    $this->get('/cv')->assertStatus(200)->assertSee('Upload Your CV');
});

test('jobs page loads', function () {
    $this->get('/jobs')->assertStatus(200)->assertSee('Job Search');
});

test('interview page loads', function () {
    $this->get('/interview')->assertStatus(200)->assertSee('Interview Simulation');
});

test('settings page loads', function () {
    $this->get('/settings')->assertStatus(200)->assertSee('AI Provider Settings');
});

test('candidate can be created', function () {
    $user = User::factory()->create();
    expect(Candidate::count())->toBe(0);

    Candidate::create([
        'user_id' => $user->id,
        'cv' => 'Experienced PHP developer...',
        'bio' => 'Senior developer with 10 years experience',
    ]);

    expect(Candidate::count())->toBe(1);
    $this->assertDatabaseHas('candidates', [
        'user_id' => $user->id,
        'bio' => 'Senior developer with 10 years experience',
    ]);
});

test('install command runs successfully', function () {
    $this->artisan('jobibot:install')
        ->expectsOutput('🚀 Installing JobiBot Community Edition...')
        ->expectsOutput('✅ JobiBot Community Edition installed successfully.')
        ->assertExitCode(0);
});
