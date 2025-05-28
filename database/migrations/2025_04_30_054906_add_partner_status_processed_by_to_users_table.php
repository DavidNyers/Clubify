<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Welcher Admin hat den Antrag bearbeitet?
            $table->foreignId('partner_status_processed_by')
                  ->nullable()
                  ->after('partner_application_notes')
                  ->constrained('users') // Verweist auf die ID des Admins in der users Tabelle
                  ->nullOnDelete(); // Setzt auf NULL, wenn der Admin-User gelöscht wird

            // Wann wurde der Antrag bearbeitet?
            $table->timestamp('partner_status_processed_at')
                  ->nullable()
                  ->after('partner_status_processed_by');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Wichtig: Zuerst den Foreign Key Constraint entfernen
            $table->dropForeign(['partner_status_processed_by']);
            $table->dropColumn(['partner_status_processed_by', 'partner_status_processed_at']);
        });
    }
};