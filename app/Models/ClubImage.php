<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClubImage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'club_id',
        'path',
        'original_name',
        'mime_type',
        'size',
        'order_column',
        'caption',
    ];

    /**
     * Get the club that owns the image.
     */
    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }
}