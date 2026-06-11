<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobiBotFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_loads(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('JobiBot');
    }

    public function test_cv_page_loads(): void
    {
        $response = $this->get('/cv');
        $response->assertStatus(200);
        $response->assertSee('Upload Your CV');
    }

    public function test_jobs_page_loads(): void
    {
        $response = $this->get('/jobs');
        $response->assertStatus(200);
        $response->assertSee('Job Search');
    }

    public function test_interview_page_loads(): void
    {
        $response = $this->get('/interview');
        $response->assertStatus(200);
        $response->assertSee('Interview Simulation');
    }

    public function test_settings_page_loads(): void
    {
        $response = $this->get('/settings');
        $response->assertStatus(200);
        $response->assertSee('AI Provider Settings');
    }

    public function test_candidate_can_be_created(): void
    {
        $user = User::factory()->create();
        $this->assertDatabaseCount('candidates', 0);

        Candidate::create([
            'user_id' => $user->id,
            'cv'      => 'Experienced PHP developer...',
            'bio'     => 'Senior developer with 10 years experience',
        ]);

        $this->assertDatabaseCount('candidates', 1);
        $this->assertDatabaseHas('candidates', [
            'user_id' => $user->id,
            'bio'     => 'Senior developer with 10 years experience',
        ]);
    }

    public function test_install_command_runs(): void
    {
        $this->artisan('jobibot:install')
             ->expectsOutput('🚀 Installing JobiBot Community Edition...')
             ->expectsOutput('✅ JobiBot Community Edition installed successfully.')
             ->assertExitCode(0);
    }
}