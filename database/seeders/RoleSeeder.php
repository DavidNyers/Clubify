<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission; // Falls benötigt

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Alte Rollen löschen, um Konflikte zu vermeiden, falls Seeder erneut läuft
        // Vorsicht: Nicht im Produktivbetrieb!
        // Role::query()->delete(); // Optional

        // Definiere Rollen genau wie im Prompt beschrieben
        $roles = [
            'Administrator',
            'Organizer',
            'ClubOwner',
            'DJ',
            'Moderator',
            'Doorman',
            'VIP', // "Friends" Rolle
            'User', // Standard-Benutzerrolle
        ];

        foreach ($roles as $roleName) {
             // Erstellt Rolle nur, wenn sie noch nicht existiert
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

         $this->command->info('Standard roles seeded successfully.');

        // Optional: Hier könnten auch Berechtigungen erstellt und zugewiesen werden
    }
}