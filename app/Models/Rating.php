<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'club_id',
        'rating',
        'comment',
        'is_approved',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    /**
     * Der Benutzer, der die Bewertung abgegeben hat.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Der Club, der bewertet wurde.
     */
    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

     /**
      * Der Admin, der die Bewertung freigegeben hat (optional).
      */
     public function approver(): BelongsTo
     {
         return $this->belongsTo(User::class, 'approved_by');
     }
}