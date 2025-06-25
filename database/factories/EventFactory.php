<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Super Party Night: ' . fake()->words(3, true),
            'description' => fake()->paragraphs(4, true),
            'start_time' => fake()->dateTimeBetween('+1 day', '+2 months'),
            'end_time' => null, // Kann später gesetzt werden
            'price' => fake()->randomElement([10, 15, 20, 25, 0]),
            'is_active' => true,
            'requires_approval' => false,
            'cover_image_path' => 'https://picsum.photos/seed/' . Str::random(8) . '/1200/630',
        ];
    }
}