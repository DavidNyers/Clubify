<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Club;
use App\Models\Event;
use App\Models\Genre;
use Illuminate\Support\Facades\Route; // Für route() Helper
use Illuminate\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;   // Für DB::raw
use Illuminate\Support\Str;       // Für e() oder Str::limit
use Countries; // Für die Länderliste

class MapController extends Controller
{
    public function index(Request $request): View
    {
        // === 1. Filter Parameter holen ===
        $searchTerm = $request->input('search_term');
        $filterType = $request->input('type', 'all'); // 'all', 'clubs', 'events'
        $filterCountry = $request->input('country');
        $filterCity = $request->input('city');
        $filterDateOption = $request->input('date_option', 'any');
        $filterCustomDateFrom = $request->input('custom_date_from');
        $filterCustomDateTo = $request->input('custom_date_to');
        $filterOpenToday = $request->boolean('open_today');
        $filterGenresInput = $request->input('genres', []);
        $filterGenres = is_array($filterGenresInput) ? array_filter($filterGenresInput) : [];

        // === 2. Basis Query Builder für Clubs und Events initialisieren ===
        $clubQueryBase = Club::query()
            ->where('clubs.is_active', true)
            ->whereNotNull('clubs.latitude')
            ->whereNotNull('clubs.longitude');

        $eventQueryBase = Event::query()
            ->where('events.is_active', true)
            ->whereNull('events.cancelled_at')
            ->whereHas('club', function ($query) { // Nur Events mit Club-Koordinaten
                $query->whereNotNull('latitude')->whereNotNull('longitude');
            })
            ->with(['club:id,name,slug,latitude,longitude,city,opening_hours,country', 'genres:id,name,slug']);

        // === 3. Klone Basis-Queries für die Anwendung der Filter ===
        // Ein Klon für die Hauptdatenauswahl, ein Klon für die Filtergenerierung
        $clubQueryForData = clone $clubQueryBase;
        $eventQueryForData = clone $eventQueryBase;

        $clubQueryForFilters = clone $clubQueryBase;
        $eventQueryForFilters = clone $eventQueryBase;


        // === 4. Filter auf die Daten-Queries anwenden ===

        // Allgemeine Suche
        if ($searchTerm) {
            $searchTermLike = "%{$searchTerm}%";
            if ($filterType === 'clubs' || $filterType === 'all') {
                $clubQueryForData->where(function ($q) use ($searchTermLike) {
                    $q->where('clubs.name', 'LIKE', $searchTermLike)
                      ->orWhere('clubs.city', 'LIKE', $searchTermLike)
                      ->orWhere('clubs.description', 'LIKE', $searchTermLike);
                });
            }
            if ($filterType === 'events' || $filterType === 'all') {
                $eventQueryForData->where(function ($q) use ($searchTermLike) {
                    $q->where('events.name', 'LIKE', $searchTermLike)
                      ->orWhere('events.description', 'LIKE', $searchTermLike)
                      ->orWhereHas('club', fn($cq) => $cq->where('name', 'LIKE', $searchTermLike)->orWhere('city', 'LIKE', $searchTermLike));
                });
            }
        }

        // Länderfilter
        if ($filterCountry) {
            if ($filterType === 'clubs' || $filterType === 'all') { $clubQueryForData->where('clubs.country', $filterCountry); }
            if ($filterType === 'events' || $filterType === 'all') { $eventQueryForData->whereHas('club', fn($cq) => $cq->where('country', $filterCountry)); }
        }

        // Städtefilter
        if ($filterCity) {
            if ($filterType === 'clubs' || $filterType === 'all') { $clubQueryForData->where('clubs.city', $filterCity); }
            if ($filterType === 'events' || $filterType === 'all') { $eventQueryForData->whereHas('club', fn($cq) => $cq->where('city', $filterCity)); }
        }

        // Datumsfilter (Events)
        if ($filterType === 'events' || $filterType === 'all') {
            if ($filterDateOption === 'today') { $eventQueryForData->whereDate('events.start_time', Carbon::today()); }
            elseif ($filterDateOption === 'tomorrow') { $eventQueryForData->whereDate('events.start_time', Carbon::tomorrow()); }
            elseif ($filterDateOption === 'this_weekend') { $eventQueryForData->whereBetween('events.start_time', [Carbon::now()->startOfWeek(Carbon::FRIDAY)->startOfDay(), Carbon::now()->endOfWeek(Carbon::SUNDAY)->endOfDay()]); }
            elseif ($filterDateOption === 'custom' && $filterCustomDateFrom) {
                try {
                    $from = Carbon::parse($filterCustomDateFrom)->startOfDay();
                    $eventQueryForData->where('events.start_time', '>=', $from);
                    if ($filterCustomDateTo) {
                        $to = Carbon::parse($filterCustomDateTo)->endOfDay();
                        $eventQueryForData->where('events.start_time', '<=', $to);
                    }
                } catch (\Exception $e) { /* Ignoriere ungültiges Datum */ }
            } else {
                 $eventQueryForData->where('events.start_time', '>=', Carbon::today()->startOfDay());
            }
        }

        // "Heute geöffnet" (Clubs)
        $todayKey = date('D');
        if (($filterType === 'clubs' || $filterType === 'all') && $filterOpenToday) {
            $clubQueryForData->where(function($q) use ($todayKey) {
                $q->whereRaw("JSON_EXTRACT(clubs.opening_hours, '$.\"{$todayKey}\"') IS NOT NULL")
                  ->whereRaw("JSON_EXTRACT(clubs.opening_hours, '$.\"{$todayKey}\"') != '\"closed\"'");
            });
        }

        // Genre Filter
        if (!empty($filterGenres)) {
            if ($filterType === 'clubs' || $filterType === 'all') {
                $clubQueryForData->whereHas('genres', fn($q) => $q->whereIn('genres.slug', $filterGenres));
            }
            if ($filterType === 'events' || $filterType === 'all') {
                $eventQueryForData->whereHas('genres', fn($q) => $q->whereIn('genres.slug', $filterGenres));
            }
        }

        // === 5. Datensätze für Karte aufbereiten ===
        $mapDataItems = collect();

        if ($filterType === 'clubs' || $filterType === 'all') {
            $clubs = $clubQueryForData->select('clubs.id', 'clubs.name', 'clubs.slug', 'clubs.latitude', 'clubs.longitude', 'clubs.city', 'clubs.opening_hours')->distinct()->get();
            $clubMapData = $clubs->map(function ($club) use ($todayKey) {
                $clubNameEscaped = e($club->name);
                $clubCityEscaped = e($club->city ?? 'Unbekannt');
                $clubShowUrl = route('clubs.show', $club);
                $openingTimeToday = '<span class="italic text-gray-400">n.a.</span>';
                if (isset($club->opening_hours[$todayKey])) {
                    if ($club->opening_hours[$todayKey] === 'closed') { $openingTimeToday = 'Geschlossen'; }
                    elseif (!empty($club->opening_hours[$todayKey])) { $openingTimeToday = e(str_replace('-', ' - ', $club->opening_hours[$todayKey])) . ' Uhr'; }
                }
                $formatString = '<div class="text-sm font-sans leading-normal max-w-[220px] space-y-1"><h4 class="font-semibold text-md mb-0.5 text-gray-900 dark:text-white">%s</h4><p class="text-xs text-gray-600 dark:text-gray-300">%s</p><p class="text-xs text-gray-600 dark:text-gray-300">Heute: %s</p><div class="mt-1.5 pt-1.5 border-t border-gray-200 dark:border-gray-600"><a href="%s" class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline text-xs">Details →</a></div></div>';
                $popupHtmlClub = sprintf($formatString, $clubNameEscaped, $clubCityEscaped, $openingTimeToday, $clubShowUrl);
                return ['type' => 'club', 'lat' => $club->latitude, 'lng' => $club->longitude, 'title' => $club->name, 'popupContent' => $popupHtmlClub];
            });
            $mapDataItems = $mapDataItems->concat($clubMapData);
        }

        if ($filterType === 'events' || $filterType === 'all') {
            $events = $eventQueryForData->select('events.id','events.name', 'events.slug', 'events.start_time', 'events.club_id')->distinct()->take(150)->get();
            $eventMapData = $events->map(function ($event) {
                if (!$event->club) return null;
                $eventNameEscaped = e($event->name);
                $eventTimeFormatted = $event->start_time ? $event->start_time->format('d.m H:i') : 'N/A';
                $eventClubNameEscaped = e($event->club->name ?? 'Unbekannte Location');
                $eventShowUrl = route('events.show', $event);
                $formatString = '<div class="text-sm font-sans leading-normal max-w-[220px] space-y-1"><h4 class="font-semibold text-md mb-0.5 text-gray-900 dark:text-white">%s</h4><p class="text-xs text-gray-600 dark:text-gray-300">%s @ %s</p><div class="mt-1.5 pt-1.5 border-t border-gray-200 dark:border-gray-600"><a href="%s" class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline text-xs">Event Details →</a></div></div>';
                $popupHtmlEvent = sprintf($formatString, $eventNameEscaped, $eventTimeFormatted, $eventClubNameEscaped, $eventShowUrl);
                return ['type' => 'event', 'lat' => $event->club->latitude, 'lng' => $event->club->longitude, 'title' => $event->name, 'popupContent' => $popupHtmlEvent];
            })->filter();
            $mapDataItems = $mapDataItems->concat($eventMapData);
        }
        $mapData = $mapDataItems->values();


        // === 6. Daten für Filter-Dropdowns holen (basierend auf POTENZIELLEN Ergebnissen, nicht den bereits gefilterten $mapData) ===
        $locale = app()->getLocale();
        $allCountryNames = Countries::getList($locale);

        // Länder: Basierend auf Clubs, die entweder selbst aktiv sind oder aktive zukünftige Events haben
        $countryCodesQuery = DB::table('clubs')
            ->select('clubs.country')
            ->where('clubs.is_active', true)
            ->whereNotNull('clubs.country')->where('clubs.country', '!=', '');
        if ($filterType === 'events' || $filterType === 'all' || $filterType === 'clubs') { // Immer Clubs berücksichtigen für Länder, Events optional
             $countryCodesQuery->orWhereExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('events')
                      ->join('clubs as event_clubs_for_country', 'events.club_id', '=', 'event_clubs_for_country.id')
                      ->whereColumn('event_clubs_for_country.country', 'clubs.country')
                      ->where('events.is_active', true)
                      ->whereNull('events.cancelled_at')
                      ->where('events.start_time', '>=', Carbon::today());
            });
        }
        $countryCodes = $countryCodesQuery->distinct()->orderBy('clubs.country')->pluck('clubs.country');
        $countriesForFilter = $countryCodes->mapWithKeys(fn($code) => [strtoupper($code) => $allCountryNames[strtoupper($code)] ?? $code])->sort();


        // Städte: Basierend auf Clubs (ggf. gefiltert nach Land), die entweder selbst aktiv sind oder aktive zukünftige Events haben
        $cityQueryForFilter = clone $clubQueryForFilters; // Starte mit Basis-Club-Query für Filter
        if ($filterCountry) { $cityQueryForFilter->where('clubs.country', $filterCountry); }
        // Nur Städte von Clubs, die entweder selbst dem Type-Filter entsprechen ODER Events haben, die dem Type-Filter entsprechen
        $cityQueryForFilter->where(function ($q) use ($filterType, $filterCountry) {
            if ($filterType === 'clubs' || $filterType === 'all') {
                $q->orWhere(fn($sq) => $sq); // Club ist per se gültig
            }
            if ($filterType === 'events' || $filterType === 'all') {
                $q->orWhereHas('events', function($eq) use ($filterCountry) {
                    $eq->where('is_active', true)->whereNull('cancelled_at')->where('start_time', '>=', Carbon::today());
                    // Kein extra Länderfilter hier nötig, da $cityQueryForFilter schon nach Land gefiltert ist (falls $filterCountry gesetzt)
                });
            }
        });
        $availableCities = $cityQueryForFilter->whereNotNull('clubs.city')->where('clubs.city', '!=', '')
                                           ->distinct('clubs.city')->orderBy('clubs.city')->pluck('clubs.city');


        // Genres: Basierend auf Clubs/Events, die den aktuellen Hauptfiltern (Typ, Standort etc.) entsprechen
        $genreQueryForFilter = Genre::query();
        $genreQueryForFilter->where(function($q) use ($clubQueryForData, $eventQueryForData, $filterType) {
            // Klonen, um die Queries für die Kartendaten nicht zu beeinflussen
            $subClubQuery = clone $clubQueryForData;
            $subEventQuery = clone $eventQueryForData;

            if ($filterType === 'clubs' || $filterType === 'all') {
                // Hole IDs von Clubs, die den Hauptfiltern entsprechen
                $clubIds = $subClubQuery->select('clubs.id')->pluck('clubs.id');
                if($clubIds->isNotEmpty()){
                    $q->whereHas('clubs', fn($cq) => $cq->whereIn('clubs.id', $clubIds));
                }
            }
            if ($filterType === 'events' || $filterType === 'all') {
                // Hole IDs von Events, die den Hauptfiltern entsprechen
                $eventIds = $subEventQuery->select('events.id')->pluck('events.id');
                if($eventIds->isNotEmpty()){
                    // Wenn $filterType 'all' war, muss es OR sein
                    $method = ($filterType === 'all' && ($q->getQuery()->wheres && count(array_filter($q->getQuery()->wheres, fn($w) => $w['type'] === 'Has')) > 0)) ? 'orWhereHas' : 'whereHas';
                    $q->{$method}('events', fn($eq) => $eq->whereIn('events.id', $eventIds));
                }
            }
             // Wenn keine der obigen Bedingungen zutrifft (z.B. $filterType ist weder 'clubs' noch 'events'),
             // aber wir dennoch eine valide Query für Genres brauchen, um Fehler zu vermeiden.
             if (empty($q->getQuery()->wheres)) { // Wenn noch keine whereHas Bedingung gesetzt wurde
                 $q->whereRaw('1 = 0'); // Keine Genres finden, wenn keine Clubs/Events dem Filter entsprechen
             }
        });
        $availableGenres = $genreQueryForFilter->distinct()->orderBy('name')->get(['name', 'slug']);


        // === Meta-Informationen ===
        $title = 'Interaktive Karte - Clubs & Events';
        $description = 'Entdecke Clubs und Events auf unserer interaktiven Karte.';

        // === Daten an die View übergeben ===
        return view('frontend.map.index', compact(
            'mapData', 'title', 'description',
            'searchTerm', 'filterType', 'countriesForFilter', 'filterCountry',
            'availableCities', 'filterCity',
            'filterDateOption', 'filterCustomDateFrom', 'filterCustomDateTo', 'filterOpenToday',
            'availableGenres', 'filterGenres'
        ));
    }
}