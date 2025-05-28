<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class SubscriptionPlan extends Model
{
    use HasFactory, HasSlug;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'price',
        'currency',
        'billing_interval',
        'description',
        'features', // Wird durch Casts als JSON behandelt
        'stripe_plan_id',
        'paypal_plan_id',
        'is_active',
        'trial_days',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2', // Stellt sicher, dass es als Dezimalzahl mit 2 Nachkommastellen behandelt wird
        'features' => 'array', // Wandelt das JSON aus der DB in ein PHP-Array um (und umgekehrt)
        'is_active' => 'boolean',
        'trial_days' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->slugsShouldBeNoLongerThan(50)
            ->doNotGenerateSlugsOnUpdate();
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Formatiert den Preis für die Anzeige.
     */
    public function getFormattedPrice(): string
    {
        // Formatierung für Deutschland/Euro
        return number_format($this->price, 2, ',', '.') . ' ' . $this->currency;
    }

     /**
     * Gibt den Intervall-Namen menschenlesbar zurück.
     */
    public function getIntervalLabel(): string
    {
        return $this->billing_interval === 'month' ? 'Monatlich' : 'Jährlich';
    }
}