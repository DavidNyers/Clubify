<?php

namespace App\Models;

// Standard Laravel / Breeze Imports
use Illuminate\Contracts\Auth\MustVerifyEmail; // Nur wenn E-Mail-Verifizierung aktiv genutzt wird
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Für API Tokens (falls verwendet)

// Spatie Permissions Import
use Spatie\Permission\Traits\HasRoles;

// Imports für neue Relationen
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Event; // Model für Events
use App\Models\Club;  // Model für Clubs
use App\Models\DjProfile;  // Model für DJ
use App\Models\Rating;   


class User extends Authenticatable /* Optional: implements MustVerifyEmail */
{
    // Standard Traits
    use HasApiTokens, HasFactory, Notifiable;

    // Trait für Rollen & Berechtigungen
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'last_login_at',
        'partner_status', 
        'partner_application_notes',
        'partner_status_processed_by', 
        'partner_status_processed_at', 
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime', // Cast für das neue Feld
        'password' => 'hashed', // Wichtig für Laravel 10+ Hashing
        'partner_status_processed_at' => 'datetime',
    ];


    // =========================================
    // RELATIONEN (BEZIEHUNGEN ZU ANDEREN MODELS)
    // =========================================

    /**
     * Die Events, die dieser Benutzer als Veranstalter erstellt hat.
     * Wird für withCount('events') im UserController verwendet.
     */
    public function events(): HasMany
    {
        // Annahme: Der Fremdschlüssel in der 'events'-Tabelle heißt 'organizer_id'
        return $this->hasMany(Event::class, 'organizer_id');
    }

    /**
     * Die Clubs, die diesem Benutzer als Besitzer gehören.
     * Wird für withCount('clubs') im UserController verwendet.
     */
    public function clubs(): HasMany
    {
         // Annahme: Der Fremdschlüssel in der 'clubs'-Tabelle heißt 'owner_id'
        return $this->hasMany(Club::class, 'owner_id');
    }

    /**
     * Die Events (Gigs), bei denen dieser Benutzer als DJ auftritt.
     * Wird für withCount('djGigs') im UserController verwendet.
     */
    public function djGigs(): BelongsToMany
    {
        // Verknüpft User mit Events über die Pivot-Tabelle 'event_dj'.
        // Parameter:
        // 1. Verknüpftes Model: Event::class
        // 2. Pivot-Tabelle: 'event_dj'
        // 3. Fremdschlüssel des aktuellen Models (User) in der Pivot-Tabelle: 'dj_id'
        // 4. Fremdschlüssel des verknüpften Models (Event) in der Pivot-Tabelle: 'event_id'
        return $this->belongsToMany(Event::class, 'event_dj', 'dj_id', 'event_id');
    }

    public function djProfile(): HasOne
    {
        return $this->hasOne(DjProfile::class);
    }

    public function partnerStatusProcessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'partner_status_processed_by');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function bookmarkedEvents(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'bookmarked_events', 'user_id', 'event_id')
                    ->withTimestamps(); // Lädt created_at/updated_at von der Pivot-Tabelle (optional)
    }

    // Hier könnten später weitere Relationen hinzukommen, z.B.:
    // - Abonnements
    // - Bestellungen
    // - Gespeicherte/Verfolgte Events
    // - Geschriebene Bewertungen
    // - Hochgeladene Bilder (wenn User Bilder hochladen können)


    // =========================================
    // ACCESSORS & MUTATORS (Optional)
    // =========================================
    // Hier könnten z.B. Accessoren für formatierte Namen, Initialen etc. stehen.


    // =========================================
    // SCOPES (Optional)
    // =========================================
    // Hier könnten Scopes stehen, um z.B. nur aktive User oder User mit einer bestimmten Rolle zu finden.
    // Beispiel: public function scopeActive($query) { return $query->where('is_active', true); }


}