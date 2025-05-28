<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hole die benötigten Rollen (oder erstelle sie zur Sicherheit)
        $adminRole = Role::firstOrCreate(['name' => 'Administrator']);
        $userRole = Role::firstOrCreate(['name' => 'User']);
        $organizerRole = Role::firstOrCreate(['name' => 'Organizer']);
        $djRole = Role::firstOrCreate(['name' => 'DJ']);
        $clubOwnerRole = Role::firstOrCreate(['name' => 'ClubOwner']); // Für später

        // 1. Admin-Benutzer erstellen
        User::firstOrCreate(
            ['email' => 'admin@clubify.test'], // Eindeutiger Identifikator
            [
                'name' => 'Admin User',
                'email_verified_at' => now(),
                'password' => Hash::make('password'), // !!! ÄNDERE DAS PASSWORT !!!
            ]
        )->assignRole($adminRole); // Rolle zuweisen

        // 2. Standard Test-Benutzer erstellen
        User::firstOrCreate(
            ['email' => 'user@clubify.test'],
            [
                'name' => 'Test User',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        )->assignRole($userRole);

        // 3. Veranstalter (Organizer) erstellen
        User::firstOrCreate(
            ['email' => 'organizer@clubify.test'],
            [
                'name' => 'Party Veranstalter GmbH', // Beispielname
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        )->assignRole($organizerRole);

         User::firstOrCreate(
            ['email' => 'eventmacher@clubify.test'],
            [
                'name' => 'Eventmacher Max',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        )->assignRole($organizerRole);

        // 4. DJs erstellen
        User::firstOrCreate(
            ['email' => 'dj.one@clubify.test'],
            [
                'name' => 'DJ One',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        )->assignRole($djRole);

         User::firstOrCreate(
            ['email' => 'dj.beatmaster@clubify.test'],
            [
                'name' => 'Beatmaster B',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        )->assignRole($djRole);

         User::firstOrCreate(
            ['email' => 'dj.synthia@clubify.test'],
            [
                'name' => 'Synthia Groove',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        )->assignRole($djRole);

        // 5. Club Besitzer (für später)
         User::firstOrCreate(
            ['email' => 'clubowner@clubify.test'],
            [
                'name' => 'Besitzer Klaus',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        )->assignRole($clubOwnerRole);


        $this->command->info('Admin, User, Organizer, DJ, and ClubOwner users seeded successfully.');
    }
}