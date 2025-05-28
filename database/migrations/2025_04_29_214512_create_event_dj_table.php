<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_dj', function (Blueprint $table) {
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            // Annahme: DJs sind Benutzer (Users) mit der Rolle 'DJ'
            $table->foreignId('dj_id')->constrained('users')->onDelete('cascade');
            $table->primary(['event_id', 'dj_id']);
            // Optional: Spalte für Sortierung im Lineup, Set-Zeiten etc.
            // $table->integer('sort_order')->default(0);
            // $table->time('set_start_time')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_dj');
    }
};