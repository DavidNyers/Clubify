<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();

            // Fremdschlüssel zum Benutzer, der bewertet hat
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Fremdschlüssel zum bewerteten Club
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');

            // Bewertung (Sterne)
            $table->unsignedTinyInteger('rating'); // 1 bis 5 Sterne (TinyInteger ist platzsparend)

            // Kommentar (optional)
            $table->text('comment')->nullable();

            // Moderationsstatus
            $table->boolean('is_approved')->default(false); // Muss vom Admin freigegeben werden
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete(); // Welcher Admin hat freigegeben?
            $table->timestamp('approved_at')->nullable(); // Wann wurde freigegeben?

            $table->timestamps(); // created_at, updated_at

            // Indizes für häufige Abfragen
            $table->index(['club_id', 'is_approved', 'created_at']);
            $table->index('user_id');

            // Optional: Ein Benutzer kann einen Club nur einmal bewerten?
            // $table->unique(['user_id', 'club_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};