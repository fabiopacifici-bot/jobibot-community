<?php

namespace Database\Factories;

use App\Models\Candidate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Candidate>
 */
class CandidateFactory extends Factory
{
    protected $model = Candidate::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'cv' => fake()->paragraphs(5, true),
            'cv_path' => null,
            'bio' => fake()->sentence(10),
        ];
    }
}
