<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\Club; // Nicht unbedingt für die Query hier nötig, aber für die View-Daten
use App\Models\User; // Nicht unbedingt für die Query hier nötig, aber für die View-Daten
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Für DB::raw, falls komplexere Joins nötig werden

class RatingModerationController extends Controller
{
    /**
     * Zeigt die Liste der zu moderierenden Bewertungen an.
     */
    public function index(Request $request)
    {
        // === Filter Parameter holen ===
        $filterClubName = $request->input('club_name');
        $filterUserName = $request->input('user_name');
        $filterStars = $request->input('stars');

        // === Sortier Parameter holen & validieren ===
        $sortValue = $request->input('sort', 'date-asc'); // Default: Älteste zuerst (aufsteigend nach Datum)
        $sortParts = explode('-', $sortValue);
        $sortBy = $sortParts[0] ?? 'date';
        $sortDirectionInput = strtolower($sortParts[1] ?? 'asc');
        $sortDirection = in_array($sortDirectionInput, ['asc', 'desc']) ? $sortDirectionInput : 'asc';

        // === Basis Query Builder ===
        $query = Rating::query()
            ->where('ratings.is_approved', false) // Nur nicht freigegebene, mit Tabellen-Alias für Klarheit
            ->with(['user:id,name,email', 'club:id,name,slug']); // Lade Relationen

        // === FILTER ANWENDEN ===
        if ($filterClubName) {
            $query->whereHas('club', function ($q) use ($filterClubName) {
                $q->where('name', 'LIKE', '%' . $filterClubName . '%');
            });
        }
        if ($filterUserName) {
            $query->whereHas('user', function ($q) use ($filterUserName) {
                $q->where('name', 'LIKE', '%' . $filterUserName . '%')
                  ->orWhere('email', 'LIKE', '%' . $filterUserName . '%');
            });
        }
        if ($filterStars) {
            if (str_contains($filterStars, '-')) {
                list($min, $max) = explode('-', $filterStars);
                if (is_numeric($min) && is_numeric($max) && $min >= 1 && $max <=5 && $min <= $max) {
                    $query->whereBetween('ratings.rating', [(int)$min, (int)$max]);
                }
            } elseif (is_numeric($filterStars) && $filterStars >= 1 && $filterStars <= 5) {
                $query->where('ratings.rating', (int)$filterStars);
            }
        }

        // === SORTIERUNG ANWENDEN ===
        // Um Mehrdeutigkeiten bei Spaltennamen zu vermeiden, verwenden wir Aliase der Tabellen,
        // wenn wir nach Feldern aus verknüpften Tabellen sortieren wollen.
        if ($sortBy === 'club') {
            $query->select('ratings.*') // Wähle alle Rating-Spalten explizit, um Konflikte zu vermeiden
                  ->join('clubs', 'ratings.club_id', '=', 'clubs.id')
                  ->orderBy('clubs.name', $sortDirection);
        } elseif ($sortBy === 'user') {
            $query->select('ratings.*')
                  ->join('users', 'ratings.user_id', '=', 'users.id')
                  ->orderBy('users.name', $sortDirection);
        } elseif ($sortBy === 'rating') {
            $query->orderBy('ratings.rating', $sortDirection);
        } elseif ($sortBy === 'date') {
            $query->orderBy('ratings.created_at', $sortDirection);
        } else {
            // Fallback-Sortierung
            $query->orderBy('ratings.created_at', 'asc');
        }
        // Immer einen eindeutigen Tie-Breaker hinzufügen, um Paginierungsprobleme zu vermeiden
        $query->orderBy('ratings.id', 'asc');


        // === Daten holen ===
        // Wenn Joins verwendet wurden, müssen wir sicherstellen, dass `select('ratings.*')`
        // die Paginierung nicht stört oder ggf. `groupBy('ratings.id')` verwenden,
        // aber da wir nach dem Join `orderBy` auf die gejointen Spalten anwenden,
        // sollte es für die Sortierung funktionieren. Für die Paginierung selbst ist es besser,
        // wenn die Hauptentität (ratings) die Basis bleibt.
        // `distinct()` könnte nötig sein, wenn Joins zu Duplikaten führen.
        // Hier gehen wir davon aus, dass die Relationen 1:n sind und keine Duplikate erzeugen.
        if ($sortBy === 'club' || $sortBy === 'user') {
            // Wenn wir gejoint haben, ist es sicherer, die Ergebnisse zu gruppieren,
            // um sicherzustellen, dass wir pro Rating nur einen Eintrag bekommen.
            // Oder wir stellen sicher, dass unser select() alle benötigten Spalten enthält und
            // die Paginierung auf der Haupttabelle basiert.
            // Da wir `select('ratings.*')` verwenden, sollte es in Ordnung sein.
        }

        $pendingRatings = $query->paginate(20) // z.B. 20 pro Seite
                                 ->appends($request->query()); // Hänge alle Request-Parameter an die Paginierungslinks an

        // === Daten für Filter-Dropdowns (optional, hier nicht implementiert für Einfachheit) ===
        // $availableClubsForFilter = Club::whereHas('ratings', fn($q) => $q->where('is_approved', false))->orderBy('name')->pluck('name','id');

        return view('admin.ratings-moderation.index', compact(
            'pendingRatings',
            'filterClubName',
            'filterUserName',
            'filterStars',
            'sortValue'
            // 'availableClubsForFilter'
        ));
    }

    /**
     * Gibt eine Bewertung frei.
     */
    public function approve(Rating $rating)
    {
        if ($rating->is_approved) {
            return back()->with('info', 'Diese Bewertung wurde bereits freigegeben.');
        }

        $rating->is_approved = true;
        $rating->approved_by = Auth::id();
        $rating->approved_at = now();
        $rating->save();

        return redirect()->route('admin.ratings.moderation.index')
                         ->with('success', "Bewertung für Club '".($rating->club->name ?? 'Unbekannt')."' wurde freigegeben.");
    }

    /**
     * Lehnt eine Bewertung ab (löscht sie oder markiert sie als abgelehnt).
     */
    public function reject(Rating $rating)
    {
        try {
            $originalClubName = $rating->club?->name ?? 'Unbekannt';
            $rating->delete(); // Für dieses Beispiel löschen wir die Bewertung

            return redirect()->route('admin.ratings.moderation.index')
                             ->with('success', "Bewertung für Club '{$originalClubName}' wurde abgelehnt/gelöscht.");
        } catch (\Exception $e) {
            report($e);
            return redirect()->route('admin.ratings.moderation.index')
                             ->with('error', 'Bewertung konnte nicht abgelehnt/gelöscht werden.');
        }
    }
}