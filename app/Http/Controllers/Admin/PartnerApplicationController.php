<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;

class PartnerApplicationController extends Controller
{
    /**
     * Zeigt die Liste der offenen Partneranträge (Benutzer mit Status 'pending').
     */
    public function index(Request $request)
    {
        $query = User::where('partner_status', 'pending')
                     ->orderBy('created_at', 'asc');

        $pendingApplications = $query->paginate(20);

        return view('admin.partner-applications.index', compact('pendingApplications'));
    }

    public function show(User $user)
    {
        // Prüfe, ob der Benutzer überhaupt einen Antrag gestellt hat oder bearbeitet wurde
        if ($user->partner_status === 'none') {
            abort(404); // Oder Weiterleitung mit Fehlermeldung
        }

        // Lade notwendige Relationen für die Detailansicht (ähnlich wie UsersController@show)
        $user->load(['roles', 'partnerStatusProcessor']); // Lade den bearbeitenden Admin

        // Lade alle Partner-Rollen für das Auswahlfeld zum Annehmen
        $partnerRoles = Role::whereNotIn('name', ['Administrator', 'User', 'VIP'])->orderBy('name')->get();

        return view('admin.partner-applications.show', compact('user', 'partnerRoles'));
    }

    /**
     * Nimmt den Partnerantrag eines Benutzers an.
     * Weist die entsprechende Rolle zu und aktualisiert den Status.
     */
    public function approve(Request $request, User $user)
    {
        $validated = $request->validate([
            'role_to_assign' => ['required', 'string', Rule::exists('roles', 'name')]
        ]);
        $roleNameToAssign = $validated['role_to_assign'];

        // Nur Admins oder die zugewiesene Rolle dürfen keine Partnerrollen sein
        if (in_array($roleNameToAssign, ['Administrator', 'User', 'VIP'])) {
             return back()->with('error', 'Ungültige Rolle für Partner ausgewählt.')->withInput();
        }

        if ($user->partner_status !== 'pending') {
             return back()->with('error', 'Dieser Antrag ist nicht mehr offen.');
        }

        // Rolle hinzufügen
        $user->assignRole($roleNameToAssign);
        if ($user->hasRole('User') && $user->roles()->count() > 1) {
             $user->removeRole('User');
        }

        // Status und Bearbeitungsinfos aktualisieren
        $user->partner_status = 'approved';
        $user->partner_application_notes = null;
        $user->partner_status_processed_by = Auth::id(); // ID des eingeloggten Admins
        $user->partner_status_processed_at = now();
        $user->save();

        // TODO: E-Mail senden
        // Notification::send($user, new PartnerApplicationApproved($roleNameToAssign));

        return redirect()->route('admin.partner-applications.index') // Zurück zur Liste
                         ->with('success', "Antrag von {$user->name} angenommen und Rolle '{$roleNameToAssign}' zugewiesen.");
    }

    /**
     * Lehnt den Partnerantrag eines Benutzers ab.
     */
    public function reject(Request $request, User $user)
    {
         $validated = $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:1000'],
         ]);

        if ($user->partner_status !== 'pending') {
             return back()->with('error', 'Dieser Antrag ist nicht mehr offen.');
        }

        // Status und Bearbeitungsinfos aktualisieren
        $user->partner_status = 'rejected';
        $user->partner_application_notes = $validated['rejection_reason'] ?? null;
        $user->partner_status_processed_by = Auth::id(); // ID des eingeloggten Admins
        $user->partner_status_processed_at = now();
        $user->save();

         // TODO: E-Mail senden
         // Notification::send($user, new PartnerApplicationRejected($validated['rejection_reason']));

         return redirect()->route('admin.partner-applications.index') // Zurück zur Liste
                         ->with('success', "Antrag von {$user->name} abgelehnt.");
    }
}