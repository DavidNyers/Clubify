<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Club;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Support\Facades\DB; // Für eine saubere Zuordnung wichtig

class ClubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lösche evtl. alte Einträge, um Dopplungen zu vermeiden bei erneutem Seeden
        // DB::table('club_genre')->truncate(); // Vorsicht bei bestehenden Daten
        // Club::truncate(); // Vorsicht bei bestehenden Daten

        // Hole verfügbare Genres
        $genres = Genre::all();
        if ($genres->isEmpty()) {
            $this->command->warn('Keine Genres gefunden. Bitte zuerst GenreSeeder ausführen.');
            // Optional hier Genres direkt erstellen oder abbrechen
            // Genre::create(['name' => 'Techno']); // etc.
            // $genres = Genre::all();
            return; // Abbrechen, wenn keine Genres da sind
        }

        // Hole einen Beispiel-ClubOwner (falls vorhanden)
        $clubOwner = User::role('ClubOwner')->first(); // Finde den ersten User mit der Rolle

        // Beispiel-Clubs erstellen
        $clubsData = [
            [
                'name' => 'Berghain',
                'description' => 'Bekannter Technoclub in Berlin.',
                'street_address' => 'Am Wriezener Bahnhof',
                'city' => 'Berlin',
                'zip_code' => '10243',
                'country' => 'DE',
                'latitude' => 52.5119,
                'longitude' => 13.4433,
                'website' => 'https://www.berghain.berlin/',
                'opening_hours' => ['Fri' => '23:59-08:00', 'Sat' => '23:59-12:00', 'Sun' => '23:59-08:00'],
                'price_level' => '$$',
                'is_active' => true,
                'is_verified' => true,
                'accessibility_features' => ['wheelchair_accessible' => false, 'details' => 'Viele Treppen.'],
                'owner_id' => $clubOwner?->id, // Weise Owner zu, falls gefunden
                'genres' => ['Techno', 'Minimal'] // Namen der Genres
            ],
            [
                'name' => 'Watergate',
                'description' => 'Club an der Spree mit Blick aufs Wasser.',
                'street_address' => 'Falckensteinstraße 49',
                'city' => 'Berlin',
                'zip_code' => '10997',
                'country' => 'DE',
                'latitude' => 52.5023,
                'longitude' => 13.4444,
                'website' => 'https://water-gate.de/',
                'opening_hours' => ['Wed' => '23:00-06:00', 'Fri' => '23:59-08:00', 'Sat' => '23:59-10:00'],
                'price_level' => '$$$',
                'is_active' => true,
                'is_verified' => true,
                'accessibility_features' => ['wheelchair_accessible' => true, 'accessible_restrooms' => true],
                'owner_id' => null, // Kein spezifischer Owner
                'genres' => ['House', 'Tech House', 'Deep House']
            ],
            [
                'name' => 'Bootshaus',
                'description' => 'Großer Club in Köln, bekannt für EDM und Techno.',
                'street_address' => 'Auenweg 173',
                'city' => 'Köln',
                'zip_code' => '51063',
                'country' => 'DE',
                'latitude' => 50.9491,
                'longitude' => 7.0000, // Beispielkoordinaten
                'website' => 'https://www.bootshaus.tv/',
                'opening_hours' => ['Fri' => '23:00-06:00', 'Sat' => '23:00-06:00'],
                'price_level' => '$$',
                'is_active' => true,
                'is_verified' => false, // Beispiel nicht verifiziert
                'accessibility_features' => null,
                'owner_id' => $clubOwner?->id,
                'genres' => ['Techno', 'EDM', 'Hardstyle'] // EDM gibt es vielleicht noch nicht als Genre
            ],
            [
                'name' => 'Grelle Forelle',
                'description' => 'Bekannter Club für elektronische Musik am Donaukanal.',
                'street_address' => 'Spittelauer Lände 12',
                'city' => 'Wien',
                'zip_code' => '1090',
                'country' => 'AT', // Österreich
                'latitude' => 48.2315, // Ungefähre Koordinaten
                'longitude' => 16.3655, // Ungefähre Koordinaten
                'website' => 'https://www.grelleforelle.com/',
                'opening_hours' => ['Fri' => '23:00-06:00', 'Sat' => '23:00-06:00'],
                'price_level' => '$$$',
                'is_active' => true,
                'is_verified' => true,
                'accessibility_features' => null, // Keine spezifischen Infos bekannt/angegeben
                'owner_id' => null,
                'genres' => ['Techno', 'House', 'Electronic']
            ],
            [
                'name' => 'Flex',
                'description' => 'Legendärer Club am Donaukanal mit Fokus auf Drum & Bass, Techno und Live-Musik.',
                'street_address' => 'Augartenbrücke 1', // Am Donaukanal
                'city' => 'Wien',
                'zip_code' => '1010', // Nahe Schottenring
                'country' => 'AT',
                'latitude' => 48.2181, // Ungefähre Koordinaten
                'longitude' => 16.3715, // Ungefähre Koordinaten
                'website' => 'https://flex.at/',
                'opening_hours' => ['Tue' => '21:00-04:00', 'Wed' => '21:00-04:00', 'Thu' => '22:00-05:00', 'Fri' => '22:00-06:00', 'Sat' => '22:00-06:00'], // Öffnungszeiten können variieren
                'price_level' => '$$',
                'is_active' => true,
                'is_verified' => true,
                'accessibility_features' => ['wheelchair_accessible' => false, 'details' => 'Kann sehr voll werden, Stufen vorhanden.'],
                'owner_id' => $clubOwner?->id, // Beispiel: Dem Beispiel-Owner zuweisen
                'genres' => ['Drum & Bass', 'Techno', 'Electronic', 'Live Music', 'Alternative']
            ],
            [
                'name' => 'Pratersauna',
                'description' => 'Club mit Pool im Garten, bekannt für House und Techno Events.',
                'street_address' => 'Waldsteingartenstraße 135',
                'city' => 'Wien',
                'zip_code' => '1020',
                'country' => 'AT',
                'latitude' => 48.2158, // Ungefähre Koordinaten
                'longitude' => 16.4075, // Ungefähre Koordinaten
                'website' => 'https://pratersauna.tv/',
                'opening_hours' => ['Fri' => '23:00-06:00', 'Sat' => '23:00-06:00'], // Hauptsächlich Wochenende, Sommer auch tagsüber
                'price_level' => '$$$',
                'is_active' => true,
                'is_verified' => false, // Beispiel nicht verifiziert
                'accessibility_features' => ['details' => 'Outdoor-Bereich zugänglicher als Indoor.'],
                'owner_id' => null,
                'genres' => ['House', 'Techno', 'Deep House', 'Disco']
            ],
            [
                'name' => 'Das Werk',
                'description' => 'Underground-Club in Wien bekannt für Techno und eine alternative Atmosphäre.',
                'street_address' => 'Spittelauer Lände 12, Stadtbahnbogen 331', // Oft mit Bogen-Nummer
                'city' => 'Wien',
                'zip_code' => '1090',
                'country' => 'AT',
                'latitude' => 48.2318, // Nahe Grelle Forelle
                'longitude' => 16.3650, // Nahe Grelle Forelle
                'website' => 'https://daswerk.org/',
                'opening_hours' => ['Fri' => '23:00-06:00', 'Sat' => '23:00-06:00'], // Fokussiert auf Wochenende
                'price_level' => '$$',
                'is_active' => true,
                'is_verified' => false, // Beispiel nicht verifiziert
                'accessibility_features' => null,
                'owner_id' => null,
                'genres' => ['Techno', 'Hard Techno', 'Industrial', 'Electronic', 'Experimental']
            ],
            [
                'name' => 'Voga Club', // Voga hinzugefügt
                'description' => 'Stylischer Club in Stuttgart, oft mit Hip Hop, R&B und House.',
                'street_address' => 'Bolzstraße 7',
                'city' => 'Stuttgart',
                'zip_code' => '70173',
                'country' => 'DE',
                'latitude' => 48.7795, // Ungefähre Koordinaten
                'longitude' => 9.1782, // Ungefähre Koordinaten
                'website' => 'https://voga-stuttgart.de/', // Beispiel-Website, ggf. anpassen
                'opening_hours' => ['Fri' => '23:00-05:00', 'Sat' => '23:00-05:00'],
                'price_level' => '$$$',
                'is_active' => true,
                'is_verified' => false, // Beispiel nicht verifiziert
                'accessibility_features' => null,
                'owner_id' => null,
                'genres' => ['Hip Hop', 'R&B', 'House', 'Charts']
            ],
            [
                'name' => 'Uebel & Gefährlich',
                'description' => 'Club und Konzertlocation im Hochbunker auf St. Pauli.',
                'street_address' => 'Feldstraße 66',
                'city' => 'Hamburg',
                'zip_code' => '20359',
                'country' => 'DE',
                'latitude' => 53.5565, // Ungefähre Koordinaten
                'longitude' => 9.9665, // Ungefähre Koordinaten
                'website' => 'https://uebelundgefaehrlich.com/',
                'opening_hours' => ['Fri' => '23:59-07:00', 'Sat' => '23:59-07:00'], // Stark Event-abhängig
                'price_level' => '$$$',
                'is_active' => true,
                'is_verified' => true,
                'accessibility_features' => ['wheelchair_accessible' => false, 'details' => 'Im Bunker, viele Treppen.'],
                'owner_id' => $clubOwner?->id, // Beispiel: Dem Beispiel-Owner zuweisen
                'genres' => ['Techno', 'House', 'Indie', 'Electronic', 'Live Music']
            ],
            [
                'name' => 'Robert Johnson',
                'description' => 'Minimalistischer Club in Offenbach (bei Frankfurt) mit Fokus auf House und Techno.',
                'street_address' => 'Nordring 131',
                'city' => 'Offenbach am Main', // Technisch Offenbach, gehört zur Frankfurter Szene
                'zip_code' => '63067',
                'country' => 'DE',
                'latitude' => 50.1110, // Ungefähre Koordinaten
                'longitude' => 8.7500, // Ungefähre Koordinaten
                'website' => 'https://robert-johnson.de/',
                'opening_hours' => ['Fri' => '23:59-08:00', 'Sat' => '23:59-08:00'], // Oft lange Öffnungszeiten
                'price_level' => '$$$',
                'is_active' => true,
                'is_verified' => true,
                'accessibility_features' => null, // Unbekannt, ggf. prüfen
                'owner_id' => null,
                'genres' => ['House', 'Techno', 'Minimal', 'Deep House']
            ],
            [
                'name' => 'Pacha München',
                'description' => 'Münchner Ableger der bekannten Clubkette aus Ibiza.',
                'street_address' => 'Maximiliansplatz 5',
                'city' => 'München',
                'zip_code' => '80333',
                'country' => 'DE',
                'latitude' => 48.1412, // Ungefähre Koordinaten
                'longitude' => 11.5705, // Ungefähre Koordinaten
                'website' => 'https://pacha-muenchen.de/',
                'opening_hours' => ['Thu' => '22:00-04:00','Fri' => '23:00-05:00', 'Sat' => '23:00-05:00'],
                'price_level' => '$$$',
                'is_active' => true,
                'is_verified' => true,
                'accessibility_features' => ['details' => 'Ebenerdig, aber oft sehr voll.'], // Beispiel Angabe
                'owner_id' => null,
                'genres' => ['House', 'Tech House', 'EDM', 'Commercial House']
            ],
             [
                'name' => 'Distillery',
                'description' => 'Ältester Techno-Club Ostdeutschlands in Leipzig.',
                'street_address' => 'Kurt-Eisner-Straße 108a', // Adresse könnte sich ändern/geändert haben
                'city' => 'Leipzig',
                'zip_code' => '04275',
                'country' => 'DE',
                'latitude' => 51.3221, // Ungefähre Koordinaten
                'longitude' => 12.3675, // Ungefähre Koordinaten
                'website' => 'https://distillery.de/',
                'opening_hours' => ['Fri' => '23:59-08:00', 'Sat' => '23:59-09:00'], // Typische Zeiten
                'price_level' => '$$',
                'is_active' => true,
                'is_verified' => false, // Beispiel
                'accessibility_features' => null,
                'owner_id' => $clubOwner?->id, // Beispiel
                'genres' => ['Techno', 'House', 'Drum & Bass', 'Electronic']
            ],
        ];

        foreach ($clubsData as $data) {
            // Finde die IDs der Genres anhand der Namen
            $genreNames = $data['genres'];
            unset($data['genres']); // Entferne Genre-Namen aus den Hauptdaten
            $genreIds = Genre::whereIn('name', $genreNames)->pluck('id');

            // Erstelle den Club
            $club = Club::create($data);

            // Weise die Genres über die Pivot-Tabelle zu
            if ($genreIds->isNotEmpty()) {
                $club->genres()->sync($genreIds); // sync ist sicher für Seeder
            }
        }

        $this->command->info('ClubSeeder executed successfully!');
    }
}