<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PartnerApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Erstelle 2-3 Benutzer mit Status 'pending'
        User::factory()->create([
            'name' => 'Antragsteller Club',
            'email' => 'pending.club@clubify.test',
            'password' => Hash::make('password'),
            'partner_status' => 'pending', // Wichtig!
        ]);

         User::factory()->create([
            'name' => 'Antragsteller DJ',
            'email' => 'pending.dj@clubify.test',
            'password' => Hash::make('password'),
            'partner_status' => 'pending',
        ]);

        User::factory()->create([
            'name' => 'Antragsteller Veranstalter',
            'email' => 'pending.organizer@clubify.test',
            'password' => Hash::make('password'),
            'partner_status' => 'pending',
        ]);

        $this->command->info('Pending partner application users seeded.');
    }
}