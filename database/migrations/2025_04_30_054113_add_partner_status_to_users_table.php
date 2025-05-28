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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('partner_status', ['none', 'pending', 'approved', 'rejected'])
                  ->default('none')
                  ->after('email_verified_at'); // Oder eine andere passende Position
            $table->text('partner_application_notes')->nullable()->after('partner_status'); // Für Ablehnungsgrund o.ä.

            $table->index('partner_status'); // Index für schnelles Filtern
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['partner_status', 'partner_application_notes']);
        });
    }
};