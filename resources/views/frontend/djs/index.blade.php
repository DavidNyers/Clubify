<x-frontend-layout :title="$title" :description="$description">

    {{-- Header --}}
    <div class="bg-gray-200 dark:bg-gray-800 py-8">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $title }}</h1>
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">

        {{-- Filter & Sortierung Bar --}}
        <div class="mb-8 p-4 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <form id="filter-sort-form" action="{{ route('djs.index') }}" method="GET"
                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
                {{-- Suche --}}
                <div class="sm:col-span-2 md:col-span-2">
                    <label for="search" class="form-label text-xs">DJ / Name suchen</label>
                    <input type="search" id="search" name="search" value="{{ $searchName ?? '' }}"
                        placeholder="Suche..." class="form-input-field text-sm w-full mt-1">
                </div>
                {{-- Filter Genre (Platzhalter) --}}
                <div class="sm:col-span-1 md:col-span-1">
                    <label for="filter-genre" class="form-label text-xs">Genre (TODO)</label>
                    <select id="filter-genre" name="genre" class="form-select-field text-sm w-full mt-1" disabled>
                        <option value="">Alle Genres</option>
                    </select>
                </div>
                {{-- Sortierung --}}
                <div>
                    <label for="sort-djs" class="form-label text-xs">Sortieren nach</label>
                    <select id="sort-djs" name="sort" class="form-select-field text-sm w-full mt-1">
                        <option value="name-asc" {{ $sortValue == 'name-asc' ? 'selected' : '' }}>Name (A-Z)</option>
                        <option value="name-desc" {{ $sortValue == 'name-desc' ? 'selected' : '' }}>Name (Z-A)</option>
                        <option value="date-desc" {{ $sortValue == 'date-desc' ? 'selected' : '' }}>Neueste zuerst
                        </option> {{-- Sortiert nach Profil-Erstellung --}}
                    </select>
                </div>

                {{-- Submit/Reset Buttons --}}
                <div class="flex items-center space-x-2 md:col-start-4">
                    <button type="submit" class="btn-primary w-full py-1.5 text-sm">Anwenden</button>
                    @if (request()->hasAny(['search', 'genre', 'sort']))
                        <a href="{{ route('djs.index') }}" class="btn-secondary w-full py-1.5 text-sm text-center"
                            title="Filter/Sortierung zurücksetzen">Reset</a>
                    @endif
                </div>
                @foreach (request()->except(['search', 'genre', 'sort', 'page']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
            </form>
        </div>
        {{-- Ende Filter Bar --}}

        {{-- DJ Liste Grid --}}
        @if ($djProfiles->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($djProfiles as $djProfile)
                    {{-- DJ Card --}}
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden flex flex-col group transition-shadow duration-200 hover:shadow-xl text-center">
                        {{-- Bild Bereich --}}
                        <a href="{{ route('djs.show', $djProfile) }}"
                            class="block relative h-48 bg-gray-300 dark:bg-gray-700 group-hover:opacity-90 transition duration-150 ease-in-out">
                            {{-- TODO: DJ Profilbild laden --}}
                            {{-- @if ($djProfile->profile_image_path)
                              <img src="{{ Storage::url($djProfile->profile_image_path) }}" alt="{{ $djProfile->displayName }}" class="w-full h-full object-cover">
                         @else --}}
                            <span
                                class="absolute inset-0 flex items-center justify-center text-gray-500 dark:text-gray-400 text-lg font-medium">
                                <svg class="w-16 h-16 text-gray-400 dark:text-gray-500" fill="currentColor"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z">
                                    </path>
                                </svg>
                            </span>
                            {{-- @endif --}}
                            @if ($djProfile->is_verified)
                                <span
                                    class="absolute top-2 right-2 bg-blue-500 text-white text-xs font-semibold px-2 py-0.5 rounded-full shadow"
                                    title="Verifiziert">✓</span>
                            @endif
                        </a>
                        {{-- Text Inhalt --}}
                        <div class="p-4 flex flex-col flex-grow">
                            <h3
                                class="text-lg font-semibold mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition duration-150 ease-in-out">
                                <a href="{{ route('djs.show', $djProfile) }}">
                                    {{ $djProfile->displayName }} {{-- Nutzt Accessor für Stage Name oder User Name --}}
                                </a>
                            </h3>
                            {{-- Optional: Hauptgenre(s) des DJs anzeigen --}}
                            <p class="text-sm text-indigo-500 dark:text-indigo-400 mb-2">Techno, House (TODO)</p>

                            {{-- Social Links (Beispiele) --}}
                            <div class="mt-auto pt-3 flex justify-center space-x-3 text-gray-400 dark:text-gray-500">
                                @if ($djProfile->social_links['soundcloud'] ?? null)
                                    <a href="{{ $djProfile->social_links['soundcloud'] }}" target="_blank"
                                        class="hover:text-indigo-500"><svg class="w-5 h-5" fill="currentColor"
                                            viewBox="0 0 24 24">...</svg></a>
                                @endif {{-- Soundcloud Icon --}}
                                @if ($djProfile->social_links['instagram'] ?? null)
                                    <a href="{{ $djProfile->social_links['instagram'] }}" target="_blank"
                                        class="hover:text-indigo-500"><svg class="w-5 h-5" fill="currentColor"
                                            viewBox="0 0 24 24">...</svg></a>
                                @endif {{-- Instagram Icon --}}
                                {{-- Weitere Links --}}
                            </div>
                        </div>
                    </div>
                    {{-- Ende DJ Card --}}
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-12">
                {{ $djProfiles->links() }}
            </div>
        @else
            {{-- Keine DJs gefunden Nachricht --}}
            <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                {{-- Icon und Text wie bei Clubs/Events --}}
                <h2 class="mt-2 text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">Keine DJs gefunden</h2>
                <p class="text-gray-500 dark:text-gray-400">Es wurden keine DJs gefunden, die den aktuellen Kriterien
                    entsprechen.</p>
                @if (request()->hasAny(['search', 'genre', 'sort']))
                    <div class="mt-6"><a href="{{ route('djs.index') }}" class="btn-secondary">Alle Filter
                            zurücksetzen</a></div>
                @endif
            </div>
        @endif

    </div> {{-- Ende Container --}}

</x-frontend-layout>
