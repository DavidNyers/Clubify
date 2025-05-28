<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dj_profiles', function (Blueprint $table) {
            $table->id();

            // Verknüpfung zum User-Account (1-zu-1 Beziehung)
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');

            // DJ-spezifische Daten
            $table->string('stage_name')->nullable(); // Kann vom User->name abweichen
            $table->string('slug')->unique();        // Eigener Slug für DJ-Profil-URL
            $table->text('bio')->nullable();         // Biografie (für Rich Text Editor)
            $table->string('profile_image_path')->nullable(); // Profilbild
            $table->string('banner_image_path')->nullable();  // Bannerbild
            $table->json('social_links')->nullable(); // z.B. {"soundcloud": "...", "instagram": "..."}
            $table->json('music_links')->nullable();  // z.B. {"soundcloud_track": "...", "mixcloud_mix": "..."}
            $table->boolean('is_visible')->default(true); // Sichtbar im Frontend-Verzeichnis?
            $table->boolean('is_verified')->default(false); // Vom Admin geprüft? (Kann mit partner_status verknüpft sein)

            // Optional: Booking-Infos
            $table->string('booking_email')->nullable();
            $table->string('technical_rider_path')->nullable(); // Pfad zum PDF o.ä.

            $table->timestamps();

            $table->index('is_visible');
            $table->index('is_verified');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dj_profiles');
    }
};