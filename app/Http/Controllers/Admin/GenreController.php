<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request; // Standard Request für einfache Validierung hier
// Optional: Eigene Form Requests für komplexere Validierung
// use App\Http\Requests\Admin\StoreGenreRequest;
// use App\Http\Requests\Admin\UpdateGenreRequest;
use Illuminate\Support\Facades\View; // Sicherstellen, dass View importiert ist, falls nötig
use Illuminate\Support\Str; // Für Str::slug, falls Sluggable nicht greift oder manuell benötigt

class GenreController extends Controller
{
  /**
     * Display a listing of the resource.
     */
    public function index(Request $request) // <<< Request $request hinzufügen
    {
        // Suchbegriff aus dem Request holen
        $searchTerm = $request->input('search');

        // Query Builder starten
        $query = Genre::query();

        // Wenn ein Suchbegriff vorhanden ist, die Query anpassen
        if ($searchTerm) {
            // Suche im 'name'-Feld (case-insensitive durch LIKE)
            $query->where('name', 'LIKE', '%' . $searchTerm . '%');
              // Optional: Auch im Slug suchen?
              // $query->orWhere('slug', 'LIKE', '%' . $searchTerm . '%');
        }

        // Ergebnisse sortieren und paginieren
        $genres = $query->orderBy('name')->paginate(15);

        // Daten an die View übergeben (inkl. Suchbegriff für das Formular)
        return view('admin.genres.index', compact('genres', 'searchTerm')); // <<< searchTerm übergeben
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.genres.create');
    }

    /**
     * Store a newly created resource in storage.
     * Optional: Ersetze Request durch StoreGenreRequest
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:genres,name',
        ]);

        // Slug wird automatisch durch das Sluggable-Trait im Model generiert
        Genre::create($validated);

        return redirect()->route('admin.genres.index')
                         ->with('success', 'Genre erfolgreich erstellt.'); // Flash Message
    }

    /**
     * Display the specified resource.
     * (Wird oft nicht gebraucht im Admin CRUD, aber lassen wir es drin)
     */
    public function show(Genre $genre)
    {
         // Normalerweise zeigt man direkt die Edit-Seite oder die Index-Liste
        return view('admin.genres.edit', compact('genre')); // Zeige Edit-View als "Show"
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Genre $genre) // Nutzt Route Model Binding mit Slug
    {
        return view('admin.genres.edit', compact('genre'));
    }

    /**
     * Update the specified resource in storage.
     * Optional: Ersetze Request durch UpdateGenreRequest
     */
    public function update(Request $request, Genre $genre)
    {
        $validated = $request->validate([
            // Stelle sicher, dass der Name einzigartig ist, außer für das aktuelle Genre
            'name' => 'required|string|max:255|unique:genres,name,' . $genre->id,
        ]);

        // Slug wird standardmäßig nicht aktualisiert (siehe Model Konfiguration)
        $genre->update($validated);

        return redirect()->route('admin.genres.index')
                         ->with('success', 'Genre erfolgreich aktualisiert.'); // Flash Message
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Genre $genre)
    {
        // Hier könnte man noch prüfen, ob das Genre verwendet wird (z.B. von Events/Clubs)
        // try {
        $genre->delete();
        return redirect()->route('admin.genres.index')
                         ->with('success', 'Genre erfolgreich gelöscht.');
        // } catch (\Illuminate\Database\QueryException $e) {
        //     // Handle foreign key constraint violation, falls gewünscht
        //     return redirect()->route('admin.genres.index')
        //                      ->with('error', 'Genre konnte nicht gelöscht werden, da es noch verwendet wird.');
        // }
    }
}