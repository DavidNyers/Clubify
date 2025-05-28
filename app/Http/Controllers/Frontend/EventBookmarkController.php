<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Unbedingt Auth importieren
use Illuminate\View\View; // Für Typhinweis der index-Methode
use Illuminate\Support\Carbon; // Für Datumsmanipulation

class EventBookmarkController extends Controller
{
    // Der Konstruktor mit $this->middleware() WURDE ENTFERNT.
    // Die Middleware wird in routes/web.php angewendet.

    /**
     * Fügt ein Event zu den Bookmarks des Benutzers hinzu oder entfernt es.
     */
    public function toggle(Request $request, Event $event): \Illuminate\Http\JsonResponse // JSON Response als Typhinweis
    {
        $user = Auth::user();

        // Es ist gut, hier eine zusätzliche Prüfung zu haben, obwohl die Route geschützt sein sollte.
        if (!$user) {
            return response()->json(['message' => 'Nicht authentifiziert.'], 401);
        }
        if (!$event->exists) { // Prüft, ob das Event-Model tatsächlich aus der DB kommt
             return response()->json(['message' => 'Event nicht gefunden.'], 404);
        }

        $bookmarked = false; // Default
        $message = '';

        try {
            // `toggle` ist eine praktische Methode für BelongsToMany-Beziehungen,
            // um einen Eintrag hinzuzufügen, falls er nicht existiert, oder zu entfernen, falls er existiert.
            // Es gibt die IDs der angehängten/abgehängten Einträge zurück.
            $syncResult = $user->bookmarkedEvents()->toggle($event->id);

            if (count($syncResult['attached']) > 0) {
                $message = 'Event zu deiner Merkliste hinzugefügt!';
                $bookmarked = true;
            } elseif (count($syncResult['detached']) > 0) {
                $message = 'Event von deiner Merkliste entfernt.';
                $bookmarked = false;
            } else {
                // Sollte nicht passieren bei toggle, aber als Fallback
                $message = 'Status des Bookmarks unverändert.';
                // Ermittle den aktuellen Status neu, falls nötig
                $bookmarked = $user->bookmarkedEvents()->where('event_id', $event->id)->exists();
            }

            return response()->json([
                'message' => $message,
                'bookmarked' => $bookmarked,
                'count' => $user->bookmarkedEvents()->count() // Aktuelle Anzahl
            ]);

        } catch (\Exception $e) {
            logger()->error("Bookmark Toggle Fehler: " . $e->getMessage(), [
                'user_id' => $user->id,
                'event_id' => $event->id,
                'exception' => $e
            ]);
            return response()->json(['message' => 'Ein interner Fehler ist aufgetreten.'], 500);
        }
    }

    /**
     * Zeigt die Liste der vom Benutzer gemerkten Events.
     */
    public function index(): View
    {
        $user = Auth::user();
        if (!$user) { // Sollte nicht passieren wegen Middleware, aber zur Sicherheit
             return redirect()->route('login');
        }

        $bookmarkedEvents = $user->bookmarkedEvents()
                                ->where('events.is_active', true)
                                ->whereNull('events.cancelled_at')
                                ->where('events.start_time', '>=', Carbon::now()->subHours(12)) // Zeige Events bis 12h in Vergangenheit
                                ->orderBy('events.start_time', 'asc')
                                ->with(['club:id,name,slug,city', 'genres:id,name,slug'])
                                ->paginate(10);

        $title = "Meine gemerkten Events";
        return view('frontend.user.bookmarked-events', compact('bookmarkedEvents', 'title'));
    }
}