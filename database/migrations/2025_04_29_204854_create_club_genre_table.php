<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pivot-Tabelle für die Many-to-Many Beziehung zwischen Clubs und Genres
        Schema::create('club_genre', function (Blueprint $table) {
            $table->foreignId('club_id')->constrained()->onDelete('cascade'); // Löscht Eintrag, wenn Club gelöscht wird
            $table->foreignId('genre_id')->constrained()->onDelete('cascade'); // Löscht Eintrag, wenn Genre gelöscht wird
            $table->primary(['club_id', 'genre_id']); // Zusammengesetzter Primärschlüssel
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('club_genre');
    }
};