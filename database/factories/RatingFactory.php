<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RatingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'rating' => fake()->numberBetween(3, 5),
            'comment' => fake()->boolean(70) ? fake()->paragraph() : null,
            'is_approved' => true,
            'approved_at' => now(),
        ];
    }
}