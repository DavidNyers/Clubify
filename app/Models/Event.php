<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon; // Für Datums-Handling

use App\Models\User;

class Event extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'start_time',
        'end_time',
        'cover_image_path',
        'club_id',
        'organizer_id',
        'price',
        'currency',
        'is_active',
        'requires_approval',
        'allows_presale',
        'allows_guestlist',
        'cancelled_at',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'cancelled_at' => 'datetime',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'requires_approval' => 'boolean',
        'allows_presale' => 'boolean',
        'allows_guestlist' => 'boolean',
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        // Slug aus Name und Datum (Jahr) generieren, um Eindeutigkeit zu erhöhen
        return SlugOptions::create()
            ->generateSlugsFrom(['name', 'start_year']) // Benutzt 'name' und die Accessor-Methode 'start_year'
            ->saveSlugsTo('slug')
            ->slugsShouldBeNoLongerThan(80) // Etwas länger für Datum
            ->doNotGenerateSlugsOnUpdate();
    }

     /**
     * Accessor, um das Jahr des Startdatums für den Slug zu bekommen.
     * Wird von getSlugOptions verwendet.
     */
    public function getStartYearAttribute(): string
    {
        if ($this->start_time instanceof Carbon) {
            return $this->start_time->format('Y');
        }
        // Fallback, falls start_time noch nicht gesetzt oder kein Carbon-Objekt ist
        try {
             return Carbon::parse($this->attributes['start_time'])->format('Y');
        } catch (\Exception $e) {
            return date('Y'); // Aktuelles Jahr als Fallback
        }
    }


    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // --- Beziehungen ---

    /**
     * Der Club, in dem das Event stattfindet.
     */
    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    /**
     * Der Veranstalter (Benutzer mit Rolle 'Organizer'), der das Event organisiert.
     */
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    /**
     * Die Genres, die zu diesem Event gehören.
     */
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'event_genre');
    }

    /**
     * Die DJs (Benutzer mit Rolle 'DJ'), die bei diesem Event auflegen.
     */
    public function djs(): BelongsToMany
    {
        // Pivot-Tabelle 'event_dj', Fremdschlüssel 'dj_id' verweist auf User-ID
        return $this->belongsToMany(User::class, 'event_dj', 'event_id', 'dj_id');
    }

    // --- Hilfsmethoden / Scopes ---

     /**
     * Ist das Event abgesagt?
     */
    public function isCancelled(): bool
    {
        return !is_null($this->cancelled_at);
    }

    /**
     * Scope: Nur aktive Events.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->whereNull('cancelled_at');
    }

     /**
     * Scope: Nur zukünftige Events.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>=', now());
    }

    /**
    * Formatiert den Preis für die Anzeige.
    */
    public function getFormattedPriceAttribute(): string
    {
        if (is_null($this->price) || $this->price == 0) {
            return 'Kostenlos';
        }
         // Formatierung für Deutschland/Euro
        return number_format($this->price, 2, ',', '.') . ' ' . $this->currency;
    }

     /**
    * Formatiert Start- und Enddatum/-zeit für die Anzeige.
    */
    public function getFormattedStartEndAttribute(): string
    {
        if (!$this->start_time) return 'N/A';

        $startFormat = 'd.m.Y H:i';
        $endFormat = 'H:i'; // Standard nur Endzeit, wenn am selben Tag

        $start = $this->start_time;
        $end = $this->end_time;

        if ($end) {
            // Wenn End-Datum anders als Start-Datum, volles Datum anzeigen
            if ($start->format('Y-m-d') !== $end->format('Y-m-d')) {
                 $endFormat = 'd.m.Y H:i';
            }
            return $start->format($startFormat) . ' - ' . $end->format($endFormat) . ' Uhr';
        }

         return $start->format($startFormat) . ' Uhr';
    }

    public function bookmarkedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'bookmarked_events', 'event_id', 'user_id')
                    ->withTimestamps();
    }

}