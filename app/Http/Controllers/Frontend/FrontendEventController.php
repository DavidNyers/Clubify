<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Event; // <<< Event Model importieren
use App\Models\Genre; // <<< Genre Model für Filter importieren
use App\Models\Club;  // <<< Club Model für Filter importieren
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon; // <<< Carbon für Datum importieren
use Illuminate\Support\Facades\DB; // <<< DB importieren

class FrontendEventController extends Controller
{
    /**
     * Zeigt die Liste der öffentlichen Events an.
     */
    public function index(Request $request): View
    {
        // === Filter Parameter holen ===
        $genreFilter = $request->input('genre'); // Genre-Slug
        $cityFilter = $request->input('city');
        $dateFilter = $request->input('date');   // YYYY-MM-DD

        // === Sortier Parameter holen & validieren ===
        $sortValue = $request->input('sort', 'date-asc'); // Default: Datum aufsteigend
        $sortParts = explode('-', $sortValue);
        $sortBy = $sortParts[0] ?? 'date';
        $sortDirectionInput = strtolower($sortParts[1] ?? 'asc');
        $sortDirection = in_array($sortDirectionInput, ['asc', 'desc']) ? $sortDirectionInput : 'asc';

        // === Basis Query Builder ===
        $query = Event::query()
                ->where('events.is_active', true) // Nur aktive Events
                ->whereNull('events.cancelled_at') // Nur nicht abgesagte Events
                ->where('events.start_time', '>=', Carbon::today()); // Nur zukünftige/heutige Events

        // === Relationen laden ===
        // Lade nur notwendige Felder für die Liste
        $query->with([
            'club:id,name,slug,city', // Club-Infos
            'genres:id,name,slug'     // Genre-Infos
            // 'djs:id,name,slug' // DJs später hinzufügen?
        ]);

        // === FILTER ANWENDEN ===
        if ($genreFilter) {
            $query->whereHas('genres', function ($q) use ($genreFilter) {
                $q->where('slug', $genreFilter);
            });
        }
        if ($cityFilter) {
            $query->whereHas('club', function ($q) use ($cityFilter) {
                $q->where('city', $cityFilter);
            });
        }
        if ($dateFilter) {
            try {
                $targetDate = Carbon::parse($dateFilter)->startOfDay();
                $query->whereDate('start_time', $targetDate);
            } catch (\Exception $e) {}
        }

        // === SORTIERUNG ANWENDEN ===
        if ($sortBy === 'date') { 
            $query->orderBy('events.start_time', $sortDirection); 
        }
        elseif ($sortBy === 'name') { 
            $query->orderBy('events.name', $sortDirection); 
        }
        else { $query->orderBy('events.start_time', 'asc'); } 
        $query->orderBy('events.id', 'asc'); 

        // Fallback
        if ($sortBy !== 'start_time' && $sortBy !== 'name') {
             $query->orderBy('events.start_time', 'asc');
        }
        // Eindeutiger Tie-Breaker
        $query->orderBy('events.id', 'asc');


        // === Daten holen ===
        $events = $query->paginate(9)->appends($request->query());

        // === Daten für Filter-Dropdowns holen ===
        $availableGenres = Genre::whereHas('events', fn($q) => $q->where('is_active', true)->whereNull('cancelled_at')->where('start_time', '>=', Carbon::today()))
                                ->orderBy('name')->get(['name', 'slug']);
        // Geändert: Hole verfügbare Städte aus den Clubs der zukünftigen, aktiven Events
        $availableCities = Club::whereHas('events', fn($q) => $q->where('is_active', true)->whereNull('cancelled_at')->where('start_time', '>=', Carbon::today()))
                                ->whereNotNull('city')->where('city', '!=', '')
                                ->distinct()->orderBy('city')->pluck('city'); // pluck holt nur die Stadt-Spalte

        // Meta-Informationen
        $title = 'Events finden - Club & Party Kalender';
        $description = 'Finde die nächsten Partys, Konzerte und Club-Events in deiner Nähe.';

        // Daten an die View übergeben
        return view('frontend.events.index', compact(
            'events', 'title', 'description', 
            'sortValue','availableGenres', 
            'genreFilter','availableCities', 
            'cityFilter', 'dateFilter'
        ));
    }

    public function show(Event $event): View // Route Model Binding mit Slug
    {
        // Stelle sicher, dass nur aktive, nicht abgesagte Events angezeigt werden
        if (!$event->is_active || $event->isCancelled()) {
            abort(404);
        }

        // Lade notwendige Relationen für Details
        // - club: Infos zur Location
        // - genres: Zugehörige Musikgenres
        // - djs: Verknüpfte User mit DJ-Rolle
        // - djs.djProfile: Das zugehörige DJ-Profil für Stage Name, Bild etc. (wichtig!)
        $event->load([
            'club:id,name,slug,city,street_address,zip_code', // Lade benötigte Club-Felder
            'genres:id,name,slug',
            'djs' => function ($query) {
                // Lade den User und sein DJ Profil, falls vorhanden
                $query->with('djProfile:user_id,stage_name,slug,profile_image_path')
                    ->select('users.id', 'users.name'); // Wähle nur benötigte User-Felder
            }
        ]);

        // Meta-Informationen
        $title = $event->name . ' @ ' . $event->club->name; // Einfacher Titel
        $description = Str::limit(strip_tags($event->description ?? ''), 155);

        // TODO: Lade ggf. verwandte Events (z.B. andere Events im selben Club/Genre)

        return view('frontend.events.show', compact('event', 'title', 'description'));
    }
}