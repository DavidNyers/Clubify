<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // Import für Relation
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import für Owner
use Illuminate\Database\Eloquent\Relations\HasMany; 
use App\Models\Event;                            
use App\Models\Rating;
use Illuminate\Support\Carbon;    

class Club extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'street_address',
        'city',
        'zip_code',
        'country',
        'latitude',
        'longitude',
        'website',
        'phone',
        'email',
        'opening_hours',
        'price_level',
        'is_active',
        'is_verified',
        'accessibility_features',
        'owner_id',
    ];

    protected $casts = [
        'opening_hours' => 'array',
        'accessibility_features' => 'array',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
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
     * Relationship: Die Genres, die zu diesem Club gehören.
     */
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'club_genre');
    }

     /**
     * Relationship: Der Benutzer, dem dieser Club gehört (optional).
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function events(): HasMany
    {
        // Annahme: Der Fremdschlüssel in der 'events'-Tabelle heißt 'club_id'
        return $this->hasMany(Event::class);
    }

    public function upcomingEvents(): HasMany
    {
        return $this->hasMany(Event::class)
                     ->where('is_active', true)       // Nur aktive Events
                     ->whereNull('cancelled_at')    // Nur nicht abgesagte Events
                     ->where('start_time', '>=', Carbon::now()) // Nur Events, die jetzt oder später beginnen
                     ->orderBy('start_time', 'asc'); // Sortiere nach Startzeit (nächste zuerst)
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function galleryImages()
    {
        return $this->hasMany(ClubImage::class);
    }

    public function checkIns()
{
    return $this->hasMany(CheckIn::class);
}


    // --- Hilfsmethoden (Beispiele) ---

    /**
     * Gibt die vollständige Adresse als String zurück.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([$this->street_address, $this->zip_code . ' ' . $this->city]);
        return implode(', ', $parts);
    }

    /**
    * Gibt die Öffnungszeiten als formatierten String zurück (Beispielimplementierung).
    * Für eine robustere Lösung wäre eine Library sinnvoll.
    */
    public function getFormattedOpeningHoursAttribute(): string
    {
        // Daten sind da (Array), also sollte diese Prüfung OK sein
        if (empty($this->opening_hours) || !is_array($this->opening_hours)) {
            // Dieser Teil wird NICHT ausgeführt, wenn das Debug-Array angezeigt wurde.
            return '<span class="text-gray-500 italic">Keine Angaben</span>';
        }

        $output = []; // Startet als leeres Array

        // Deutsche Wochentage für die Anzeige
        $daysMap = [
            'Mon' => 'Montag', 'Tue' => 'Dienstag', 'Wed' => 'Mittwoch',
            'Thu' => 'Donnerstag', 'Fri' => 'Freitag', 'Sat' => 'Samstag', 'Sun' => 'Sonntag'
        ];
        // Reihenfolge für die Ausgabe
        $orderedKeys = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']; // <-- WICHTIG: Sind diese Keys korrekt?

        // Geht die definierten Tage durch
        foreach ($orderedKeys as $key) {
            // <<< PRÜFUNG HIER: >>>
            // Existiert der Schlüssel (z.B. 'Fri') im Daten-Array $this->opening_hours?
            if (isset($this->opening_hours[$key])) {
                // <<< DIESER BLOCK SOLLTE FÜR 'Fri' und 'Sat' AUSGEFÜHRT WERDEN >>>
                $label = $daysMap[$key] ?? $key;
                $time = $this->opening_hours[$key];
                $timeDisplay = ($time === 'closed' || empty($time)) ? 'Geschlossen' : str_replace('-', ' - ', $time) . ' Uhr';
                // Fügt HTML zum $output Array hinzu
                $output[] = '<div class="flex justify-between"><span class="font-medium w-24">' . e($label) . ':</span><span>' . e($timeDisplay) . '</span></div>';
            } else {
                 // Dieser Block wird für die anderen Tage ausgeführt (Mon, Tue, Wed, Thu, Sun)
                 // Hier wird nichts zum $output hinzugefügt (außer du aktivierst den auskommentierten Teil)
            }
            logger("Final Output Array Count: " . count($output));
        } // Ende foreach

        // <<< PRÜFUNG HIER: >>>
        // Ist $output nach der Schleife immer noch leer?
        // Wenn ja, warum wurde im `if (isset(...))` nichts hinzugefügt?
        // Mögliche Gründe:
        //   - Die Keys in $orderedKeys ('Mon', 'Tue'...) stimmen NICHT mit den Keys in $this->opening_hours ('Fri', 'Sat') überein (Groß/Kleinschreibung?)
        //   - Die `e()` Funktion (htmlspecialchars) verursacht Probleme (unwahrscheinlich für den String hier).

        // Gibt den zusammengefügten HTML-String zurück oder "Keine Angaben"
        return implode('', $output) ?: '<span class="text-gray-500 italic">Keine Angaben (im Accessor generiert)</span>'; // Angepasste Meldung
    }
}