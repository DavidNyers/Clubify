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
    Schema::create('check_ins', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
        $table->foreignId('event_id')->nullable()->constrained('events')->onDelete('set null');
        $table->timestamp('created_at')->useCurrent();
        // Ein User kann pro Club pro Tag nur einmal einchecken
        $table->unique(['user_id', 'club_id', \DB::raw('DATE(created_at)')], 'user_club_daily_checkin_unique');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('check_ins');
    }
};
