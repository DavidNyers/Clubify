<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // Auth Fassade importieren

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * Check if the user has a specific role.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string $role The required role name.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Prüfen, ob der Benutzer eingeloggt ist UND die erforderliche Rolle hat
        if (!Auth::check() || !$request->user()->hasRole($role)) {
            // Benutzer hat nicht die erforderliche Rolle
            // Hier könnten wir den Benutzer umleiten oder einen 403 Forbidden Fehler werfen
            // Für den Admin-Bereich ist ein 403 oft sinnvoll
            abort(403, 'Zugriff verweigert: Sie haben nicht die erforderlichen Berechtigungen.');
        }

        // Benutzer hat die Rolle, Anfrage weiterleiten
        return $next($request);
    }
}