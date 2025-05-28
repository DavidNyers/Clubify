<x-admin-layout>
    <x-slot name="header" title="Benutzerdetails: {{ $user->name }}">
        <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
            {{-- Profilbild & Name --}}
            <div>
                {{-- Optional: Avatar --}}
                <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight align-middle">
                    {{ $user->name }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Benutzer-ID: {{ $user->id }}</p>
            </div>
            {{-- Aktionen --}}
            <div class="flex-shrink-0 flex items-center space-x-3">
                {{-- Zurück-Knopf --}}
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-4 h-4 mr-1 inline-block">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                    Zurück zur Liste
                </a>
                {{-- Bearbeiten-Knopf --}}
                <a href="{{ route('admin.users.edit', $user) }}" class="btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-4 h-4 mr-1 inline-block">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                    </svg>
                    Bearbeiten
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6"> {{-- Etwas weniger Padding oben, mehr Abstand zwischen Sektionen --}}

        {{-- Grid für Karten-Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Karte 1: Hauptinformationen --}}
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                        Profil & Status
                    </h3>
                </div>
                {{-- Definitionsliste für Details --}}
                <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                    <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">
                            {{ $user->name }}</dd>
                    </div>
                    <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">E-Mail</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">
                            {{ $user->email }}</dd>
                    </div>
                    <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">E-Mail verifiziert</dt>
                        <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                            @if ($user->hasVerifiedEmail())
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                    Ja ({{ $user->email_verified_at->diffForHumans() }}) {{-- Relatives Datum --}}
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">Nein</span>
                                {{-- Optional: Button zum Senden --}}
                            @endif
                        </dd>
                    </div>
                    <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Registriert</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">
                            {{ $user->created_at->format('d.m.Y H:i') }} ({{ $user->created_at->diffForHumans() }})</dd>
                    </div>
                    <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Letzter Login</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">
                            {{ $user->last_login_at ? $user->last_login_at->format('d.m.Y H:i') . ' (' . $user->last_login_at->diffForHumans() . ')' : 'Nie' }}
                        </dd>
                    </div>
                    {{-- TODO: Abo-Status hier hinzufügen, wenn implementiert --}}
                    {{-- <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                      <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Abonnement</dt>
                      <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">
                          {{ $user->activeSubscription?->plan->name ?? 'Kein aktives Abo' }}
                          @if ($user->activeSubscription) (Seit: {{ $user->activeSubscription->created_at->format('d.m.Y') }}) @endif
                      </dd>
                  </div> --}}
                </dl>
            </div>

            {{-- Karte 2: Rollen & Berechtigungen --}}
            <div class="lg:col-span-1 bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                        Rollen & Berechtigungen
                    </h3>
                </div>
                <div class="px-4 py-5 sm:p-6 space-y-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Zugewiesene Rollen:</h4>
                        <div class="flex flex-wrap gap-1">
                            @forelse ($user->getRoleNames() as $roleName)
                                {{-- getRoleNames() ist effizienter --}}
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium /* ... Farben ... */
                                  @if ($roleName == 'Administrator') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                  @elseif($roleName == 'Organizer') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                  @elseif($roleName == 'ClubOwner') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300
                                  @elseif($roleName == 'DJ') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                  @elseif($roleName == 'Moderator') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                  @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                                    {{ $roleName }}
                                </span>
                            @empty
                                <span class="text-sm text-gray-500 italic">Keine Rollen zugewiesen.</span>
                            @endforelse
                        </div>
                    </div>
                    {{-- Optionale Anzeige der direkten Berechtigungen (kann lang werden!) --}}
                    {{-- <div>
                      <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2 mt-4">Direkte Berechtigungen:</h4>
                      <div class="flex flex-wrap gap-1">
                           @forelse ($user->getDirectPermissions() as $permission)
                               <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                   {{ $permission->name }}
                               </span>
                          @empty
                              <span class="text-sm text-gray-500 italic">Keine direkten Berechtigungen.</span>
                          @endforelse
                      </div>
                  </div> --}}
                    {{-- Optionale Anzeige ALLER Berechtigungen (inkl. via Rollen) --}}
                    {{-- <div>
                      <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2 mt-4">Alle Berechtigungen (inkl. Rollen):</h4>
                      <div class="flex flex-wrap gap-1 max-h-40 overflow-y-auto">
                           @forelse ($user->getAllPermissions() as $permission)
                              <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                   {{ $permission->name }}
                               </span>
                          @empty
                              <span class="text-sm text-gray-500 italic">Keine Berechtigungen.</span>
                          @endforelse
                      </div>
                  </div> --}}
                </div>
            </div>

            {{-- Karte 3: Verknüpfte Aktivitäten --}}
            @if (
                $user->hasRole('Organizer') ||
                    $user->hasRole('ClubOwner') ||
                    $user->hasRole('DJ') ||
                    $user->events_count > 0 ||
                    $user->clubs_count > 0 ||
                    $user->dj_gigs_count > 0)
                <div class="lg:col-span-3 bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
                    {{-- Volle Breite --}}
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                            Aktivitäten & Verknüpfungen
                        </h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6 space-y-5">
                        {{-- Events als Veranstalter --}}
                        @if ($user->events_count > 0)
                            <div>
                                <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Erstellte Events
                                    ({{ $user->events_count }}):</h4>
                                <ul class="text-sm text-gray-700 dark:text-gray-300 space-y-1 max-h-40 overflow-y-auto">
                                    @foreach ($user->events as $event)
                                        {{-- Geladene Relation nutzen --}}
                                        <li class="flex justify-between items-center">
                                            <a href="{{ route('admin.events.edit', $event) }}"
                                                class="hover:underline hover:text-indigo-600 dark:hover:text-indigo-400">{{ $event->name }}</a>
                                            <span
                                                class="text-xs text-gray-500 whitespace-nowrap">{{ $event->start_time->format('d.m.Y') }}
                                                @if ($event->isCancelled())
                                                    <span class="text-red-500">(Abgesagt)</span>
                                                @elseif(!$event->is_active)
                                                    <span class="text-yellow-500">(Inaktiv/Review)</span>
                                                @endif
                                            </span>
                                        </li>
                                    @endforeach
                                    @if ($user->events_count > count($user->events))
                                        <li class="text-xs italic text-gray-500">... (zeige letzte
                                            {{ count($user->events) }})</li>
                                    @endif
                                </ul>
                            </div>
                        @endif
                        {{-- Clubs als Besitzer --}}
                        @if ($user->clubs_count > 0)
                            <div>
                                <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Verwaltete Clubs
                                    ({{ $user->clubs_count }}):</h4>
                                <ul class="text-sm text-gray-700 dark:text-gray-300 space-y-1 max-h-40 overflow-y-auto">
                                    @foreach ($user->clubs as $club)
                                        <li class="flex justify-between items-center">
                                            <a href="{{ route('admin.clubs.edit', $club) }}"
                                                class="hover:underline hover:text-indigo-600 dark:hover:text-indigo-400">{{ $club->name }}</a>
                                            <span class="text-xs text-gray-500 whitespace-nowrap">
                                                @if ($club->is_verified)
                                                    <span class="text-blue-500">(Verifiziert)</span>
                                                @endif
                                                @if (!$club->is_active)
                                                    <span class="text-yellow-500">(Inaktiv)</span>
                                                @endif
                                            </span>
                                        </li>
                                    @endforeach
                                    @if ($user->clubs_count > count($user->clubs))
                                        <li class="text-xs italic text-gray-500">... (zeige letzte
                                            {{ count($user->clubs) }})</li>
                                    @endif
                                </ul>
                            </div>
                        @endif
                        {{-- Events als DJ --}}
                        @if ($user->dj_gigs_count > 0)
                            <div>
                                <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">DJ Gigs
                                    ({{ $user->dj_gigs_count }}):</h4>
                                <ul class="text-sm text-gray-700 dark:text-gray-300 space-y-1 max-h-40 overflow-y-auto">
                                    @foreach ($user->djGigs as $gig)
                                        <li class="flex justify-between items-center">
                                            <a href="{{ route('admin.events.edit', $gig) }}"
                                                class="hover:underline hover:text-indigo-600 dark:hover:text-indigo-400">{{ $gig->name }}</a>
                                            <span
                                                class="text-xs text-gray-500 whitespace-nowrap">{{ $gig->start_time->format('d.m.Y') }}
                                                @if ($gig->isCancelled())
                                                    <span class="text-red-500">(Abgesagt)</span>
                                                @endif
                                            </span>
                                        </li>
                                    @endforeach
                                    @if ($user->dj_gigs_count > count($user->djGigs))
                                        <li class="text-xs italic text-gray-500">... (zeige letzte
                                            {{ count($user->djGigs) }})</li>
                                    @endif
                                </ul>
                            </div>
                        @endif
                        {{-- Platzhalter für andere Aktivitäten --}}
                        {{-- <div><h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Letzte Bestellungen:</h4> <p class="text-sm text-gray-500 italic">Noch nicht implementiert.</p></div> --}}
                        {{-- <div><h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Letzte Bewertungen:</h4> <p class="text-sm text-gray-500 italic">Noch nicht implementiert.</p></div> --}}

                    </div>
                </div>
            @endif

            {{-- TODO: Weitere Karten für z.B. Gamification, Notizen etc. --}}

        </div> {{-- Ende Grid --}}
    </div> {{-- Ende Haupt-Padding-Container --}}

    {{-- Stelle sicher, dass die globalen Button-Styles geladen werden --}}

</x-admin-layout>
