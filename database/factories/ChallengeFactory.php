<?php

namespace Database\Factories;

use App\Models\Challenge;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Challenge>
 */
class ChallengeFactory extends Factory
{
    protected $model = Challenge::class;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                '30 Day Plank Challenge',
                'Summer Core Challenge',
                'Power Plank Progression',
                'Daily Core Builder Challenge',
            ]),
            'description' => fake()->sentence(),
            'starting_time_seconds' => fake()->randomElement([60, 120, 300]),
            'daily_increment_seconds' => fake()->randomElement([5, 10, 15, 30]),
            'goal_time_seconds' => fake()->randomElement([300, 600, 900, 1800]),
            'start_date' => fake()->dateTimeBetween('-30 days', 'now'),
            'is_active' => true,
        ];
    }
}
