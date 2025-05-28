<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Club;
use App\Models\DjProfile; // DJ Profil Model importieren
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        // === Daten für die Karte (Events in der Nähe - behalten wir bei) ===
        $mapEventsQuery = Event::query()
            ->where('is_active', true)
            ->whereNull('cancelled_at')
            ->where('start_time', '>=', Carbon::now())
            ->whereHas('club', fn($q) => $q->whereNotNull('latitude')->whereNotNull('longitude'))
            ->with(['club:id,slug,name,latitude,longitude', 'genres:id,name,slug']) // Genres für Event-Popups (optional)
            ->orderBy('start_time', 'asc')
            ->take(30); // Z.B. die nächsten 30 Events für die Karte

        $mapEvents = $mapEventsQuery->get();
        $mapData = $mapEvents->map(function ($event) {
            // Sicherstellen, dass $event->club existiert
            if (!$event->club) return null;

            $popupHtml = sprintf(
                '<div class="text-sm font-sans leading-normal max-w-[220px] space-y-1"><h4 class="font-semibold text-md mb-0.5 text-gray-900 dark:text-white">%s</h4><p class="text-xs text-gray-600 dark:text-gray-300">%s @ %s</p>%s<div class="mt-1.5 pt-1.5 border-t border-gray-200 dark:border-gray-600"><a href="%s" class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline text-xs">Event Details →</a></div></div>',
                e($event->name),
                $event->start_time ? $event->start_time->format('d.m H:i') : 'N/A',
                e($event->club->name ?? 'Unbekannt'),
                $event->genres->isNotEmpty() ? '<p class="text-xs text-indigo-500 dark:text-indigo-400 mt-0.5">'.e($event->genres->take(2)->pluck('name')->implode(', ')).'</p>' : '',
                route('events.show', $event)
            );
            return [
                'lat' => $event->club->latitude, 'lng' => $event->club->longitude,
                'title' => $event->name, 'popupContent' => $popupHtml, 'type' => 'event'
            ];
        })->filter()->values();


        // === Daten für "Nächste Events" Sektion ===
        $upcomingEvents = Event::query()
            ->where('is_active', true)
            ->whereNull('cancelled_at')
            ->where('start_time', '>=', Carbon::now())
            ->with(['club:id,name,slug,city', 'genres:id,name,slug']) // Club & Genres laden
            ->orderBy('start_time', 'asc')
            ->take(6) // Z.B. die nächsten 6 Events
            ->get();

        // === Daten für "Featured Clubs" Sektion ===
        $featuredClubs = Club::query()
            ->where('is_active', true)
            ->where('is_verified', true) // Nur verifizierte Clubs
            // Optional: Logik für "featured" (z.B. neues Feld, höchste Ratings, zufällig)
            // Hier: Zufällig einige mit hoher Bewertung (falls vorhanden)
            ->withCount(['ratings' => fn($q) => $q->where('is_approved', true)])
            ->withAvg(['ratings as average_rating' => fn($q) => $q->where('is_approved', true)], 'rating')
            ->orderByDesc('average_rating') // Höchste Bewertung zuerst
            ->orderByDesc('ratings_count') // Dann nach Anzahl Bewertungen
            ->inRandomOrder() // Für etwas Abwechslung
            ->take(4) // Z.B. 4 Clubs
            ->get();

        // === Daten für "Featured DJs" Sektion ===
        $featuredDjs = DjProfile::query()
            ->where('is_visible', true)
            ->where('is_verified', true)
            ->with('user:id,name') // User für Namen laden
            // Optional: Logik für "featured" (z.B. Gigs in naher Zukunft, viele Follower - braucht mehr Daten)
            ->inRandomOrder()
            ->take(4) // Z.B. 4 DJs
            ->get();

        // Meta-Infos
        $title = 'Clubify - Dein Nightlife Guide für Clubs, Events & DJs';
        $description = 'Entdecke die angesagtesten Partys, Locations und Künstler. Plane dein nächstes Wochenende mit Clubify!';

        return view('frontend.home', compact(
            'title', 'description', 'mapData',
            'upcomingEvents', 'featuredClubs', 'featuredDjs'
        ));
    }
}