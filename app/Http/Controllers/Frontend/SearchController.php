<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Club;
use App\Models\Event;
use App\Models\DjProfile;
use Illuminate\View\View;
use Illuminate\Support\Carbon;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $searchTerm = $request->input('q');
        $results = [
            'clubs' => collect(),
            'events' => collect(),
            'djs' => collect(),
        ];
        $resultCount = 0;

        if (strlen(trim($searchTerm ?? '')) > 1) { // Nur suchen, wenn Suchbegriff vorhanden und lang genug
            $searchTermLike = "%{$searchTerm}%";

            // Clubs durchsuchen
            $clubs = Club::where('is_active', true)
                ->where(function ($query) use ($searchTermLike) {
                    $query->where('name', 'LIKE', $searchTermLike)
                          ->orWhere('city', 'LIKE', $searchTermLike)
                          ->orWhere('description', 'LIKE', $searchTermLike);
                })
                ->with('genres:name,slug') // Für Anzeige auf Karte
                ->take(10) // Limit pro Typ
                ->get();
            $results['clubs'] = $clubs;
            $resultCount += $clubs->count();

            // Events durchsuchen
            $events = Event::where('is_active', true)
                ->whereNull('cancelled_at')
                ->where('start_time', '>=', Carbon::today())
                ->where(function ($query) use ($searchTermLike) {
                    $query->where('name', 'LIKE', $searchTermLike)
                          ->orWhere('description', 'LIKE', $searchTermLike)
                          ->orWhereHas('club', fn($cq) => $cq->where('name', 'LIKE', $searchTermLike)
                                                            ->orWhere('city', 'LIKE', $searchTermLike)
                                      );
                    // Optional: Auch Event-Genres oder DJ-Namen im Event durchsuchen
                })
                ->with(['club:id,name,slug,city', 'genres:id,name,slug'])
                ->orderBy('start_time', 'asc')
                ->take(10)
                ->get();
            $results['events'] = $events;
            $resultCount += $events->count();

            // DJs durchsuchen
            $djs = DjProfile::where('is_visible', true)
                ->where(function ($query) use ($searchTermLike) {
                    $query->where('stage_name', 'LIKE', $searchTermLike)
                          ->orWhere('bio', 'LIKE', $searchTermLike)
                          ->orWhereHas('user', fn($uq) => $uq->where('name', 'LIKE', $searchTermLike));
                    // Optional: DJ-Genres durchsuchen (wenn Relation existiert)
                })
                ->with('user:id,name') // Für Fallback-Namen
                ->take(10)
                ->get();
            $results['djs'] = $djs;
            $resultCount += $djs->count();
        }

        $title = $searchTerm ? "Suchergebnisse für \"{$searchTerm}\"" : "Suche";
        $description = $searchTerm ? "Finde Clubs, Events und DJs passend zu deiner Suche: {$searchTerm}." : "Durchsuche Clubify nach den besten Nightlife-Angeboten.";

        return view('frontend.search.index', compact(
            'title', 'description', 'searchTerm', 'results', 'resultCount'
        ));
    }
}