<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DjProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'stage_name' => 'DJ ' . fake()->firstName(),
            'bio' => fake()->paragraph(),
            'profile_image_path' => 'https://i.pravatar.cc/300?u=' . fake()->uuid(),
            'banner_image_path' => 'https://picsum.photos/seed/' . Str::random(8) . '/1500/500',
            'is_visible' => true,
            'is_verified' => true,
            'booking_email' => fake()->safeEmail(),
        ];
    }
}