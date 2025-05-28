<x-admin-layout>
    <x-slot name="header" title="Benutzerverwaltung">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Benutzerverwaltung') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if (session('success'))
                        <div
                            class="mb-4 p-3 bg-green-100 dark:bg-green-900 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-200 rounded text-sm">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div
                            class="mb-4 p-3 bg-red-100 dark:bg-red-900 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-200 rounded text-sm">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Filter/Such Formular --}}
                    <form action="{{ route('admin.users.index') }}" method="GET" class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            <div>
                                <label for="search_name" class="form-label text-xs">Name</label>
                                <input type="text" name="search_name" id="search_name" placeholder="Name suchen..."
                                    value="{{ request('search_name') }}" class="form-input-field text-sm w-full mt-1">
                            </div>
                            <div>
                                <label for="search_email" class="form-label text-xs">E-Mail</label>
                                <input type="text" name="search_email" id="search_email"
                                    placeholder="E-Mail suchen..." value="{{ request('search_email') }}"
                                    class="form-input-field text-sm w-full mt-1">
                            </div>
                            <div>
                                <label for="filter_role" class="form-label text-xs">Rolle</label>
                                <select name="filter_role" id="filter_role"
                                    class="form-select-field text-sm w-full mt-1">
                                    <option value="">-- Alle Rollen --</option>
                                    @foreach ($roles as $roleValue => $roleName)
                                        <option value="{{ $roleValue }}"
                                            {{ request('filter_role') == $roleValue ? 'selected' : '' }}>
                                            {{ $roleName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex space-x-2">
                                <button type="submit" class="btn-primary py-1.5 px-3 text-sm w-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4 inline-block mr-1">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                    </svg>
                                    Filtern
                                </button>
                                @if (request('search_name') || request('search_email') || request('filter_role'))
                                    <a href="{{ route('admin.users.index') }}" class="btn-secondary py-1.5 px-3 text-sm"
                                        title="Filter zurücksetzen">Reset</a>
                                @endif
                            </div>
                        </div>
                    </form>

                    {{-- Benutzer Tabelle --}}
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-4 py-3">Name</th>
                                    <th scope="col" class="px-4 py-3">E-Mail</th>
                                    <th scope="col" class="px-4 py-3">Rollen</th>
                                    <th scope="col" class="px-4 py-3 text-center">Verifiziert</th>
                                    <th scope="col" class="px-4 py-3">Registriert</th> {{-- NEU --}}
                                    <th scope="col" class="px-4 py-3">Letzter Login</th> {{-- NEU --}}
                                    <th scope="col" class="px-4 py-3 text-center">Aktivitäten</th>
                                    {{-- NEU --}}
                                    {{-- <th scope="col" class="px-4 py-3">Abo</th> --}} {{-- NEU (später) --}}
                                    <th scope="col" class="px-4 py-3 text-right">Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-xs">
                                        {{-- Kleinere Schrift --}}
                                        <td
                                            class="px-4 py-2 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                            <a href="{{ route('admin.users.show', $user) }}" class="hover:underline"
                                                title="Details anzeigen">
                                                {{ $user->name }}
                                            </a>
                                            @if ($user->id === auth()->id())
                                                <span
                                                    class="ml-1 text-xs text-indigo-600 dark:text-indigo-400">(Sie)</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2">{{ $user->email }}</td>
                                        <td class="px-4 py-2">
                                            @foreach ($user->roles as $role)
                                                <span
                                                    class="mr-1 mb-1 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium /* ... Farben ... */">
                                                    {{ $role->name }}
                                                </span>
                                            @endforeach
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            @if ($user->hasVerifiedEmail())
                                                ✓
                                            @else
                                                <span class="text-red-500">✗</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap">
                                            {{ $user->created_at->format('d.m.y H:i') }}
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap">
                                            {{ $user->last_login_at?->format('d.m.y H:i') ?? '-' }}
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            {{-- Zeige relevante Counts basierend auf Rolle --}}
                                            @if ($user->hasRole('Organizer'))
                                                <span title="Erstellte Events">{{ $user->created_events_count ?? 0 }}
                                                    <small>E</small></span>
                                            @endif
                                            @if ($user->hasRole('ClubOwner'))
                                                <span title="Verwaltete Clubs"
                                                    class="ml-1">{{ $user->owned_clubs_count ?? 0 }}
                                                    <small>C</small></span>
                                            @endif
                                            @if ($user->hasRole('DJ'))
                                                <span title="DJ Gigs" class="ml-1">{{ $user->dj_gigs_count ?? 0 }}
                                                    <small>G</small></span>
                                            @endif
                                            {{-- Weitere Counts später --}}
                                        </td>
                                        {{-- <td class="px-4 py-2"> - </td> --}} {{-- Platzhalter Abo --}}
                                        <td class="px-4 py-2 text-right flex justify-end space-x-1 whitespace-nowrap">
                                            <a href="{{ route('admin.users.edit', $user) }}"
                                                class="text-blue-600 dark:text-blue-500 hover:underline">Bearb.</a>
                                            @if ($user->id !== auth()->id())
                                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                                    onsubmit="return confirm('Benutzer löschen?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 dark:text-red-500 hover:underline">Lösch.</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    {{-- ... Keine Benutzer gefunden ... --}}
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination wie gehabt --}}
                    <div class="mt-4">
                        {{ $users->appends(request()->query())->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Stelle sicher, dass die Formular-Styles aus dem Layout oder app.css geladen werden --}}
    {{-- Die Klassen .form-input-field etc. werden für die Filter verwendet --}}

</x-admin-layout>
