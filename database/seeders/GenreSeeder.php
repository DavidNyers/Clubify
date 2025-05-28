<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Genre;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genres = [
            'Techno', 'House', 'Deep House', 'Tech House', 'Minimal Techno',
            'Drum and Bass', 'Hardstyle', 'Goa Trance', 'Psytrance', 'Electro',
            'Hip Hop', 'Rap', 'R&B', 'Charts', '80s Pop',
            '90s Pop', 'Rock', 'Classic Rock', 'Hard Rock', 'Alternative Rock',
            'Indie Rock', 'Punk Rock', 'Post-Punk', 'New Wave', 'Grunge',
            'Heavy Metal', 'Thrash Metal', 'Death Metal', 'Black Metal', 'Doom Metal',
            'Power Metal', 'Symphonic Metal', 'Metalcore', 'Deathcore', 'Nu Metal',
            'Progressive Metal', 'Glam Metal', 'Stoner Rock', 'Psychedelic Rock', 'Progressive Rock',
            'Folk Rock', 'Soft Rock', 'Pop Rock', 'Garage Rock', 'Surf Rock',
            'Rock and Roll', 'Southern Rock', 'Blues Rock', 'Gothic Rock', 'Emo',
            'Screamo', 'Shoegaze', 'Britpop', 'Acid House', 'Progressive House',
            'Funky House', 'Tribal House', 'Future House', 'Bass House', 'UK Garage',
            'Chicago House', 'Detroit Techno', 'Minimal House', 'French House', 'Electro House',
            'Big Room House', 'Tropical House', 'Ghetto House', 'Microhouse', 'Acid Techno',
            'Industrial Techno', 'Hard Techno', 'Dub Techno', 'Liquid DnB', 'Neurofunk',
            'Jump-Up DnB', 'Jungle', 'Breakbeat Hardcore', 'Darkstep', 'Techstep',
            'Trance', 'Progressive Trance', 'Uplifting Trance', 'Vocal Trance', 'Hard Trance',
            'Acid Trance', 'Tech Trance', 'Classic Trance', 'Dream Trance', 'Hardcore',
            'Gabber', 'Happy Hardcore', 'UK Hardcore', 'Frenchcore', 'Speedcore',
            'Terrorcore', 'Makina', 'Freeform Hardcore', 'Ambient', 'Chillout',
            'Lounge', 'Downtempo', 'Trip Hop', 'Glitch', 'IDM',
            'Psybient', 'Psychill', 'Dubstep', 'Brostep', 'Riddim',
            'UK Bass', 'Grime', 'Future Bass', 'Synthwave', 'Vaporwave',
            'Chiptune', '8-bit', 'Breakbeat', 'Big Beat', 'Nu-Disco',
            'Electroclash', 'EBM', 'Industrial Music', 'Dark Wave', 'Coldwave',
            'Old School Hip Hop', 'Golden Age Hip Hop', 'Gangsta Rap', 'Conscious Hip Hop', 'Alternative Hip Hop',
            'Southern Hip Hop', 'Trap', 'Drill', 'Cloud Rap', 'Boom Bap',
            'G-Funk', 'Crunk', 'Hyphy', 'Mumble Rap', 'UK Hip Hop',
            'Jazz Rap', 'Abstract Hip Hop', 'Contemporary R&B', 'Neo Soul', 'Quiet Storm',
            'Funk', 'Soul', 'Disco', 'Motown', 'New Jack Swing',
            'Alternative R&B', 'PBR&B', 'Pop', 'Synth-pop', 'Electropop',
            'Dance-Pop', 'Teen Pop', 'Bubblegum Pop', 'Power Pop', 'Indie Pop',
            'Art Pop', 'J-Pop', 'K-Pop', 'C-Pop', 'Europop',
            'Latin Pop', 'Folk', 'Country', 'Bluegrass', 'Americana',
            'Singer-Songwriter', 'World Music', 'Reggae', 'Dancehall', 'Dub',
            'Ska', 'Rocksteady', 'Soca', 'Calypso', 'Afrobeat',
            'Highlife', 'Latin Music', 'Salsa', 'Merengue', 'Bachata',
            'Cumbia', 'Tango', 'Bossa Nova', 'Samba', 'Reggaeton',
            'Celtic Music', 'Klezmer', 'Balkan Beats', 'Bhangra', 'Bollywood',
            'Flamenco', 'Fado', 'Traditional Folk', 'Jazz', 'Blues',
            'Swing', 'Big Band', 'Dixieland', 'Bebop', 'Cool Jazz',
            'Hard Bop', 'Modal Jazz', 'Free Jazz', 'Jazz Fusion', 'Smooth Jazz',
            'Acid Jazz', 'Delta Blues', 'Chicago Blues', 'Electric Blues', 'Country Blues',
            'Boogie Woogie', 'Classical', 'Baroque', 'Romantic Era', 'Modern Classical',
            'Opera', 'Orchestral', 'Chamber Music', 'Choral Music', 'Film Score',
            'Soundtrack', 'Video Game Music', 'Experimental', 'Noise Music', 'Avant-Garde',
            'Spoken Word', 'Comedy Music', 'A cappella', 'Christian Music', 'Gospel',
            'Library Music', 'Exotica', 'Space Age Pop', 'New Age', 'Drone',
            'Lo-fi Hip Hop', 'Phonk', 'Witch House', 'Seapunk', 'Hyperpop',
            'Country Rap', 'Folk Punk', 'Celtic Punk', 'Gypsy Punk', 'Cowpunk',
            'Psychobilly', 'Horror Punk', 'Skate Punk', 'Crust Punk', 'D-beat',
            'Post-Hardcore', 'Math Rock', 'Post-Rock', 'Sludge Metal', 'Grindcore',
            'Viking Metal', 'Folk Metal', 'Funk Metal', 'Rap Rock', 'Rap Metal',
            'Industrial Metal', 'Neoclassical Metal', 'Zeuhl', 'Krautrock', 'Freak Folk',
            'Lowercase', 'Glitch Hop', 'Moombahton', 'Complextro', 'Zouk'
        ];

        foreach ($genres as $genreName) {
            Genre::create(['name' => $genreName]); // Slug wird automatisch erstellt
        }
    }
}