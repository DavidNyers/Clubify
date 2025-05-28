<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\DjProfile; // <<< DjProfile Model importieren
use App\Models\Genre;    // <<< Genre Model für Filter importieren
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;

class FrontendDjController extends Controller
{
    /**
     * Zeigt die Liste der öffentlichen DJ-Profile an.
     */
    public function index(Request $request): View
    {
        // === Filter Parameter holen ===
        $searchName = $request->input('search');
        $genreFilter = $request->input('genre'); // Genre-Slug

        // === Sortier Parameter holen & validieren ===
        $sortValue = $request->input('sort', 'name-asc'); // Default: name-asc
        $sortParts = explode('-', $sortValue);
        $sortBy = $sortParts[0] ?? 'name';
        $sortDirectionInput = strtolower($sortParts[1] ?? 'asc');
        $sortDirection = in_array($sortDirectionInput, ['asc', 'desc']) ? $sortDirectionInput : 'asc';

        // === Basis Query Builder ===
        $query = DjProfile::query()
                ->where('is_visible', true) // Nur sichtbare Profile
                // ->where('is_verified', true) // Optional: Nur verifizierte DJs anzeigen?
                ;

        // === Relationen laden ===
        // Lade User (für Name als Fallback) und Genres des DJs
        // Annahme: Genres werden über Events -> Genres geladen (indirekt)
        // oder wir fügen eine direkte Genre-Relation zum DjProfile/User hinzu
        $query->with(['user:id,name',
                      // TODO: Direkte DJ-Genre Relation hinzufügen oder über Events laden?
                      // 'user.djGigs.genres:id,name,slug' // Beispiel für indirektes Laden über Events (potenziell langsam)
                     ]);


        // === FILTER ANWENDEN ===
        if ($searchName) {
             $query->where(function($q) use ($searchName) {
                $q->where('stage_name', 'LIKE', '%' . $searchName . '%')
                  ->orWhereHas('user', function ($userQuery) use ($searchName) {
                      $userQuery->where('name', 'LIKE', '%' . $searchName . '%');
                  });
            });
        }
        // TODO: Genre Filter implementieren (erfordert Genre-Relation für DJs)
        // if ($genreFilter) {
        //     $query->whereHas('genres', function ($q) use ($genreFilter) { // Oder über User/Events?
        //         $q->where('slug', $genreFilter);
        //     });
        // }

        // === SORTIERUNG ANWENDEN ===
        // Sortierung nach Stage Name (oder User Name als Fallback)
        // Komplexer, da wir evtl. den User-Namen brauchen. Einfacher: Nur nach stage_name oder ID sortieren.
        if ($sortBy === 'name') {
             // ORDER BY COALESCE(stage_name, users.name) - Erfordert Join oder Raw OrderBy
             // Einfacher: Nach stage_name sortieren, NULLS LAST/FIRST
             $query->orderByRaw('stage_name IS NULL ASC, stage_name ' . $sortDirection); // NULLs zuerst bei ASC
        } else {
             // Fallback auf Erstellungsdatum oder ID
             $query->orderBy('created_at', 'desc'); // Neueste Profile zuerst
        }
        $query->orderBy('id', 'asc'); // Eindeutiger Tie-Breaker

        // === Daten holen ===
        $djProfiles = $query->paginate(12)->appends($request->query());

        // === Daten für Filter-Dropdowns holen ===
        // TODO: Lade verfügbare Genres für den Filter
        // $availableGenres = Genre::whereHas('djProfiles....')->get();

        // Meta-Informationen
        $title = 'DJs entdecken';
        $description = 'Finde lokale und überregionale DJs für dein nächstes Event.';

        // Daten an die View übergeben
        return view('frontend.djs.index', compact(
            'djProfiles',
            'title',
            'description',
            'sortValue',
            'searchName' // Übergabe für Suchfeld-Prefill
            // 'availableGenres',
            // 'genreFilter'
        ));
    }

    /**
     * Zeigt die Detailseite eines einzelnen DJ-Profils.
     */
     public function show(DjProfile $dj): View // Route Model Binding mit Slug
     {
         // Stelle sicher, dass nur sichtbare Profile angezeigt werden
         if (!$dj->is_visible) {
             abort(404);
         }

        // Lade notwendige Relationen für Details
        // user: Grunddaten
        // user.djGigs: Vergangene/zukünftige Events des Users
        // user.djGigs.club: Club des Events
         $dj->load([
            'user',
            'user.djGigs' => function ($query) {
                 // Lade kommende Gigs zuerst, dann vergangene, limitiert
                 $query->with('club:id,name,slug') // Club-Info laden
                       ->orderByRaw('start_time >= CURDATE() DESC, start_time DESC') // Zukünftige zuerst, dann nach Datum absteigend
                       ->limit(15); // Limit für Anzeige
             },
             // TODO: Lade Genres des DJs (direkt oder über Events)
         ]);

         $title = $dj->displayName . ' - DJ Profil'; // Nutzt Accessor
         $description = Str::limit(strip_tags($dj->bio ?? 'DJ Profil für '.$dj->displayName), 155);

         return view('frontend.djs.show', compact('dj', 'title', 'description'));
     }
}