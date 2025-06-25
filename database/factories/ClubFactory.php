<?php

namespace Database\Factories;

use App\Models\Club;
use App\Models\ClubImage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClubFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'description' => fake()->paragraphs(3, true),
            'street_address' => fake()->streetAddress(),
            'city' => fake()->randomElement(['Berlin', 'Hamburg', 'München', 'Köln', 'Frankfurt am Main']),
            'zip_code' => fake()->postcode(),
            'country' => 'DE',
            'latitude' => fake()->latitude(47.3, 55.0), // Germany's latitude range
            'longitude' => fake()->longitude(5.9, 15.0), // Germany's longitude range
            'website' => 'https://' . fake()->domainName(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'price_level' => fake()->randomElement(['$', '$$', '$$$']),
            'is_active' => true,
            'is_verified' => true,
        ];
    }

    // Dieser Hook wird ausgeführt, NACHDEM ein Club erstellt wurde
    public function configure(): static
    {
        return $this->afterCreating(function (Club $club) {
            // Erstelle 3 bis 5 Galeriebilder für jeden Club
            for ($i = 0; $i < rand(3, 5); $i++) {
                ClubImage::create([
                    'club_id' => $club->id,
                    'path' => 'https://picsum.photos/seed/' . Str::random(8) . '/1200/800',
                    'original_name' => 'demo_image.jpg',
                ]);
            }
        });
    }
}