<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function store(Request $request, Club $club)
    {
        $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        // Prüfen, ob User diesen Club schon bewertet hat (optional, wenn unique constraint in DB fehlt)
        // $existingRating = $club->ratings()->where('user_id', Auth::id())->first();
        // if ($existingRating) {
        //     return back()->with('error', 'Du hast diesen Club bereits bewertet.');
        // }

        // Bewertung erstellen (noch nicht approved)
        $club->ratings()->create([
            'user_id' => Auth::id(),
            'rating' => $request->input('rating'),
            'comment' => $request->input('comment'),
            'is_approved' => false, // Standard: Muss moderiert werden
        ]);

         // TODO: Benachrichtigung an Admin?

        return back()->with('success', 'Deine Bewertung wurde übermittelt und wird nach Prüfung veröffentlicht.');
    }
}