<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rating;
use App\Models\User;
use App\Models\Club;
use Illuminate\Support\Carbon;

class RatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hole Benutzer (ohne Admins/Partner, nur normale User?) und Clubs
        $users = User::role('User')->get(); // Hole nur User mit der Rolle 'User'
        $clubs = Club::all();
        $admin = User::role('Administrator')->first(); // Für 'approved_by'

        // Stelle sicher, dass Daten vorhanden sind
        if ($users->isEmpty() || $clubs->isEmpty()) {
            $this->command->warn('Keine Benutzer (mit Rolle User) oder Clubs für RatingSeeder gefunden. Bitte vorherige Seeder ausführen.');
            return;
        }

        // Lösche alte Bewertungen (optional, Vorsicht!)
        // Rating::truncate();

        $numberOfRatings = 200; // Wie viele Bewertungen sollen insgesamt erstellt werden?
        $faker = \Faker\Factory::create('de_DE'); // Deutschen Faker verwenden

        for ($i = 0; $i < $numberOfRatings; $i++) {
            $user = $users->random(); // Wähle zufälligen User
            $club = $clubs->random(); // Wähle zufälligen Club

            // Überspringe, wenn dieser User diesen Club schon bewertet hat (falls unique constraint nicht gesetzt)
            // if (Rating::where('user_id', $user->id)->where('club_id', $club->id)->exists()) {
            //     continue;
            // }

            // Zufällige Bewertung und Kommentar
            $ratingValue = rand(1, 5);
            $comment = (rand(0, 10) > 3) ? $faker->paragraph(rand(1, 4)) : null; // Ca. 70% Chance auf Kommentar

            // Zufälliger Genehmigungsstatus (ca. 80% genehmigt)
            $isApproved = (rand(0, 10) > 2);
            $approvedAt = $isApproved ? Carbon::now()->subDays(rand(0, 30)) : null; // Zufälliges Genehmigungsdatum in der Vergangenheit
            $approvedBy = $isApproved ? $admin?->id : null; // Admin als Genehmiger

            // Zufälliges Erstellungsdatum
            $createdAt = Carbon::now()->subDays(rand(1, 90)); // Bewertung innerhalb der letzten 90 Tage

            Rating::create([
                'user_id' => $user->id,
                'club_id' => $club->id,
                'rating' => $ratingValue,
                'comment' => $comment,
                'is_approved' => $isApproved,
                'approved_by' => $approvedBy,
                'approved_at' => $approvedAt,
                'created_at' => $createdAt,
                'updated_at' => $createdAt, // Setze updated_at gleich created_at für Seeder
            ]);
        }

        $this->command->info($numberOfRatings . ' example ratings seeded successfully.');
    }
}