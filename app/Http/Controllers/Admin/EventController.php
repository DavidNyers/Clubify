<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Club;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $searchName = $request->input('search_name');
        $searchClub = $request->input('search_club');
        $filterStatus = $request->input('filter_status');

        $query = Event::with('club')->orderBy('start_time', 'desc');

        if ($searchName) {
            $query->where('name', 'LIKE', '%' . $searchName . '%');
        }
        if ($searchClub) {
            $query->whereHas('club', function ($q) use ($searchClub) {
                $q->where('name', 'LIKE', '%' . $searchClub . '%');
            });
        }
        if ($filterStatus) {
            match ($filterStatus) {
                'active' => $query->where('is_active', true)->whereNull('cancelled_at'),
                'inactive' => $query->where('is_active', false)->whereNull('cancelled_at'),
                'cancelled' => $query->whereNotNull('cancelled_at'),
                'needs_approval' => $query->where('requires_approval', true)->where('is_active', false),
                default => null,
            };
        }
        $events = $query->paginate(15);
        return view('admin.events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clubs = Club::orderBy('name')->pluck('name', 'id');
        $genres = Genre::orderBy('name')->get(['id', 'name']);
        $organizers = User::role('Organizer')->orderBy('name')->get(['id', 'name', 'email']);
        $djs = User::role('DJ')->orderBy('name')->get(['id', 'name']);
        return view('admin.events.create', compact('clubs', 'genres', 'organizers', 'djs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate($this->validationRules());

        $validatedData['is_active'] = $request->boolean('is_active');
        $validatedData['requires_approval'] = $request->boolean('requires_approval');
        $validatedData['allows_presale'] = $request->boolean('allows_presale');
        $validatedData['allows_guestlist'] = $request->boolean('allows_guestlist');

        if ($request->hasFile('cover_image')) {
            $imageFile = $request->file('cover_image');
            $originalName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $filename = time() . '_' . Str::slug($originalName) . '.webp'; // <<< Zielformat .webp
            $path = 'event-covers/' . $filename;

            $imageManager = new ImageManager(new GdDriver());

            try {
                $img = $imageManager->read($imageFile->getRealPath());
                $img->cover(1200, 630);
                $encodedImage = $img->toWebp(75); // <<< KORREKT: toWebp mit Qualität

                Storage::disk('public')->put($path, (string) $encodedImage);
                $validatedData['cover_image_path'] = $path;
            } catch (\Exception $e) {
                report($e);
                return back()->with('error', 'Fehler bei der Bildverarbeitung: ' . $e->getMessage())->withInput();
            }
        } else {
            $validatedData['cover_image_path'] = null;
        }

        DB::beginTransaction();
        try {
            $event = Event::create($validatedData);
            $event->genres()->sync($request->input('genres', []));
            $event->djs()->sync($request->input('djs', []));
            DB::commit();
            return redirect()->route('admin.events.index')->with('success', 'Event erfolgreich erstellt.');
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($validatedData['cover_image_path']) && $validatedData['cover_image_path'] && Storage::disk('public')->exists($validatedData['cover_image_path'])) {
                Storage::disk('public')->delete($validatedData['cover_image_path']);
            }
            report($e);
            return back()->with('error', 'Fehler beim Erstellen des Events: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        $clubs = Club::orderBy('name')->pluck('name', 'id');
        $genres = Genre::orderBy('name')->get(['id', 'name']);
        $organizers = User::role('Organizer')->orderBy('name')->get(['id', 'name', 'email']);
        $djs = User::role('DJ')->orderBy('name')->get(['id', 'name']);
        $event->load(['genres:id', 'djs:id']);
        return view('admin.events.edit', compact('event', 'clubs', 'genres', 'organizers', 'djs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        $validatedData = $request->validate($this->validationRules($event->id));

        $validatedData['is_active'] = $request->boolean('is_active');
        $validatedData['requires_approval'] = $request->boolean('requires_approval');
        $validatedData['allows_presale'] = $request->boolean('allows_presale');
        $validatedData['allows_guestlist'] = $request->boolean('allows_guestlist');
        $validatedData['cancelled_at'] = $request->boolean('is_cancelled') ? now() : null;

        if ($request->hasFile('cover_image')) {
            if ($event->cover_image_path && Storage::disk('public')->exists($event->cover_image_path)) {
                Storage::disk('public')->delete($event->cover_image_path);
            }
            $imageFile = $request->file('cover_image');
            $originalName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $filename = time() . '_' . Str::slug($originalName) . '.webp'; // <<< Zielformat .webp
            $path = 'event-covers/' . $filename;

            $imageManager = new ImageManager(new GdDriver());

            try {
                $img = $imageManager->read($imageFile->getRealPath());
                $img->cover(1200, 630);
                $encodedImage = $img->toWebp(75); // <<< KORREKT: toWebp mit Qualität

                Storage::disk('public')->put($path, (string) $encodedImage);
                $validatedData['cover_image_path'] = $path;
            } catch (\Exception $e) {
                report($e);
                return back()->with('error', 'Fehler bei der Bildverarbeitung beim Update: ' . $e->getMessage())->withInput();
            }

        } elseif ($request->input('remove_cover_image') == '1') {
            if ($event->cover_image_path && Storage::disk('public')->exists($event->cover_image_path)) {
                Storage::disk('public')->delete($event->cover_image_path);
            }
            $validatedData['cover_image_path'] = null;
        }

        DB::beginTransaction();
        try {
            $event->update($validatedData);
            $event->genres()->sync($request->input('genres', []));
            $event->djs()->sync($request->input('djs', []));
            DB::commit();
            return redirect()->route('admin.events.index')->with('success', 'Event erfolgreich aktualisiert.');
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return back()->with('error', 'Fehler beim Aktualisieren des Events: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Hilfsfunktion für Validierungsregeln (DRY)
     */
    protected function validationRules($ignoreId = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_time' => ['required', 'date_format:Y-m-d\TH:i'], // Angepasst an datetime-local
            'end_time' => ['nullable', 'date_format:Y-m-d\TH:i', 'after_or_equal:start_time'],
            'club_id' => ['required', 'exists:clubs,id'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'], // Max 4MB
            'organizer_id' => ['nullable', 'exists:users,id'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['required_with:price', 'string', 'size:3'],
            'is_active' => ['nullable', 'boolean'],
            'requires_approval' => ['nullable', 'boolean'],
            'allows_presale' => ['nullable', 'boolean'],
            'allows_guestlist' => ['nullable', 'boolean'],
            'is_cancelled' => ['nullable', 'boolean'],
            'genres' => ['nullable', 'array'],
            'genres.*' => ['exists:genres,id'],
            'djs' => ['nullable', 'array'],
            'djs.*' => ['exists:users,id'],
        ];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        // Altes Bild löschen, falls vorhanden
        if ($event->cover_image_path && Storage::disk('public')->exists($event->cover_image_path)) {
            Storage::disk('public')->delete($event->cover_image_path);
        }
        try {
            $event->delete();
            return redirect()->route('admin.events.index')->with('success', 'Event erfolgreich gelöscht.');
        } catch (\Exception $e) {
             report($e);
            return redirect()->route('admin.events.index')->with('error', 'Event konnte nicht gelöscht werden.');
        }
    }
}