<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DjProfile;
use App\Models\User; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule; 

class DjController extends Controller
{
    /**
     * Display a listing of the resource (DJ Profiles).
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filterVerified = $request->input('filter_verified'); // 'yes', 'no'
        $filterVisible = $request->input('filter_visible');   // 'yes', 'no'

        // Lade DJ-Profile mit dem zugehörigen User
        $query = DjProfile::with('user') // Eager load user data
                          ->orderBy('stage_name'); // Sortiere nach Stage Name

        // Suche nach Stage Name oder User Name
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('stage_name', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', '%' . $search . '%');
                  });
            });
        }

        // Filtern nach Verifizierungsstatus
        if ($filterVerified === 'yes') {
            $query->where('is_verified', true);
        } elseif ($filterVerified === 'no') {
             $query->where('is_verified', false);
        }

        // Filtern nach Sichtbarkeitsstatus
        if ($filterVisible === 'yes') {
            $query->where('is_visible', true);
        } elseif ($filterVisible === 'no') {
             $query->where('is_visible', false);
        }

        $djProfiles = $query->paginate(15)->appends($request->query());

        return view('admin.djs.index', compact('djProfiles'));
    }

    public function create()
    {
        // Finde User mit Rolle 'DJ', die noch KEIN DjProfile haben
        $availableDjs = User::role('DJ')
                           ->whereDoesntHave('djProfile') // Prüft, ob die Relation 'djProfile' NICHT existiert
                           ->orderBy('name')
                           ->get(['id', 'name', 'email']);

        return view('admin.djs.create', compact('availableDjs'));
    }

    /**
     * Store a newly created DJ Profile in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate($this->validationRules());

        // JSON Felder validieren und umwandeln
        $processedJson = $this->processJsonLinkFields($request);
        if (isset($processedJson['json_error'])) {
             return back()->withErrors(['*_json' => $processedJson['json_error']])->withInput();
        }
        $validatedData['social_links'] = $processedJson['social_links'];
        $validatedData['music_links'] = $processedJson['music_links'];

        // Boolean Felder
        $validatedData['is_visible'] = $request->boolean('is_visible');
        $validatedData['is_verified'] = $request->boolean('is_verified');

        // TODO: Bild-Upload Handling (profile_image, banner_image)
        $validatedData['profile_image_path'] = null;
        $validatedData['banner_image_path'] = null;

         // Sicherstellen, dass der ausgewählte User die DJ Rolle hat (doppelte Sicherheit)
         $user = User::find($validatedData['user_id']);
         if (!$user || !$user->hasRole('DJ')) {
              return back()->with('error', 'Ausgewählter Benutzer ist kein DJ.')->withInput();
         }
         // Sicherstellen, dass der User noch kein Profil hat
          if (DjProfile::where('user_id', $user->id)->exists()) {
             return back()->with('error', 'Dieser Benutzer hat bereits ein DJ-Profil.')->withInput();
         }


        try {
            DjProfile::create($validatedData); // Slug wird automatisch generiert

            return redirect()->route('admin.djs.index')
                             ->with('success', 'DJ-Profil erfolgreich erstellt.');
        } catch (\Exception $e) {
            report($e);
            return back()->with('error', 'Fehler beim Erstellen des DJ-Profils: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified DJ Profile.
     */
    public function edit(DjProfile $dj) // Route Model Binding mit DjProfile (via Slug)
    {
        $dj->load('user'); // Lade den zugehörigen User

        // Dekodiere JSON für das Formular (nur zur Anzeige in Textareas)
        $socialLinksJson = $this->formatJsonForTextarea($dj->social_links);
        $musicLinksJson = $this->formatJsonForTextarea($dj->music_links);

        return view('admin.djs.edit', compact('dj', 'socialLinksJson', 'musicLinksJson'));
    }

    /**
     * Update the specified DJ Profile in storage.
     */
    public function update(Request $request, DjProfile $dj)
    {
         // Wir erlauben keine Änderung des user_id nach der Erstellung
         $validatedData = $request->validate($this->validationRules($dj->id));
         unset($validatedData['user_id']); // Entferne user_id aus den validierten Daten

         // JSON Felder validieren und umwandeln
        $processedJson = $this->processJsonLinkFields($request);
        if (isset($processedJson['json_error'])) {
             return back()->withErrors(['*_json' => $processedJson['json_error']])->withInput();
        }
        $validatedData['social_links'] = $processedJson['social_links'];
        $validatedData['music_links'] = $processedJson['music_links'];

         // Boolean Felder
        $validatedData['is_visible'] = $request->boolean('is_visible');
        $validatedData['is_verified'] = $request->boolean('is_verified');

         // TODO: Bild-Upload Handling (Update/Delete)

        try {
            $dj->update($validatedData);

            return redirect()->route('admin.djs.index')
                             ->with('success', 'DJ-Profil erfolgreich aktualisiert.');
        } catch (\Exception $e) {
            report($e);
            return back()->with('error', 'Fehler beim Aktualisieren des DJ-Profils: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Validierungsregeln für DJ-Profile.
     */
    protected function validationRules($ignoreId = null): array
    {
        $rules = [
            // user_id nur beim Erstellen benötigt und muss unique in dj_profiles sein
            'user_id' => [
                Rule::requiredIf(is_null($ignoreId)), // Nur required bei store
                'exists:users,id',
                Rule::unique('dj_profiles', 'user_id')->ignore($ignoreId) // Darf nur einmal vorkommen
            ],
            'stage_name' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            // 'profile_image_path' => ['nullable', 'image', 'max:2048'], // Später
            // 'banner_image_path' => ['nullable', 'image', 'max:4096'], // Später
            'social_links' => ['nullable', 'string'], // Validierung als String, JSON Check im Controller
            'music_links' => ['nullable', 'string'], // Validierung als String, JSON Check im Controller
            'is_visible' => ['nullable', 'boolean'],
            'is_verified' => ['nullable', 'boolean'],
            'booking_email' => ['nullable', 'email', 'max:255'],
            // 'technical_rider_path' => ['nullable', 'file', 'mimes:pdf', 'max:5120'], // Später
        ];

        return $rules;
    }

     /**
     * Hilfsfunktion zum Validieren und Konvertieren von JSON-Link-Feldern aus Textareas.
     */
    protected function processJsonLinkFields(Request $request): array
    {
        $output = [];
        $jsonFields = ['social_links', 'music_links'];
        foreach ($jsonFields as $field) {
            $jsonString = $request->input($field);
            if (!empty($jsonString)) {
                $decoded = json_decode($jsonString, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $output['json_error'] = "Ungültiges JSON-Format im Feld '$field'. Bitte Key-Value Paare verwenden, z.B. {\"soundcloud\": \"url\", ...}.";
                    $output[$field] = null; // Fehler -> null speichern
                    break;
                }
                // Optional: URLs validieren?
                // foreach($decoded as $key => $value) { if (!filter_var($value, FILTER_VALIDATE_URL)) { ... Fehler ...}}
                $output[$field] = $decoded; // Gültiges JSON -> Array speichern
            } else {
                $output[$field] = null; // Leerer String -> null speichern
            }
        }
        return $output;
    }

    /**
     * Formatiert JSON-Daten für die Anzeige in einer Textarea.
     */
    private function formatJsonForTextarea(?array $jsonData): string
    {
        if (empty($jsonData)) {
            return '';
        }
        // Konvertiert Array in schön formatierten JSON String
        return json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public function show(DjProfile $dj) { 
        return redirect()->route('admin.djs.edit', $dj); 
    } 
    
    public function destroy(DjProfile $dj) {
        // Löscht nur das DJ-Profil, nicht den User-Account!
        // Der User verliert aber ggf. seine DJ-spezifischen Daten.
         try {
            $dj->delete();
            return redirect()->route('admin.djs.index')->with('success', 'DJ-Profil erfolgreich gelöscht.');
        } catch (\Exception $e) {
             report($e);
            return redirect()->route('admin.djs.index')->with('error', 'DJ-Profil konnte nicht gelöscht werden.');
        }
    }
}