<x-frontend-layout :title="$title" :description="$description">

    {{-- Header für die Seite --}}
    <div class="bg-gray-200 dark:bg-gray-800 py-8">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $title }}</h1>
            {{-- Optional: Breadcrumbs hier einfügen --}}
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">

        {{-- Filter & Sortierung Bar --}}
        <div class="mb-8 p-4 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <form id="filter-sort-form" action="{{ route('clubs.index') }}" method="GET"
                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 items-end">
                {{-- Filter Stadt (Dropdown) --}}
                <div class="sm:col-span-2 md:col-span-2 lg:col-span-1">
                    <label for="filter-city" class="form-label text-xs">Stadt</label>
                    <select id="filter-city" name="city" class="form-select-field text-sm w-full mt-1">
                        <option value="">Alle Städte</option>
                        @foreach ($availableCities as $city)
                            <option value="{{ $city }}" {{ $cityFilter == $city ? 'selected' : '' }}>
                                {{ $city }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Filter Genre (Placeholder) --}}
                <div class="sm:col-span-2 md:col-span-2 lg:col-span-1">
                    <label for="filter-genre" class="form-label text-xs">Genre</label>
                    <select id="filter-genre" name="genre" class="form-select-field text-sm w-full mt-1" disabled>
                        {{-- Deaktiviert bis implementiert --}}
                        <option value="">Alle Genres (TODO)</option>
                    </select>
                </div>

                {{-- Sortierfeld --}}
                <div class="lg:col-start-4">
                    <label for="sort-clubs" class="form-label text-xs">Sortieren nach</label>
                    <select id="sort-clubs" name="sort" class="form-select-field text-sm w-full mt-1">
                        <option value="name-asc" {{ $sortValue == 'name-asc' ? 'selected' : '' }}>Name (A-Z)</option>
                        <option value="name-desc" {{ $sortValue == 'name-desc' ? 'selected' : '' }}>Name (Z-A)</option>
                        <option value="rating-desc" {{ $sortValue == 'rating-desc' ? 'selected' : '' }}>Bewertung
                            (Beste)</option>
                        <option value="rating-asc" {{ $sortValue == 'rating-asc' ? 'selected' : '' }}>Bewertung
                            (Schlechteste)</option>
                        {{-- NEU: Sortieren nach "Heute offen" --}}
                        <option value="open_today-asc" {{ $sortValue == 'open_today-asc' ? 'selected' : '' }}>Heute
                            offen (Zuerst)</option>
                        {{-- <option value="open_today-desc" {{ $sortValue == 'open_today-desc' ? 'selected' : '' }}>Heute geschlossen (Zuerst)</option> --}}
                    </select>
                </div>

                {{-- Submit/Reset Buttons --}}
                <div class="flex items-center space-x-2 lg:col-start-5">
                    <button type="submit" class="btn-primary w-full py-1.5 text-sm">Anwenden</button>
                    {{-- Reset nur anzeigen, wenn Filter/Sortierung aktiv ist --}}
                    @if (
                        (request()->hasAny(['city', 'genre', 'sort']) && request('sort', 'name-asc') !== 'name-asc') ||
                            request()->filled('city') ||
                            request()->filled('genre'))
                        <a href="{{ route('clubs.index') }}" class="btn-secondary w-full py-1.5 text-sm text-center"
                            title="Filter/Sortierung zurücksetzen">Reset</a>
                    @endif
                </div>
                {{-- Versteckte Felder für Parameter, die nicht im Formular sind --}}
                @foreach (request()->except(['city', 'genre', 'sort', 'page']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
            </form>
        </div>
        {{-- Ende Filter & Sortierung Bar --}}

        {{-- Club Liste Grid --}}
        @if ($clubs->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($clubs as $club)
                    {{-- Club Card --}}
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden flex flex-col group transition-shadow duration-200 hover:shadow-xl">
                        {{-- Bild Bereich --}}
                        <a href="{{ route('clubs.show', $club) }}"
                            class="block relative h-48 bg-gray-300 dark:bg-gray-700 group-hover:opacity-90 transition duration-150 ease-in-out">
                            {{-- Bild Placeholder --}}
                            <span
                                class="absolute inset-0 flex items-center justify-center text-gray-500 dark:text-gray-400 text-lg font-medium">Bild</span>
                            {{-- Verifiziert Badge (Optional) --}}
                            @if ($club->is_verified)
                                <span
                                    class="absolute top-2 right-2 bg-blue-500 text-white text-xs font-semibold px-2 py-0.5 rounded-full shadow"
                                    title="Verifiziert">✓</span>
                            @endif
                        </a>
                        {{-- Text Inhalt --}}
                        <div class="p-4 flex flex-col flex-grow">
                            {{-- Titel & Stadt --}}
                            <h3
                                class="text-lg font-semibold mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition duration-150 ease-in-out">
                                <a href="{{ route('clubs.show', $club) }}">
                                    {{ $club->name }}
                                </a>
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                                {{ $club->city ?? 'Ort unbekannt' }}</p>

                            {{-- Bewertungen mit halben Sternen & Dezimalstelle --}}
                            <div class="mb-2 flex items-center"
                                title="{{ $club->ratings_count > 0 ? number_format($club->average_rating ?? 0, 1) . ' / 5 (' . $club->ratings_count . ')' : 'Keine Wertungen' }}">
                                @php
                                    $ratingValue = $club->average_rating ?? 0;
                                    // Runde auf nächste 0.5 (z.B. 3.7 -> 3.5, 3.2 -> 3.0, 4.9 -> 5.0)
                                    $roundedRating = round($ratingValue * 2) / 2;
                                @endphp
                                @if ($club->ratings_count > 0)
                                    <div class="flex items-center text-yellow-400">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($roundedRating >= $i)
                                                {{-- Voller Stern --}}
                                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                                    <path
                                                        d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                                </svg>
                                            @elseif ($roundedRating >= $i - 0.5)
                                                {{-- Halber Stern (Linke Hälfte gefüllt) --}}
                                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                                    {{-- Pfad für die linke Hälfte --}}
                                                    <path
                                                        d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0v15z" />
                                                    {{-- Optional: Pfad für die leere rechte Hälfte (als Umriss) --}}
                                                    <path
                                                        d="M10 15l5.878 3.09-1.123-6.545L19.511 6.91l-6.572-.955L10 0v15z"
                                                        fill="none" class="text-gray-300 dark:text-gray-600"
                                                        stroke="currentColor" stroke-width="0" /> {{-- Stroke 0 macht ihn quasi unsichtbar, aber definiert ihn --}}
                                                </svg>
                                            @else
                                                {{-- Leerer Stern --}}
                                                <svg class="w-4 h-4 fill-current text-gray-300 dark:text-gray-600"
                                                    viewBox="0 0 20 20">
                                                    <path
                                                        d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                                </svg>
                                            @endif
                                        @endfor
                                    </div>
                                    {{-- Bewertungswert mit einer Dezimalstelle --}}
                                    <span class="ml-1.5 text-xs text-gray-500 dark:text-gray-400">
                                        {{ number_format($ratingValue, 1) }} ({{ $club->ratings_count }})
                                    </span>
                                @else
                                    {{-- Leere Sterne, wenn keine Bewertung --}}
                                    <div class="flex items-center text-gray-300 dark:text-gray-600">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                                <path
                                                    d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                            </svg>
                                        @endfor
                                    </div>
                                    <span class="ml-1.5 text-xs text-gray-400 italic">Keine</span>
                                @endif
                            </div>
                            {{-- Ende Bewertungen --}}

                            {{-- Öffnungsstatus Heute --}}
                            @php
                                $todayKey = date('D');
                                $openingHoursData = $club->opening_hours ?? [];
                                $statusHtml = '<span class="text-xs text-gray-400 dark:text-gray-500">Unbekannt</span>'; // Default
                                $statusColor = 'gray';
                                if (isset($openingHoursData[$todayKey])) {
                                    if ($openingHoursData[$todayKey] === 'closed') {
                                        $statusHtml = 'Heute geschlossen';
                                        $statusColor = 'red';
                                    } else {
                                        $statusHtml = 'Heute geöffnet';
                                        $statusColor = 'green';
                                    }
                                }
                            @endphp
                            <div class="text-xs mb-3 flex items-center">
                                <span @class([
                                    'w-2 h-2 rounded-full mr-1.5 flex-shrink-0',
                                    'bg-green-500' => $statusColor === 'green',
                                    'bg-red-500' => $statusColor === 'red',
                                    'bg-gray-400 dark:bg-gray-600' => $statusColor === 'gray',
                                ])></span>
                                <span @class([
                                    'font-medium',
                                    'text-green-700 dark:text-green-400' => $statusColor === 'green',
                                    'text-red-700 dark:text-red-400' => $statusColor === 'red',
                                    'text-gray-500 dark:text-gray-400' => $statusColor === 'gray',
                                ])>{!! $statusHtml !!}</span> {{-- Korrekte Ausgabe --}}
                            </div>
                            {{-- Ende Öffnungsstatus --}}

                            {{-- Genres --}}
                            @if ($club->genres->isNotEmpty())
                                <div class="text-xs mb-3 flex flex-wrap gap-1">
                                    @foreach ($club->genres->take(3) as $genre)
                                        <span
                                            class="inline-block bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2 py-0.5 rounded">
                                            {{ $genre->name }}
                                        </span>
                                    @endforeach
                                    @if ($club->genres->count() > 3)
                                        <span class="text-gray-400 dark:text-gray-500">...</span>
                                    @endif
                                </div>
                            @endif

                            {{-- Button unten --}}
                            <div class="mt-auto pt-3 border-t border-gray-200 dark:border-gray-700 text-right">
                                <a href="{{ route('clubs.show', $club) }}"
                                    class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                                    Details ansehen →
                                </a>
                            </div>
                        </div>
                    </div>
                    {{-- Ende Club Card --}}
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-12">
                {{-- Stelle sicher, dass die Query Parameter angehängt werden --}}
                {{ $clubs->appends(request()->query())->links() }}
            </div>
        @else
            {{-- Keine Clubs gefunden Nachricht --}}
            <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    aria-hidden="true">
                    <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 13h6m-3-3v6m-9 1V7a2 2 0 0 1 2-2h6l2 2h6a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z" />
                </svg>
                <h2 class="mt-2 text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">Keine Clubs gefunden</h2>
                <p class="text-gray-500 dark:text-gray-400">Es wurden keine Clubs gefunden, die den aktuellen Kriterien
                    entsprechen.</p>
                @if (request()->hasAny(['city', 'genre', 'sort']))
                    <div class="mt-6">
                        <a href="{{ route('clubs.index') }}" class="btn-secondary">Alle Filter zurücksetzen</a>
                    </div>
                @endif
            </div>
        @endif

    </div> {{-- Ende Container --}}

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const mapElement = document.getElementById('club-directory-map');
                const mapData = @json($mapData ?? []); // $mapData kommt jetzt vom Controller

                if (mapElement && typeof L !== 'undefined' && mapData.length > 0) { // Prüfe ob Leaflet (L) geladen ist
                    mapElement.innerHTML = ''; // Lade-Text entfernen

                    // Mittelpunkt (z.B. Deutschland oder erster Club)
                    const initialLat = mapData[0].lat || 51.16;
                    const initialLng = mapData[0].lng || 10.45;
                    const initialZoom = mapData.length === 1 ? 13 : 6; // Höherer Zoom, wenn nur ein Club

                    const map = L.map(mapElement).setView([initialLat, initialLng], initialZoom);

                    // CartoDB Dark Matter Tile Layer (oder ein anderer deiner Wahl)
                    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_nolabels/{z}/{x}/{y}{r}.png', {
                        attribution: '© <a href="https://www.openstreetmap.org/copyright">OSM</a> © <a href="https://carto.com/attributions">CARTO</a>',
                        subdomains: 'abcd',
                        maxZoom: 20
                    }).addTo(map);

                    const markerGroup = L.featureGroup().addTo(map);

                    mapData.forEach(item => {
                        if (item.lat && item.lng) {
                            const marker = L.marker([item.lat, item.lng])
                                .bindPopup(item.popupContent);
                            markerGroup.addLayer(marker);
                        }
                    });

                    if (markerGroup.getLayers().length > 0) {
                        map.fitBounds(markerGroup.getBounds(), {
                            padding: [40, 40],
                            maxZoom: 16
                        });
                    }
                } else if (mapElement && mapData.length === 0 && {{ $clubs->total() }} > 0) {
                    // Wenn Clubs in der Liste sind, aber keine mit Koordinaten für die Karte
                    mapElement.innerHTML =
                        '<div class="flex items-center justify-center h-full text-gray-500 italic">Keine Clubs mit Standortdaten für die Kartenansicht gefunden.</div>';
                } else if (mapElement) {
                    // Dieser Fall sollte nicht eintreten, wenn die section oben schon prüft
                }
            });
        </script>
    @endpush

</x-frontend-layout>
