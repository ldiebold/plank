<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PlankCompletionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'duration_seconds' => fake()->numberBetween(30, 600),
            'date' => now()->toDateString(),
            'completed_at' => now(),
        ];
    }
}
