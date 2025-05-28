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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // z.B. Free, Basic, Premium
            $table->string('slug')->unique();
            $table->decimal('price', 8, 2)->default(0.00); // Preis, z.B. 9.99
            $table->string('currency', 3)->default('EUR'); // Währungscode (ISO 4217)
            $table->enum('billing_interval', ['month', 'year'])->default('month'); // Abrechnungsintervall
            $table->text('description')->nullable(); // Beschreibung für Benutzer
            $table->json('features')->nullable(); // Features als JSON-Array ['Feature 1', 'Feature 2']
            $table->string('stripe_plan_id')->nullable()->unique(); // ID von Stripe (wichtig für später)
            $table->string('paypal_plan_id')->nullable()->unique(); // ID von PayPal (wichtig für später)
            $table->boolean('is_active')->default(true); // Kann der Plan abonniert werden?
            $table->unsignedInteger('trial_days')->default(0)->nullable(); // Anzahl der Testtage
            $table->integer('sort_order')->default(0); // Für die Anzeigereihenfolge
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};