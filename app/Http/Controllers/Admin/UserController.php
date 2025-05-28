<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role; // <<< Rollen importieren
use Illuminate\Support\Facades\Hash; // Für Passwort-Update (optional)
use Illuminate\Validation\Rule; // Für Unique-Regel
use Illuminate\Validation\Rules; // Für Passwort-Regel (optional)

class UserController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */ 
    public function index(Request $request): \Illuminate\View\View
        {
        // 1. Hole Such- und Filterparameter aus dem Request
        $searchName = $request->input('search_name');
        $searchEmail = $request->input('search_email');
        $filterRole = $request->input('filter_role');

        // 2. Starte den Query Builder für das User-Model
        $query = User::query();

        // 3. Lade notwendige Beziehungen und Counts mit Eager Loading / withCount
        $query->with('roles') // Lade die Rollen-Beziehung, um Rollennamen anzuzeigen
              // Zähle verknüpfte Events (Relation 'events' im User-Model benötigt)
              ->withCount('events as created_events_count')
              // Zähle verknüpfte Clubs (Relation 'clubs' im User-Model benötigt)
              ->withCount('clubs as owned_clubs_count')
              // Zähle DJ Gigs (Relation 'djGigs' im User-Model benötigt)
              ->withCount('djGigs as dj_gigs_count');

        // 4. Wende Filter an, falls Parameter gesetzt sind
        if ($searchName) {
            // Suche nach dem Namen (case-insensitive)
            $query->where('name', 'LIKE', '%' . $searchName . '%');
        }
        if ($searchEmail) {
            // Suche nach der E-Mail (case-insensitive)
            $query->where('email', 'LIKE', '%' . $searchEmail . '%');
        }
        if ($filterRole) {
            // Filtere Benutzer, die die spezifische Rolle haben.
            // 'whereHas' prüft, ob die Beziehung 'roles' existiert UND
            // wendet eine Bedingung auf die Beziehungstabelle an.
            $query->whereHas('roles', function ($roleQuery) use ($filterRole) {
                $roleQuery->where('name', $filterRole); // Filtert nach dem Rollennamen
            });
        }

        // 5. Sortiere die Ergebnisse (z.B. nach Name)
        $query->orderBy('name', 'asc');

        // 6. Führe die Abfrage aus und paginiere die Ergebnisse
        $users = $query->paginate(20) // Zeige 20 Benutzer pro Seite
                      // Wichtig: Hänge Query-Parameter an Paginierungslinks an,
                      // damit Filter bei Seitenwechsel erhalten bleiben
                      ->appends($request->query());

        // 7. Hole alle verfügbaren Rollennamen für den Filter-Dropdown
        // pluck('name', 'name') erstellt ein Array ['RoleName' => 'RoleName']
        $roles = Role::orderBy('name')->pluck('name', 'name');

        // 8. Gib die View zurück und übergebe die Daten (Benutzerliste und Rollenliste)
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function show(User $user): \Illuminate\View\View
    {
        // Lade alle notwendigen Relationen für die Detailansicht
        $user->load([
            'roles', // Rollen des Benutzers
            'events' => function ($query) { // Events als Veranstalter (limitiert?)
                $query->orderBy('start_time', 'desc')->limit(10); // Lade letzte 10 Events
            },
            'clubs' => function ($query) { // Clubs als Besitzer (limitiert?)
                $query->orderBy('name')->limit(10);
            },
            'djGigs' => function ($query) { // Events als DJ (limitiert?)
                $query->orderBy('start_time', 'desc')->limit(10);
            },
            // Spätere Relationen hier laden:
            // 'subscriptions' => function($query) { $query->active(); }, // Aktives Abo
            // 'orders' => function($query) { $query->latest()->limit(5); }, // Letzte Bestellungen
            // 'reviews' => function($query) { $query->latest()->limit(5); }, // Letzte Bewertungen
        ]);

        // Optional: Daten für Gamification-Statistiken laden (Punkte, Badges etc.)

        return view('admin.users.show', compact('user'));
    }


    public function edit(User $user)
    {
        // Lade alle verfügbaren Rollen
        $roles = Role::orderBy('name')->get();
        // Lade die aktuellen Rollen des Benutzers (nur die Namen oder IDs reichen)
        $user->load('roles:name'); // Lädt die Namen der Rollen

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Validierung
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // E-Mail muss einzigartig sein, außer für den aktuellen Benutzer
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            // Passwort ist optional. Nur validieren und hashen, wenn es eingegeben wurde.
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            // Rollen müssen existieren
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,name'] // Prüfen gegen Rollennamen
        ]);

        // Update Basisdaten
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];

        // Update Passwort, falls ein neues eingegeben wurde
        if (!empty($validatedData['password'])) {
            $user->password = Hash::make($validatedData['password']);
        }

        // E-Mail Verifizierung (optional, wenn E-Mail geändert wird?)
        // if ($user->isDirty('email')) {
        //     $user->email_verified_at = null;
        // }

        $user->save();

        // Rollen synchronisieren (ersetzt alle bisherigen Rollen des Users)
        // Vorsicht bei der Admin-Rolle! Ggf. zusätzliche Checks einbauen.
        if ($request->has('roles')) {
            // Verhindere, dass der letzte Admin seine Admin-Rolle verliert (optional)
            if ($user->hasRole('Administrator') && !in_array('Administrator', $request->input('roles', []))) {
                $adminCount = User::role('Administrator')->count();
                if ($adminCount <= 1) {
                    return back()->with('error', 'Der letzte Administrator kann seine Rolle nicht verlieren.');
                }
            }
            $user->syncRoles($request->input('roles', [])); // syncRoles erwartet Rollennamen oder IDs
        } else {
            // Keine Rollen ausgewählt -> Alle Rollen entfernen (außer vielleicht 'User'?)
            // Vorsicht: Wenn Admin hier ist, könnte er seine Rolle verlieren!
             if ($user->hasRole('Administrator')) {
                 $adminCount = User::role('Administrator')->count();
                 if ($adminCount <= 1 && $user->id === auth()->id()) { // Nur wenn es der letzte UND man selbst ist
                     // Behalte die Admin-Rolle oder gib einen Fehler aus
                     // $user->syncRoles(['Administrator']); // Behalte Admin
                      return back()->with('error', 'Der letzte Administrator kann seine Rolle nicht entfernen.');
                 }
             }
            $user->syncRoles([]); // Entfernt alle Rollen
            // Optional: Standardrolle 'User' wieder zuweisen, wenn keine andere gewählt wurde
             if (!$user->hasAnyRole(Role::all())) {
                 $user->assignRole('User');
             }
        }


        return redirect()->route('admin.users.index')
                         ->with('success', 'Benutzer erfolgreich aktualisiert.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Wird im nächsten Schritt implementiert
        // Vorsicht beim Löschen von Admins oder wichtigen Benutzern!
        // Überlege Soft Deletes oder Deaktivierung statt hartem Löschen.
        try {
            // Verhindere Selbstzerstörung oder Löschen anderer Admins (optional)
            if ($user->id === auth()->id()) {
                return redirect()->route('admin.users.index')->with('error', 'Sie können sich nicht selbst löschen.');
            }
            // Optional: Prüfen, ob der zu löschende User auch Admin ist und ob der aktuelle User Admin ist
            // if ($user->hasRole('Administrator') && !auth()->user()->hasRole('SuperAdmin')) { ... }

            $user->roles()->detach(); // Rollen entfernen
            $user->delete();
            return redirect()->route('admin.users.index')->with('success', 'Benutzer erfolgreich gelöscht.');
        } catch (\Exception $e) {
            report($e);
            return redirect()->route('admin.users.index')->with('error', 'Benutzer konnte nicht gelöscht werden.');
        }
    }
}