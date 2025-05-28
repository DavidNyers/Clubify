<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug; // Import HasSlug Trait
use Spatie\Sluggable\SlugOptions; // Import SlugOptions
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // <<< Hinzufügen
use App\Models\Event; // <<< Hinzufügen

class Genre extends Model
{
    use HasFactory, HasSlug; // Use HasSlug Trait

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug', // Wichtig: Slug auch fillable machen, falls man ihn manuell setzen will, aber meistens nicht nötig
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name') // Slug aus dem 'name'-Feld generieren
            ->saveSlugsTo('slug')       // Slug in der 'slug'-Spalte speichern
            ->slugsShouldBeNoLongerThan(50) // Optionale Längenbegrenzung
            ->doNotGenerateSlugsOnUpdate(); // Verhindert, dass der Slug sich bei Update ändert (oft gewünscht)
    }

    public function events(): BelongsToMany
    {
        // Verweist auf die Pivot-Tabelle 'event_genre'
        return $this->belongsToMany(Event::class, 'event_genre');
    }

    // --- ENDE NEUE RELATION ---

    // Füge hier auch die Relation zu Clubs hinzu, falls noch nicht geschehen
    // (wird für $availableGenres nicht direkt benötigt, aber für die Vollständigkeit)
     public function clubs(): BelongsToMany
     {
         return $this->belongsToMany(Club::class, 'club_genre');
     }

    /**
     * Get the route key for the model.
     * Implicit Route Model Binding mit Slug statt ID.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'slug'; // URLs wie /admin/genres/techno statt /admin/genres/1
    }
}