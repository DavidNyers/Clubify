<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookmarked_events', function (Blueprint $table) {
            // Zusammengesetzter Primärschlüssel
            $table->primary(['user_id', 'event_id']);

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');

            $table->timestamps(); // Optional: Um zu sehen, wann ein Event gemerkt wurde
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookmarked_events');
    }
};