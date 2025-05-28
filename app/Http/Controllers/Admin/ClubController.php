<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Genre; // Benötigt für Formulare
use App\Models\User; // Benötigt für Owner-Auswahl
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB; // Für Transaktionen
use Countries;

class ClubController extends Controller
{
    public function index(Request $request)
    {
        $searchName = $request->input('search_name');
        $searchCity = $request->input('search_city');

        $query = Club::with('owner'); // Lade Owner-Relation direkt mit (Eager Loading)

        if ($searchName) {
            $query->where('name', 'LIKE', '%' . $searchName . '%');
        }
        if ($searchCity) {
            $query->where('city', 'LIKE', '%' . $searchCity . '%');
        }

        $clubs = $query->orderBy('name')->paginate(15);

        return view('admin.clubs.index', compact('clubs')); // Suchbegriffe sind im Request
    }

     public function show(Club $club)
    {
        return redirect()->route('admin.clubs.edit', $club);
    }

    public function destroy(Club $club)
    {
         // Vorsicht: Was passiert mit Events, die diesem Club zugeordnet sind?
         // Ggf. vorher prüfen oder Verknüpfung in Events auf null setzen lassen (abhängig von DB Constraints)
        try {
            $club->delete();
            return redirect()->route('admin.clubs.index')
                             ->with('success', 'Club erfolgreich gelöscht.');
        } catch (\Exception $e) {
            report($e);
            return redirect()->route('admin.clubs.index')
                             ->with('error', 'Club konnte nicht gelöscht werden (möglicherweise noch verknüpfte Events?).');
        }
    }

    public function create()
    {
        $genres = Genre::orderBy('name')->get();
        $clubOwners = User::role('ClubOwner')->orderBy('name')->get();
        // Länderliste holen (Code => Name)
        $countries = Countries::getList(app()->getLocale()); // Oder 'de' fest codieren

        return view('admin.clubs.create', compact('genres', 'clubOwners', 'countries'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate($this->validationRules());

        // Manuelle Verarbeitung der strukturierten Daten VOR der Validierung der JSON-Strings
        $processedData = $this->processStructuredFields($request);
        $validatedData = array_merge($validatedData, $processedData); // Füge verarbeitete Daten hinzu

        // JSON Felder validieren (jetzt gegen das aufbereitete Array)
        $validatedData = $this->validateJsonData($validatedData);
         if (isset($validatedData['json_error'])) {
            return back()->withErrors(['*_json' => $validatedData['json_error']])->withInput();
        }


        // Checkboxen behandeln
        $validatedData['is_active'] = $request->boolean('is_active');
        $validatedData['is_verified'] = $request->boolean('is_verified');

        // Entferne die temporären strukturierten Felder aus den zu speichernden Daten
        unset($validatedData['opening_hours_structured'], $validatedData['accessibility_structured']);


        DB::beginTransaction();
        try {
            $club = Club::create($validatedData); // Enthält jetzt die korrekten 'opening_hours' und 'accessibility_features' JSONs

            // Genre-Beziehung synchronisieren
            $club->genres()->sync($request->input('genres', [])); // Default auf leeres Array

            DB::commit();
            return redirect()->route('admin.clubs.index')
                             ->with('success', 'Club erfolgreich erstellt.');

        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return back()->with('error', 'Fehler beim Erstellen des Clubs: '.$e->getMessage())->withInput();
        }
    }

    public function edit(Club $club)
    {
        $genres = Genre::orderBy('name')->get();
        $clubOwners = User::role('ClubOwner')->orderBy('name')->get();
        $countries = Countries::getList(app()->getLocale()); // Oder 'de'
        $club->load('genres');

        // Dekodiere JSON für das Formular
        $openingHoursStructured = $this->decodeOpeningHours($club->opening_hours);
        $accessibilityStructured = $this->decodeAccessibility($club->accessibility_features);


        return view('admin.clubs.edit', compact(
            'club',
            'genres',
            'clubOwners',
            'countries',
            'openingHoursStructured', // Für Formular-Prefill
            'accessibilityStructured' // Für Formular-Prefill
        ));
    }

    public function update(Request $request, Club $club)
    {
        $validatedData = $request->validate($this->validationRules($club->id));

        // Manuelle Verarbeitung der strukturierten Daten VOR der Validierung der JSON-Strings
        $processedData = $this->processStructuredFields($request);
        $validatedData = array_merge($validatedData, $processedData);

         // JSON Felder validieren (jetzt gegen das aufbereitete Array)
        $validatedData = $this->validateJsonData($validatedData);
        if (isset($validatedData['json_error'])) {
            return back()->withErrors(['*_json' => $validatedData['json_error']])->withInput();
        }

        // Checkboxen behandeln
        $validatedData['is_active'] = $request->boolean('is_active');
        $validatedData['is_verified'] = $request->boolean('is_verified');

         // Entferne die temporären strukturierten Felder
        unset($validatedData['opening_hours_structured'], $validatedData['accessibility_structured']);

        DB::beginTransaction();
        try {
            $club->update($validatedData);

            // Genre-Beziehung synchronisieren
            $club->genres()->sync($request->input('genres', []));

            DB::commit();
            return redirect()->route('admin.clubs.index')
                             ->with('success', 'Club erfolgreich aktualisiert.');

        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return back()->with('error', 'Fehler beim Aktualisieren des Clubs: '.$e->getMessage())->withInput();
        }
    }


    /**
     * Hilfsfunktion für Validierungsregeln (DRY)
     * JSON Validierung wird jetzt separat gemacht
     */
    protected function validationRules($ignoreId = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'street_address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'zip_code' => ['nullable', 'string', 'max:10'],
            'country' => ['required', 'string', 'size:2', Rule::in(array_keys(Countries::getList(app()->getLocale())))], // Prüft gegen gültige Ländercodes
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'website' => ['nullable', 'url', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            // 'opening_hours' => ['nullable', 'json'], // Wird jetzt manuell validiert
            'price_level' => ['nullable', Rule::in(['$', '$$', '$$$'])],
            'is_active' => ['nullable', 'boolean'],
            'is_verified' => ['nullable', 'boolean'],
            // 'accessibility_features' => ['nullable', 'json'], // Wird jetzt manuell validiert
            'owner_id' => ['nullable', 'exists:users,id'],
            'genres' => ['nullable', 'array'],
            'genres.*' => ['exists:genres,id'],
            // Strukturierte Felder (nur für Request-Handling, nicht für DB)
            'opening_hours_structured' => ['nullable', 'array'],
            'accessibility_structured' => ['nullable', 'array'],
            'accessibility_structured.details' => ['nullable', 'string'], // Spezifische Validierung für Details
        ];
    }

    /**
    * Verarbeitet strukturierte Eingaben für JSON-Felder.
    */
    protected function processStructuredFields(Request $request): array
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
                 // Einfache Validierung des Zeitformats (könnte verbessert werden)
                if (preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $structuredHours[$day]['start']) &&
                    preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $structuredHours[$day]['end'])) {
                    $openingHoursJson[$day] = $structuredHours[$day]['start'] . '-' . $structuredHours[$day]['end'];
                } else {
                     // Optional: Fehler werfen oder ignorieren? Ignorieren hier.
                }
            }
        }
         // Nur speichern, wenn Daten vorhanden sind
        $output['opening_hours'] = !empty($openingHoursJson) ? $openingHoursJson : null;


        // Barrierefreiheit
        $structuredAccess = $request->input('accessibility_structured', []);
        $accessibilityJson = [];
        // Vordefinierte Checkboxen explizit prüfen
        $accessibilityJson['wheelchair_accessible'] = !empty($structuredAccess['wheelchair_accessible']);
        $accessibilityJson['accessible_restrooms'] = !empty($structuredAccess['accessible_restrooms']);
        $accessibilityJson['low_counter'] = !empty($structuredAccess['low_counter']); // Beispiel für weitere Features
        // Details Text
        if (!empty($structuredAccess['details'])) {
             $accessibilityJson['details'] = trim($structuredAccess['details']);
        }
        // Nur speichern, wenn Daten vorhanden sind (mind. ein Feature oder Details)
        $hasAccessData = $accessibilityJson['wheelchair_accessible'] || $accessibilityJson['accessible_restrooms'] || $accessibilityJson['low_counter'] || !empty($accessibilityJson['details']);
        $output['accessibility_features'] = $hasAccessData ? $accessibilityJson : null;


        return $output; // Gibt ['opening_hours' => ..., 'accessibility_features' => ...] zurück
    }


     /**
     * Validiert die aufbereiteten JSON-Daten (Arrays).
     * Gibt das Array zurück oder fügt einen json_error hinzu.
     */
    protected function validateJsonData(array $data): array
    {
        // Hier könnten spezifischere Validierungen für die Inhalte der JSON-Arrays erfolgen
        // z.B. ob Zeitintervalle gültig sind etc.
        // Vorerst lassen wir es einfach, da die Struktur im processStructuredFields erzeugt wird.
        return $data;
    }

    /**
     * Dekodiert JSON-Öffnungszeiten für das Formular.
     */
    protected function decodeOpeningHours(?array $jsonData): array
    {
        if (!$jsonData) return [];

        $structured = [];
        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        foreach($days as $day) {
            if (isset($jsonData[$day])) {
                 if ($jsonData[$day] === 'closed') {
                     $structured[$day]['closed'] = true;
                     $structured[$day]['start'] = '';
                     $structured[$day]['end'] = '';
                 } else {
                     $parts = explode('-', $jsonData[$day]);
                     $structured[$day]['closed'] = false;
                     $structured[$day]['start'] = $parts[0] ?? '';
                     $structured[$day]['end'] = $parts[1] ?? '';
                 }
            } else {
                 $structured[$day]['closed'] = false; // Default nicht geschlossen
                 $structured[$day]['start'] = '';
                 $structured[$day]['end'] = '';
            }
        }
        return $structured;
    }

     /**
     * Dekodiert JSON-Barrierefreiheit für das Formular.
     */
    protected function decodeAccessibility(?array $jsonData): array
    {
        if (!$jsonData) return ['wheelchair_accessible' => false, 'accessible_restrooms' => false, 'low_counter' => false, 'details' => ''];

        return [
             'wheelchair_accessible' => $jsonData['wheelchair_accessible'] ?? false,
             'accessible_restrooms' => $jsonData['accessible_restrooms'] ?? false,
             'low_counter' => $jsonData['low_counter'] ?? false, // Beispiel
             'details' => $jsonData['details'] ?? '',
        ];
    }

}