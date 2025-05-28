<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class DjProfile extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'user_id',
        'stage_name',
        'slug',
        'bio',
        'profile_image_path',
        'banner_image_path',
        'social_links',
        'music_links',
        'is_visible',
        'is_verified',
        'booking_email',
        'technical_rider_path',
    ];

    protected $casts = [
        'social_links' => 'array',
        'music_links' => 'array',
        'is_visible' => 'boolean',
        'is_verified' => 'boolean',
    ];

    /**
     * Get the options for generating the slug.
     * Generiert Slug aus Stage Name oder User Name als Fallback.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(function($model) {
                // Nutze stage_name wenn vorhanden, sonst User Name
                return $model->stage_name ?? $model->user->name;
             })
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
     * Beziehung zum zugehörigen User-Account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor für den anzuzeigenden Namen (Stage Name oder User Name).
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->stage_name ?? $this->user->name;
    }
}