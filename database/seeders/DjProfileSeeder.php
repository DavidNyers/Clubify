<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DjProfile;
use App\Models\User;

class DjProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hole alle Benutzer mit der DJ-Rolle
        $djUsers = User::role('DJ')->get();

        if ($djUsers->isEmpty()) {
            $this->command->warn('Keine Benutzer mit der Rolle "DJ" gefunden. Bitte zuerst UserSeeder ausführen.');
            return;
        }

        // Beispiel-Social Links (kann für alle gleich sein oder variieren)
        $defaultSocialLinks = [
            'instagram' => 'https://instagram.com/example_dj',
            'soundcloud' => 'https://soundcloud.com/example_dj',
        ];
        $defaultMusicLinks = [
            'latest_mix' => 'https://soundcloud.com/example_dj/latest-mix',
        ];

        foreach ($djUsers as $djUser) {
            // Erstelle Profil nur, wenn noch keines existiert (firstOrCreate)
            DjProfile::firstOrCreate(
                ['user_id' => $djUser->id], // Suche nach diesem User
                [
                    // Falls Stage Name leer ist, wird der User-Name im Accessor verwendet
                    'stage_name' => $djUser->name === 'DJ One' ? 'DJ UNO' : null, // Beispiel für abweichenden Stage Name
                    'bio' => fake()->paragraph(4),
                    'social_links' => $defaultSocialLinks, // Beispiel
                    'music_links' => $defaultMusicLinks,  // Beispiel
                    'is_visible' => true,
                    // Verifiziert, wenn der User z.B. 'dj.one' ist (Beispiel-Logik)
                    'is_verified' => str_contains($djUser->email, 'dj.one'),
                    'booking_email' => fake()->safeEmail(),
                ]
            );
        }

        $this->command->info('DJ profiles seeded/updated for users with DJ role.');
    }
}