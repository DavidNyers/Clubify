<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->longText('description')->nullable(); // Längerer Text, für Rich Text Editor vorbereitet
            $table->dateTime('start_time');             // Startzeitpunkt
            $table->dateTime('end_time')->nullable();       // Endzeitpunkt (optional)
            $table->string('cover_image_path')->nullable(); // Pfad zum Coverbild

            // Beziehungen
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade'); // Event gehört zu einem Club
            // Veranstalter wird später ein User mit Rolle 'Organizer' oder eine eigene Entität
            $table->foreignId('organizer_id')->nullable()->constrained('users')->nullOnDelete(); // Optional, setzt auf NULL wenn User gelöscht wird

            // Preisgestaltung (einfach)
            $table->decimal('price', 8, 2)->nullable(); // Eintrittspreis
            $table->string('currency', 3)->default('EUR');

            // Features & Status
            $table->boolean('is_active')->default(true); // Sichtbar im Frontend?
            $table->boolean('requires_approval')->default(true); // Muss Admin freigeben? (wichtig für Partner-Dashboards)
            $table->boolean('allows_presale')->default(false); // Gibt es VVK/Tischreservierung?
            $table->boolean('allows_guestlist')->default(false); // Gibt es Gästeliste?
            $table->timestamp('cancelled_at')->nullable(); // Zeitstempel, falls abgesagt

            $table->timestamps(); // created_at, updated_at

            // Indizes
            $table->index('slug');
            $table->index('start_time');
            $table->index('is_active');
            $table->index('club_id');
            $table->index('organizer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};