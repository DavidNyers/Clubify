<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Club;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FrontendClubController extends Controller
{
    /**
     * Zeigt die Liste der öffentlichen Clubs an.
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        // === Filter Parameter holen ===
        $cityFilter = $request->input('city');
        // $genreFilter = $request->input('genre'); // TODO

        // === Sortier Parameter holen & validieren ===
        $sortValue = $request->input('sort', 'name-asc');
        $sortParts = explode('-', $sortValue);
        $sortBy = $sortParts[0] ?? 'name';
        $sortDirectionInput = strtolower($sortParts[1] ?? 'asc');
        $sortDirection = in_array($sortDirectionInput, ['asc', 'desc']) ? $sortDirectionInput : 'asc';

        // === Basis Query Builder ===
        $query = Club::query()->where('clubs.is_active', true);

        // === Relationen & Aggregate laden ===
        $query->with('genres:id,name,slug');
        if (method_exists(Club::class, 'ratings')) {
            $query->withAvg(['ratings as average_rating' => fn($q) => $q->where('is_approved', true)], 'rating')
                  ->withCount(['ratings' => fn($q) => $q->where('is_approved', true)]);
        } else {
             $query->select('clubs.*')
                   ->selectRaw('CAST(NULL AS DECIMAL(8,2)) as average_rating')
                   ->selectRaw('0 as ratings_count');
        }

        // === FILTER ANWENDEN ===
        if ($cityFilter) {
            $query->where('clubs.city', $cityFilter);
        }

        // === SORTIERUNG ANWENDEN ===
        $todayKey = date('D');
        $query->addSelect(DB::raw("
            CASE
                WHEN JSON_EXTRACT(opening_hours, '$.\"{$todayKey}\"') IS NULL THEN 2
                WHEN JSON_EXTRACT(opening_hours, '$.\"{$todayKey}\"') = '\"closed\"' THEN 1
                ELSE 0
            END as is_open_today_sort
        "));

        if ($sortBy === 'rating' && Schema::hasColumn('clubs', 'average_rating')) {
             $nullsOrder = ($sortDirection === 'asc' ? 'DESC' : 'ASC');
             $query->orderByRaw("average_rating IS NULL {$nullsOrder}, average_rating {$sortDirection}, clubs.id ASC");
        } elseif ($sortBy === 'open_today') {
             $query->orderBy('is_open_today_sort', $sortDirection)->orderBy('clubs.id', 'asc');
        } elseif ($sortBy === 'name') {
             $query->orderBy('clubs.name', $sortDirection)->orderBy('clubs.id', 'asc');
        } else {
             $query->orderBy('clubs.name', 'asc')->orderBy('clubs.id', 'asc');
        }

        // === Daten holen (paginiert) ===
        $clubs = $query->paginate(12)->appends($request->query());

        // === Daten für Filter-Dropdowns holen ===
        $availableCities = Club::where('is_active', true)
                                ->whereNotNull('city')
                                ->where('city', '!=', '')
                                ->distinct()
                                ->orderBy('city')
                                ->pluck('city');

        $title = 'Clubs finden - Dein Nightlife Guide';
        $description = 'Entdecke die besten Clubs...';

        // Daten an die View übergeben (OHNE mapData)
        return view('frontend.clubs.index', compact(
            'clubs',
            'title',
            'description',
            'sortValue',
            'availableCities',
            'cityFilter'
            // 'availableGenres',
            // 'genreFilter'
        ));
    }

    /**
     * Zeigt die Detailseite eines einzelnen Clubs.
     * (Wird im nächsten Schritt implementiert)
     */
   public function show(Club $club): View
    {
        if (!$club->is_active) { abort(404); }

        $club->load([
            'genres',
            'upcomingEvents',
            'galleryImages',
            // Lade freigegebene Ratings und deren User direkt mit
            'ratings' => function($query) {
                $query->where('is_approved', true)->with('user:id,name')->latest();
            }
        ]);

        // Berechne Durchschnitt und Anzahl aus der geladenen, gefilterten Relation
        $approvedRatingsCollection = $club->ratings; // Ist jetzt schon die gefilterte Collection
        $averageRating = $approvedRatingsCollection->avg('rating');
        $ratingCount = $approvedRatingsCollection->count();

        // Paginierte Reviews (Kommentare) aus der bereits gefilterten Collection
        // Hier ist eine manuelle Paginierung nötig, wenn wir nicht erneut die DB abfragen wollen.
        // Einfacher ist es, die DB erneut abzufragen, wenn die Anzahl der Reviews potenziell groß ist.
        $reviews = $club->ratings() // Starte neue Query für Paginierung
                    ->whereNotNull('comment')
                    ->where('is_approved', true)
                    ->with('user:id,name')
                    ->latest()
                    ->paginate(5, ['*'], 'reviewsPage');

        $title = $club->name . ' - Club Details & Bewertungen';
        $description = Str::limit(strip_tags($club->description ?? ''), 155);

        return view('frontend.clubs.show', compact('club', 'title', 'description', 'averageRating', 'ratingCount', 'reviews'));
    }

     // --- Helper Relation im Club Model hinzufügen ---
     // Füge dies zu app/Models/Club.php hinzu:
     /*
     use App\Models\Event;
     use Illuminate\Database\Eloquent\Relations\HasMany;
     use Illuminate\Support\Carbon;

     public function upcomingEvents(): HasMany
     {
         return $this->hasMany(Event::class)
                     ->where('is_active', true)
                     ->whereNull('cancelled_at')
                     ->where('start_time', '>=', Carbon::now())
                     ->orderBy('start_time', 'asc');
     }
     */

}