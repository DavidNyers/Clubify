<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Genre;
use App\Models\User;
use App\Models\ClubImage; // Model für Galeriebilder
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager; // Intervention Image v3
use Intervention\Image\Drivers\Gd\Driver as GdDriver; // GD Treiber
// use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver; // Falls Imagick genutzt wird
use Countries; // Für die Länderliste

class ClubController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $searchName = $request->input('search_name');
        $searchCity = $request->input('search_city');

        $query = Club::with('owner')->orderBy('name');

        if ($searchName) {
            $query->where('name', 'LIKE', '%' . $searchName . '%');
        }
        if ($searchCity) {
            $query->where('city', 'LIKE', '%' . $searchCity . '%');
        }

        $clubs = $query->paginate(15);
        return view('admin.clubs.index', compact('clubs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $genres = Genre::orderBy('name')->get();
        $clubOwners = User::role('ClubOwner')->orderBy('name')->get();
        $countries = collect(Countries::getList(app()->getLocale()))->sort()->all();

        // Für die strukturierte Formularanzeige der JSON-Felder (leere Werte für Create)
        $openingHoursStructured = $this->decodeOpeningHours(null);
        $accessibilityStructured = $this->decodeAccessibility(null);

        return view('admin.clubs.create', compact('genres', 'clubOwners', 'countries', 'openingHoursStructured', 'accessibilityStructured'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate($this->validationRules());

        $processedJson = $this->processJsonFields($request); // JSON-Felder verarbeiten
        $validatedData['opening_hours'] = $processedJson['opening_hours'];
        $validatedData['accessibility_features'] = $processedJson['accessibility_features'];

        $validatedData['is_active'] = $request->boolean('is_active', true);
        $validatedData['is_verified'] = $request->boolean('is_verified', false);

        DB::beginTransaction();
        try {
            $club = Club::create($validatedData);

            // Galeriebilder verarbeiten und speichern
            if ($request->hasFile('gallery_images')) {
                $imageManager = new ImageManager(new GdDriver());
                foreach ($request->file('gallery_images') as $imageFile) {
                    if ($imageFile->isValid()) {
                        $originalName = $imageFile->getClientOriginalName();
                        $filename = time() . '_' . uniqid() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.webp';
                        $directory = 'club-gallery/' . $club->id;
                        $path = $directory . '/' . $filename;

                        $img = $imageManager->read($imageFile->getRealPath());
                        $img->resize(1200, null, function ($constraint) {
                            $constraint->aspectRatio(); $constraint->upsize();
                        });
                        $encodedImage = $img->toWebp(80);
                        Storage::disk('public')->put($path, (string) $encodedImage);

                        $club->galleryImages()->create([
                            'path' => $path,
                            'original_name' => $originalName,
                            'mime_type' => 'image/webp', // Da wir es zu WebP konvertieren
                            'size' => strlen((string) $encodedImage),
                        ]);
                    }
                }
            }

            $club->genres()->sync($request->input('genres', []));
            DB::commit();
            return redirect()->route('admin.clubs.index')->with('success', 'Club erfolgreich erstellt.');

        } catch (\Exception $e) {
            DB::rollBack();
             if (isset($club) && $club->id && Storage::disk('public')->exists('club-gallery/' . $club->id)) {
                 Storage::disk('public')->deleteDirectory('club-gallery/' . $club->id);
             }
            report($e);
            return back()->with('error', 'Fehler beim Erstellen des Clubs: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource. (Usually redirects to edit for admin)
     */
    public function show(Club $club)
    {
        return redirect()->route('admin.clubs.edit', $club);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Club $club)
    {
        $genres = Genre::orderBy('name')->get(['id', 'name']);
        $clubOwners = User::role('ClubOwner')->orderBy('name')->get(['id', 'name', 'email']);
        $countries = collect(Countries::getList(app()->getLocale()))->sort()->all();
        $club->load(['genres:id', 'galleryImages']); // Galeriebilder laden

        $openingHoursStructured = $this->decodeOpeningHours($club->opening_hours);
        $accessibilityStructured = $this->decodeAccessibility($club->accessibility_features);

        return view('admin.clubs.edit', compact(
            'club', 'genres', 'clubOwners', 'countries',
            'openingHoursStructured', 'accessibilityStructured'
            // $club->galleryImages ist jetzt verfügbar in der View
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Club $club)
    {
        $validatedData = $request->validate($this->validationRules($club->id));

        $processedJson = $this->processJsonFields($request);
        $validatedData['opening_hours'] = $processedJson['opening_hours'];
        $validatedData['accessibility_features'] = $processedJson['accessibility_features'];

        $validatedData['is_active'] = $request->boolean('is_active');
        $validatedData['is_verified'] = $request->boolean('is_verified');

        DB::beginTransaction();
        try {
            $club->update($validatedData);

            // Neue Galeriebilder hinzufügen
            if ($request->hasFile('gallery_images')) {
                $imageManager = new ImageManager(new GdDriver());
                foreach ($request->file('gallery_images') as $imageFile) {
                    if ($imageFile->isValid()) {
                        $originalName = $imageFile->getClientOriginalName();
                        $filename = time() . '_' . uniqid() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.webp';
                        $directory = 'club-gallery/' . $club->id;
                        $path = $directory . '/' . $filename;

                        $img = $imageManager->read($imageFile->getRealPath());
                        $img->resize(1200, null, fn($c) => $c->aspectRatio()->upsize());
                        $encodedImage = $img->toWebp(80);
                        Storage::disk('public')->put($path, (string) $encodedImage);

                        $club->galleryImages()->create([
                            'path' => $path,
                            'original_name' => $originalName,
                            'mime_type' => 'image/webp',
                            'size' => strlen((string) $encodedImage),
                        ]);
                    }
                }
            }

            // Markierte Bilder löschen
            if ($request->has('delete_images')) {
                foreach ($request->input('delete_images') as $imageIdToDelete) {
                    $imageRecord = ClubImage::find($imageIdToDelete);
                    if ($imageRecord && $imageRecord->club_id == $club->id) { // Sicherheit
                        if (Storage::disk('public')->exists($imageRecord->path)) {
                            Storage::disk('public')->delete($imageRecord->path);
                            // Optional: Thumbnails löschen, falls vorhanden und andere Pfade gespeichert
                        }
                        $imageRecord->delete();
                    }
                }
            }

            $club->genres()->sync($request->input('genres', []));
            DB::commit();
            return redirect()->route('admin.clubs.index')->with('success', 'Club erfolgreich aktualisiert.');

        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return back()->with('error', 'Fehler beim Aktualisieren des Clubs: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Club $club)
    {
        DB::beginTransaction();
        try {
            // Lösche alle Galeriebilder vom Storage und aus der DB
            foreach ($club->galleryImages as $image) {
                if (Storage::disk('public')->exists($image->path)) {
                    Storage::disk('public')->delete($image->path);
                }
                // Kein explizites $image->delete() hier, da cascadeOnDelete in der Relation helfen könnte
                // oder wir löschen das gesamte Verzeichnis am Ende.
            }
            // Lösche das gesamte Verzeichnis für die Galeriebilder dieses Clubs
            if (Storage::disk('public')->exists('club-gallery/' . $club->id)) {
                Storage::disk('public')->deleteDirectory('club-gallery/' . $club->id);
            }
            // Lösche die ClubImage-Einträge (wird auch durch cascadeOnDelete in der DB-Migration erledigt)
            $club->galleryImages()->delete();

            // Lösche den Club selbst
            $club->delete();

            DB::commit();
            return redirect()->route('admin.clubs.index')->with('success', 'Club und zugehörige Bilder erfolgreich gelöscht.');
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return redirect()->route('admin.clubs.index')->with('error', 'Club konnte nicht gelöscht werden.');
        }
    }

    /**
     * Validierungsregeln für Club-Formular.
     */
    protected function validationRules($ignoreId = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'street_address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'zip_code' => ['nullable', 'string', 'max:10'],
            'country' => ['required', 'string', 'size:2', Rule::in(array_keys(Countries::getList(app()->getLocale())))],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'website' => ['nullable', 'url', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            // 'opening_hours_structured' etc. sind indirekt durch 'opening_hours' (string) abgedeckt
            'price_level' => ['nullable', Rule::in(['$', '$$', '$$$'])],
            'is_active' => ['nullable', 'boolean'],
            'is_verified' => ['nullable', 'boolean'],
            // 'accessibility_structured' etc. sind indirekt durch 'accessibility_features' (string) abgedeckt
            'owner_id' => ['nullable', 'exists:users,id'],
            'genres' => ['nullable', 'array'],
            'genres.*' => ['exists:genres,id'],
            'gallery_images'   => ['nullable', 'array', 'max:10'], // Max 10 Bilder auf einmal
            'gallery_images.*' => ['image', 'mimes:jpeg,png,jpg,webp,gif,svg', 'max:5120'], // Max 5MB pro Bild
            'delete_images' => ['nullable', 'array'], // Array von IDs der zu löschenden Bilder
            'delete_images.*' => ['integer', 'exists:club_images,id'], // Jede ID muss existieren
        ];
    }

    /**
    * Verarbeitet strukturierte JSON-Eingaben (Öffnungszeiten, Barrierefreiheit).
    */
    protected function processJsonFields(Request $request): array
    {
        $output = [];
        // Öffnungszeiten
        $structuredHours = $request->input('opening_hours_structured', []);
        $openingHoursJson = [];
        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        foreach($days as $day) {
            if (!empty($structuredHours[$day]['closed'])) {
                $openingHoursJson[$day] = 'closed';
            } elseif (!empty($structuredHours[$day]['start']) && !empty($structuredHours[$day]['end'])) {
                if (preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $structuredHours[$day]['start']) && preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $structuredHours[$day]['end'])) {
                    $openingHoursJson[$day] = $structuredHours[$day]['start'] . '-' . $structuredHours[$day]['end'];
                }
            }
        }
        $output['opening_hours'] = !empty($openingHoursJson) ? $openingHoursJson : null;

        // Barrierefreiheit
        $structuredAccess = $request->input('accessibility_structured', []);
        $accessibilityJson = [];
        $accessibilityJson['wheelchair_accessible'] = !empty($structuredAccess['wheelchair_accessible']);
        $accessibilityJson['accessible_restrooms'] = !empty($structuredAccess['accessible_restrooms']);
        $accessibilityJson['low_counter'] = !empty($structuredAccess['low_counter']);
        if (!empty($structuredAccess['details'])) { $accessibilityJson['details'] = trim($structuredAccess['details']); }
        $hasAccessData = $accessibilityJson['wheelchair_accessible'] || $accessibilityJson['accessible_restrooms'] || $accessibilityJson['low_counter'] || !empty($accessibilityJson['details']);
        $output['accessibility_features'] = $hasAccessData ? $accessibilityJson : null;

        // json_error wird hier nicht mehr benötigt, da die Daten direkt im Haupt-ValidatedData Array verarbeitet werden.
        return $output;
    }

    /**
     * Dekodiert JSON-Öffnungszeiten für das Formular.
     */
    protected function decodeOpeningHours(?array $jsonData): array
    {
        $structured = [];
        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        foreach($days as $day) {
            $defaultEntry = ['start' => '', 'end' => '', 'closed' => false];
            if (isset($jsonData[$day])) {
                 if ($jsonData[$day] === 'closed') {
                     $structured[$day] = ['start' => '', 'end' => '', 'closed' => true];
                 } else {
                     $parts = explode('-', $jsonData[$day]);
                     $structured[$day] = ['start' => $parts[0] ?? '', 'end' => $parts[1] ?? '', 'closed' => false];
                 }
            } else {
                 $structured[$day] = $defaultEntry;
            }
        }
        return $structured;
    }

     /**
     * Dekodiert JSON-Barrierefreiheit für das Formular.
     */
    protected function decodeAccessibility(?array $jsonData): array
    {
        return [
             'wheelchair_accessible' => $jsonData['wheelchair_accessible'] ?? false,
             'accessible_restrooms' => $jsonData['accessible_restrooms'] ?? false,
             'low_counter' => $jsonData['low_counter'] ?? false,
             'details' => $jsonData['details'] ?? '',
        ];
    }
}