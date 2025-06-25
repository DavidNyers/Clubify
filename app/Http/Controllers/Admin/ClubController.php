<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Genre;
use App\Models\User;
use App\Models\ClubImage; // Model für Galeriebilder
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Logging für Fehler
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Countries;

class ClubController extends Controller
{
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

    public function create()
    {
        $genres = Genre::orderBy('name')->get();
        $clubOwners = User::role('ClubOwner')->orderBy('name')->get();
        $countries = collect(Countries::getList(app()->getLocale()))->sort()->all();
        $openingHoursStructured = $this->decodeOpeningHours(null);
        $accessibilityStructured = $this->decodeAccessibility(null);

        return view('admin.clubs.create', compact('genres', 'clubOwners', 'countries', 'openingHoursStructured', 'accessibilityStructured'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate($this->validationRules());

        $processedJson = $this->processJsonFields($request);
        $validatedData['opening_hours'] = $processedJson['opening_hours'];
        $validatedData['accessibility_features'] = $processedJson['accessibility_features'];

        $validatedData['is_active'] = $request->boolean('is_active', true);
        $validatedData['is_verified'] = $request->boolean('is_verified', false);

        DB::beginTransaction();
        try {
            // Unset gallery_images from the data array before creating the club
            unset($validatedData['gallery_images']);
            $club = Club::create($validatedData);

            // Process and store gallery images
            if ($request->hasFile('gallery_images')) {
                $this->processAndStoreGalleryImages($request->file('gallery_images'), $club);
            }

            $club->genres()->sync($request->input('genres', []));
            DB::commit();
            return redirect()->route('admin.clubs.index')->with('success', 'Club erfolgreich erstellt.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Fehler beim Erstellen des Clubs: " . $e->getMessage());
            // Clean up created directory if club creation fails after directory was made
            if (isset($club) && $club->id && Storage::disk('public')->exists('club-gallery/' . $club->id)) {
                Storage::disk('public')->deleteDirectory('club-gallery/' . $club->id);
            }
            return back()->with('error', 'Ein unerwarteter Fehler ist aufgetreten. Bitte versuchen Sie es erneut.')->withInput();
        }
    }

    public function show(Club $club)
    {
        return redirect()->route('admin.clubs.edit', $club);
    }

    public function edit(Club $club)
    {
        $genres = Genre::orderBy('name')->get(['id', 'name']);
        $clubOwners = User::role('ClubOwner')->orderBy('name')->get(['id', 'name', 'email']);
        $countries = collect(Countries::getList(app()->getLocale()))->sort()->all();
        $club->load(['genres:id', 'galleryImages']);

        $openingHoursStructured = $this->decodeOpeningHours($club->opening_hours);
        $accessibilityStructured = $this->decodeAccessibility($club->accessibility_features);

        return view('admin.clubs.edit', compact(
            'club', 'genres', 'clubOwners', 'countries',
            'openingHoursStructured', 'accessibilityStructured'
        ));
    }

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
            // Unset image-related fields from the data array before updating the club
            unset($validatedData['gallery_images']);
            unset($validatedData['delete_images']);
            $club->update($validatedData);

            // Add new gallery images
            if ($request->hasFile('gallery_images')) {
                $this->processAndStoreGalleryImages($request->file('gallery_images'), $club);
            }

            // Delete marked images
            if ($request->has('delete_images')) {
                // Find images to delete that actually belong to this club
                $imagesToDelete = ClubImage::where('club_id', $club->id)
                                           ->whereIn('id', $request->input('delete_images'))
                                           ->get();

                foreach ($imagesToDelete as $imageRecord) {
                    Storage::disk('public')->delete($imageRecord->path);
                    $imageRecord->delete();
                }
            }

            $club->genres()->sync($request->input('genres', []));
            DB::commit();
            return redirect()->route('admin.clubs.index')->with('success', 'Club erfolgreich aktualisiert.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Fehler beim Aktualisieren des Clubs {$club->id}: " . $e->getMessage());
            return back()->with('error', 'Ein unerwarteter Fehler ist aufgetreten. Bitte versuchen Sie es erneut.')->withInput();
        }
    }

    public function destroy(Club $club)
    {
        DB::beginTransaction();
        try {
            // The database cascade on delete (defined in the migration) will handle
            // deleting the ClubImage records. We just need to delete the files.
            $galleryPath = 'club-gallery/' . $club->id;
            if (Storage::disk('public')->exists($galleryPath)) {
                Storage::disk('public')->deleteDirectory($galleryPath);
            }
            
            // Delete the club itself. Related genres pivot entries and club images records
            // should be handled by database foreign key constraints (cascade on delete).
            $club->delete();

            DB::commit();
            return redirect()->route('admin.clubs.index')->with('success', 'Club und alle zugehörigen Daten erfolgreich gelöscht.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Fehler beim Löschen des Clubs {$club->id}: " . $e->getMessage());
            return redirect()->route('admin.clubs.index')->with('error', 'Club konnte nicht gelöscht werden.');
        }
    }
    
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
            'price_level' => ['nullable', Rule::in(['$', '$$', '$$$'])],
            'is_active' => ['nullable', 'boolean'],
            'is_verified' => ['nullable', 'boolean'],
            'owner_id' => ['nullable', 'exists:users,id'],
            'genres' => ['nullable', 'array'],
            'genres.*' => ['exists:genres,id'],
            'gallery_images'   => ['nullable', 'array', 'max:10'], // Max 10 images at once
            'gallery_images.*' => ['image', 'mimes:jpeg,png,jpg,webp,gif', 'max:5120'], // Max 5MB per image
            'delete_images' => ['nullable', 'array'],
            'delete_images.*' => ['integer', 'exists:club_images,id'],
        ];
    }

    protected function processAndStoreGalleryImages(array $imageFiles, Club $club): void
    {
        $imageManager = new ImageManager(new GdDriver());
        foreach ($imageFiles as $imageFile) {
            if ($imageFile->isValid()) {
                $originalName = $imageFile->getClientOriginalName();
                $filename = time() . '_' . uniqid() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.webp';
                $directory = 'club-gallery/' . $club->id;
                $path = $directory . '/' . $filename;

                $img = $imageManager->read($imageFile->getRealPath());
                $img->resize(1200, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
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

    protected function processJsonFields(Request $request): array
    {
        $output = [];
        // Opening hours
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

        // Accessibility
        $structuredAccess = $request->input('accessibility_structured', []);
        $accessibilityJson = [];
        $accessibilityJson['wheelchair_accessible'] = !empty($structuredAccess['wheelchair_accessible']);
        $accessibilityJson['accessible_restrooms'] = !empty($structuredAccess['accessible_restrooms']);
        $accessibilityJson['low_counter'] = !empty($structuredAccess['low_counter']);
        if (!empty($structuredAccess['details'])) { $accessibilityJson['details'] = trim($structuredAccess['details']); }
        $hasAccessData = $accessibilityJson['wheelchair_accessible'] || $accessibilityJson['accessible_restrooms'] || $accessibilityJson['low_counter'] || !empty($accessibilityJson['details']);
        $output['accessibility_features'] = $hasAccessData ? $accessibilityJson : null;

        return $output;
    }

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