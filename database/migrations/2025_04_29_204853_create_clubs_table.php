<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable(); // Für Rich Text später vorbereitet

            // Adresse (vereinfacht für den Anfang, kann später normalisiert werden)
            $table->string('street_address')->nullable();
            $table->string('city')->nullable();
            $table->string('zip_code', 10)->nullable();
            $table->string('country', 2)->default('DE'); // ISO 3166-1 alpha-2

            // Geo-Koordinaten (für Karten)
            $table->decimal('latitude', 10, 8)->nullable(); // Genauigkeit für Karten
            $table->decimal('longitude', 11, 8)->nullable(); // Genauigkeit für Karten

            // Kontakt & Links
            $table->string('website')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable(); // Kontakt-Email des Clubs

            // Öffnungszeiten (als JSON speichern, flexibel)
            // Format-Beispiel: {"Mon": "22:00-05:00", "Tue": "closed", "Fri": "23:00-06:00", "Sat": "23:00-07:00"}
            $table->json('opening_hours')->nullable();

            // Preisniveau (optional, z.B. Enum oder einfach Text)
            $table->enum('price_level', ['$', '$$', '$$$'])->nullable();

            // Status & Verifizierung
            $table->boolean('is_active')->default(true); // Sichtbarkeit im Frontend
            $table->boolean('is_verified')->default(false); // Vom Admin geprüft/verifiziert

            // Barrierefreiheit (als JSON für Flexibilität am Anfang)
            // Format-Beispiel: {"wheelchair_accessible": true, "accessible_restrooms": true, "low_counter": false, "details": "Rampe am Eingang vorhanden."}
            $table->json('accessibility_features')->nullable();

            // Optional: Verknüpfung zum Benutzer mit der Rolle 'ClubOwner'
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            // Indizes für häufige Suchen
            $table->index('city');
            $table->index('is_active');
            $table->index('is_verified');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clubs');
    }
};