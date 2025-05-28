<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\Club;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB; // Für Transaktion (optional)

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hole benötigte Daten (stelle sicher, dass die vorherigen Seeder gelaufen sind!)
        $clubs = Club::all();
        $genres = Genre::all();
        $organizers = User::role('Organizer')->get();
        $djs = User::role('DJ')->get();

        // Prüfe, ob genug Daten vorhanden sind
        if ($clubs->isEmpty()) {
            $this->command->warn('Keine Clubs für den EventSeeder gefunden. Bitte führe den ClubSeeder aus.');
            return;
        }
        if ($genres->isEmpty()) {
            $this->command->warn('Keine Genres für den EventSeeder gefunden. Bitte führe den GenreSeeder aus.');
            return;
        }
        if ($organizers->isEmpty()) {
            $this->command->warn('Keine Veranstalter (Organizer) für den EventSeeder gefunden. Bitte führe den UserSeeder/RoleSeeder aus.');
            return;
        }
         if ($djs->isEmpty()) {
            $this->command->warn('Keine DJs für den EventSeeder gefunden. Bitte führe den UserSeeder/RoleSeeder aus.');
            // Optional: Erlaube Events ohne DJs zu erstellen
            // return;
        }

        // Lösche alte Events, um Konsistenz zu wahren (Vorsicht bei produktiven Systemen!)
        // Nur ausführen, wenn du sicher bist, dass du neu starten möchtest.
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;'); // Deaktiviert Fremdschlüsselprüfungen (MySQL)
        // DB::table('event_genre')->truncate();
        // DB::table('event_dj')->truncate();
        // Event::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;'); // Re-aktiviert Fremdschlüsselprüfungen (MySQL)
        // Für andere DBs ggf. anpassen (z.B. PostgreSQL: TRUNCATE ... RESTART IDENTITY CASCADE)


        // --- Beispiel Event 1 ---
        try {
            $event1 = Event::create([
                'name' => 'Techno Bunker Night',
                'description' => fake()->paragraphs(3, true), // Nutzt Faker für Beispieltext
                'start_time' => Carbon::now()->addDays(7)->setTime(23, 0, 0), // Nächste Woche, 23 Uhr
                'end_time' => Carbon::now()->addDays(8)->setTime(6, 0, 0), // Nächster Morgen, 6 Uhr
                'club_id' => $clubs->random()->id,
                'organizer_id' => $organizers->random()->id,
                'price' => 15.00,
                'currency' => 'EUR',
                'is_active' => true,
                'requires_approval' => false, // Direkt aktiv (Admin hat erstellt)
                'allows_presale' => true,
                'allows_guestlist' => true,
            ]);

            // Verknüpfe Genres (Techno, Minimal)
            $technoGenre = $genres->firstWhere('name', 'Techno');
            $minimalGenre = $genres->firstWhere('name', 'Minimal');
            $event1->genres()->sync(array_filter([$technoGenre?->id, $minimalGenre?->id]));

            // Verknüpfe DJs (mind. 1 DJ muss existieren)
            if ($djs->isNotEmpty()) {
                 $event1->djs()->sync($djs->random(min(2, $djs->count()))->pluck('id')); // Nimm 2 DJs, falls verfügbar
            }


        } catch (\Exception $e) {
            $this->command->error('Fehler beim Erstellen von Event 1: ' . $e->getMessage());
        }


        // --- Beispiel Event 2 ---
        try {
            $event2 = Event::create([
                'name' => 'House Grooves All Night Long',
                'description' => fake()->sentence(15),
                'start_time' => Carbon::now()->addDays(14)->setTime(22, 0, 0),
                'end_time' => Carbon::now()->addDays(15)->setTime(5, 0, 0),
                'club_id' => $clubs->random()->id, // Anderer Club?
                'organizer_id' => $organizers->random()->id,
                'price' => 10.00,
                'currency' => 'EUR',
                'is_active' => true,
                'requires_approval' => false,
                'allows_presale' => false, // Kein Vorverkauf
                'allows_guestlist' => true,
            ]);

            // Verknüpfe Genres (House, Deep House)
            $houseGenre = $genres->firstWhere('name', 'House');
            $deepHouseGenre = $genres->firstWhere('name', 'Deep House');
            $event2->genres()->sync(array_filter([$houseGenre?->id, $deepHouseGenre?->id]));

             // Verknüpfe DJs (mind. 1 DJ muss existieren)
             if ($djs->isNotEmpty()) {
                $event2->djs()->sync($djs->random(1)->pluck('id'));
             }

        } catch (\Exception $e) {
             $this->command->error('Fehler beim Erstellen von Event 2: ' . $e->getMessage());
        }

         // --- Beispiel Event 3 (Wartet auf Freigabe) ---
        try {
             $event3 = Event::create([
                'name' => 'Hip Hop Jam Session',
                'description' => 'Open Mic und freshe Beats die ganze Nacht. Anmeldung für Open Mic vor Ort.',
                'start_time' => Carbon::now()->addDays(20)->setTime(20, 0, 0), // Früher Start
                'end_time' => Carbon::now()->addDays(21)->setTime(3, 0, 0),
                'club_id' => $clubs->random()->id,
                'organizer_id' => $organizers->random()->id,
                'price' => 5.00, // Günstiger Eintritt
                'currency' => 'EUR',
                'is_active' => false, // Nicht aktiv
                'requires_approval' => true, // Muss freigegeben werden
                'allows_presale' => false,
                'allows_guestlist' => false, // Keine Gästeliste
            ]);
            // Verknüpfe Genre (Hip Hop / Rap)
             $hiphopGenre = $genres->firstWhere('name', 'Hip Hop / Rap');
            if ($hiphopGenre) {
                $event3->genres()->sync([$hiphopGenre->id]);
            }
             // Keine DJs explizit zugewiesen (vielleicht spontan oder Teil des Open Mic)

        } catch (\Exception $e) {
             $this->command->error('Fehler beim Erstellen von Event 3: ' . $e->getMessage());
        }

        // --- Beispiel Event 4 (Drum & Bass) ---
        try {
            $event4 = Event::create([
                'name' => 'D&B Mayhem',
                'description' => fake()->realText(200), // Längerer Beschreibungstext
                'start_time' => Carbon::now()->addDays(25)->setTime(23, 30, 0), // Start um 23:30
                'end_time' => Carbon::now()->addDays(26)->setTime(7, 0, 0),   // Langes Ende
                'club_id' => $clubs->random()->id,
                'organizer_id' => $organizers->random()->id,
                'price' => 12.50,
                'currency' => 'EUR',
                'is_active' => true,
                'requires_approval' => false,
                'allows_presale' => true,
                'allows_guestlist' => true,
            ]);

            // Verknüpfe Genres (Drum & Bass, Jungle)
            $dnbGenre = $genres->firstWhere('name', 'Drum & Bass');
            $jungleGenre = $genres->firstWhere('name', 'Jungle'); // Falls Jungle als Genre existiert
            $event4->genres()->sync(array_filter([$dnbGenre?->id, $jungleGenre?->id]));

            // Verknüpfe DJs (mind. 1 DJ muss existieren)
            if ($djs->isNotEmpty()) {
                 $event4->djs()->sync($djs->random(min(3, $djs->count()))->pluck('id')); // Nimm 3 DJs, falls verfügbar
            }

        } catch (\Exception $e) {
            $this->command->error('Fehler beim Erstellen von Event 4: ' . $e->getMessage());
        }

        // --- Beispiel Event 5 (Chillout Sundowner - Kostenlos) ---
        try {
            $event5 = Event::create([
                'name' => 'Sunset Chill Session',
                'description' => 'Relaxte Beats zum Sonnenuntergang. Freier Eintritt!',
                'start_time' => Carbon::now()->addDays(10)->next(Carbon::SUNDAY)->setTime(18, 0, 0), // Nächster Sonntag, 18 Uhr
                'end_time' => Carbon::now()->addDays(10)->next(Carbon::SUNDAY)->setTime(23, 0, 0), // Ende um 23 Uhr
                'club_id' => $clubs->random()->id, // Vielleicht ein Club mit Außenbereich?
                'organizer_id' => $organizers->random()->id,
                'price' => 0.00, // Kostenlos
                'currency' => 'EUR',
                'is_active' => true,
                'requires_approval' => false,
                'allows_presale' => false, // Kein VVK bei kostenlosem Event
                'allows_guestlist' => false,// Normalerweise keine Gästeliste bei freiem Eintritt
            ]);

            // Verknüpfe Genres (Ambient, Downtempo)
            $ambientGenre = $genres->firstWhere('name', 'Ambient');
            $downtempoGenre = $genres->firstWhere('name', 'Downtempo');
            $event5->genres()->sync(array_filter([$ambientGenre?->id, $downtempoGenre?->id]));

            // Verknüpfe DJs (mind. 1 DJ muss existieren)
             if ($djs->isNotEmpty()) {
                $event5->djs()->sync($djs->random(1)->pluck('id'));
             }

        } catch (\Exception $e) {
            $this->command->error('Fehler beim Erstellen von Event 5: ' . $e->getMessage());
        }

        // --- Beispiel Event 6 (Festival Warmup - Teurer, braucht Freigabe) ---
        try {
            $event6 = Event::create([
                'name' => 'Rave Festival Official Warm-Up',
                'description' => 'Die offizielle Warm-Up Party für das kommende Sommerfestival! Streng limitierte Tickets.',
                'start_time' => Carbon::now()->addMonths(2)->setTime(22, 0, 0), // Weit in der Zukunft
                'end_time' => Carbon::now()->addMonths(2)->addDays(1)->setTime(8, 0, 0), // Lange Dauer
                'club_id' => $clubs->random()->id,
                'organizer_id' => $organizers->random()->id,
                'price' => 25.00, // Höherer Preis
                'currency' => 'EUR',
                'is_active' => true, // Muss erst freigegeben werden
                'requires_approval' => false,
                'allows_presale' => true,
                'allows_guestlist' => true, // VIP Gästeliste o.ä.
            ]);

            // Verknüpfe Genres (Techno, House, Trance - falls vorhanden)
            $technoGenre = $genres->firstWhere('name', 'Techno');
            $houseGenre = $genres->firstWhere('name', 'House');
            $tranceGenre = $genres->firstWhere('name', 'Trance');
            $event6->genres()->sync(array_filter([$technoGenre?->id, $houseGenre?->id, $tranceGenre?->id]));

            // Verknüpfe DJs (mind. 1 DJ muss existieren)
            if ($djs->isNotEmpty()) {
                 $event6->djs()->sync($djs->random(min(4, $djs->count()))->pluck('id')); // Viele DJs
            }

        } catch (\Exception $e) {
            $this->command->error('Fehler beim Erstellen von Event 6: ' . $e->getMessage());
        }

        // --- Beispiel Event 7 (80s/90s Pop Party - Aktiv, kein VVK/GL) ---
        try {
            $event7 = Event::create([
                'name' => 'Back to the 80s & 90s',
                'description' => 'Die besten Hits aus zwei Jahrzehnten!',
                'start_time' => Carbon::now()->addDays(18)->setTime(21, 0, 0),
                'end_time' => Carbon::now()->addDays(19)->setTime(4, 0, 0),
                'club_id' => $clubs->random()->id,
                'organizer_id' => $organizers->random()->id,
                'price' => 8.00,
                'currency' => 'EUR',
                'is_active' => true,
                'requires_approval' => false,
                'allows_presale' => false, // Nur Abendkasse
                'allows_guestlist' => false, // Keine Gästeliste
            ]);

            // Verknüpfe Genre (Pop / Charts oder spezifischeres Genre falls vorhanden)
            $popGenre = $genres->firstWhere('name', 'Pop / Charts');
            $discoGenre = $genres->firstWhere('name', 'Disco'); // Beispiel, falls vorhanden
             if ($popGenre || $discoGenre) {
                 $event7->genres()->sync(array_filter([$popGenre?->id, $discoGenre?->id]));
             }


             // Verknüpfe DJs (mind. 1 DJ muss existieren)
             if ($djs->isNotEmpty()) {
                 $event7->djs()->sync($djs->random(1)->pluck('id'));
             }


        } catch (\Exception $e) {
             $this->command->error('Fehler beim Erstellen von Event 7: ' . $e->getMessage());
        }


        $this->command->info('Example events seeded successfully.');
    }
}