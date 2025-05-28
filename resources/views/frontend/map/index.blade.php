<x-frontend-layout :title="$title" :description="$description">

    {{-- Hauptcontainer für Sidenav und Karte --}}
    <div class="w-full h-[calc(100vh-4rem)] flex relative overflow-hidden" x-data="{ showFilters: window.innerWidth >= 1024 }"> {{-- lg: 1024px für Desktop-Sidenav --}}

        {{-- Filter Sidenav (links) --}}
        <aside
            class="absolute lg:relative inset-y-0 left-0 z-30 w-72 sm:w-80 bg-white dark:bg-gray-800 shadow-xl transform transition-transform duration-300 ease-in-out overflow-y-auto p-5 space-y-4 border-r border-gray-200 dark:border-gray-700"
            :class="{ 'translate-x-0': showFilters, '-translate-x-full': !showFilters && window.innerWidth < 1024 }"
            x-show="showFilters" @click.outside="if(window.innerWidth < 1024) showFilters = false" x-cloak>
            <div class="flex justify-between items-center mb-3">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Filter & Suche</h2>
                <button @click="showFilters = false" class="lg:hidden text-gray-500 ...">×</button>
            </div>

            <form id="map-filter-form" action="{{ route('map.index') }}" method="GET">
                {{-- Behalte bestehende Parameter bei --}}
                @foreach (request()->except(['search_term', 'type', 'country', 'city', 'date_option', 'custom_date_from', 'custom_date_to', 'open_today', 'genres', 'page']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach

                {{-- 1. Allgemeine Suche --}}
                <div>
                    <label for="search_term" class="form-label text-sm">Allgemeine Suche:</label>
                    <input type="search" name="search_term" id="search_term" value="{{ $searchTerm ?? '' }}"
                        placeholder="Name, Ort, Beschreibung..." class="form-input-field w-full mt-1 text-sm">
                </div>

                {{-- 2. Land Filter --}}
                <div>
                    <label for="filter-country" class="form-label text-sm">Land:</label>
                    <select name="country" id="filter-country" class="form-select-field w-full mt-1 text-sm">
                        <option value="">Alle Länder</option>
                        @foreach ($countriesForFilter as $code => $name)
                            <option value="{{ $code }}" {{ ($filterCountry ?? '') == $code ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- 3. Stadt Filter --}}
                <div>
                    <label for="filter-city" class="form-label text-sm">Stadt:</label>
                    <select name="city" id="filter-city" class="form-select-field w-full mt-1 text-sm">
                        <option value="">Alle Städte im Land</option>
                        @foreach ($availableCities as $city)
                            <option value="{{ $city }}" {{ ($filterCity ?? '') == $city ? 'selected' : '' }}>
                                {{ $city }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- 4. "Meinen Standort verwenden" Button --}}
                <div>
                    <button type="button" id="use-current-location-btn"
                        class="mt-2 text-xs text-indigo-600 dark:text-indigo-400 hover:underline focus:outline-none">
                        <svg class="w-3 h-3 inline-block mr-1" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">...</svg>
                        Meinen aktuellen Standort verwenden
                    </button>
                    <p id="location-status" class="text-xs text-gray-500 dark:text-gray-400 mt-1"></p>
                </div>

                {{-- Trennlinie --}}
                <hr class="dark:border-gray-700 my-4">

                {{-- 5. Typ Filter (Clubs/Events) --}}
                <div>
                    <label for="filter-type" class="form-label text-sm">Anzeigen:</label>
                    <select name="type" id="filter-type" class="form-select-field w-full mt-1 text-sm">
                        <option value="all" {{ ($filterType ?? 'all') == 'all' ? 'selected' : '' }}>Clubs & Events
                        </option>
                        <option value="clubs" {{ ($filterType ?? '') == 'clubs' ? 'selected' : '' }}>Nur Clubs
                        </option>
                        <option value="events" {{ ($filterType ?? '') == 'events' ? 'selected' : '' }}>Nur Events
                        </option>
                    </select>
                </div>

                {{-- 6. Datumsfilter (für Events) --}}
                <div x-data="{ dateOption: '{{ $filterDateOption ?? 'any' }}' }">
                    <label for="filter-date-option" class="form-label text-sm">Datum (Events):</label>
                    <select name="date_option" id="filter-date-option" x-model="dateOption"
                        class="form-select-field w-full mt-1 text-sm">
                        <option value="any">Jederzeit (Zukünftig)</option>
                        <option value="today">Heute</option>
                        <option value="tomorrow">Morgen</option>
                        <option value="this_weekend">Dieses Wochenende</option>
                        <option value="custom">Datumsbereich</option>
                    </select>
                    <div x-show="dateOption === 'custom'"
                        class="mt-2 space-y-2 bg-gray-50 dark:bg-gray-700/50 p-2 rounded-md" x-transition>
                        <label for="custom_date_from" class="form-label text-xs">Von:</label>
                        <input type="date" name="custom_date_from" id="custom_date_from"
                            value="{{ $filterCustomDateFrom ?? '' }}" class="form-input-field w-full text-sm">
                        <label for="custom_date_to" class="form-label text-xs mt-1">Bis:</label>
                        <input type="date" name="custom_date_to" id="custom_date_to"
                            value="{{ $filterCustomDateTo ?? '' }}" class="form-input-field w-full text-sm">
                    </div>
                </div>

                {{-- 7. "Heute geöffnet" (für Clubs) --}}
                <div class="mt-2">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="open_today" value="1" class="form-checkbox-field"
                            {{ $filterOpenToday ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700 dark:text-gray-300">Nur heute geöffnete Clubs</span>
                    </label>
                </div>

                {{-- Trennlinie --}}
                <hr class="dark:border-gray-700 my-4">

                {{-- 8. Genre Filter --}}
                <div>
                    <label class="form-label text-sm">Genres:</label>
                    <div
                        class="mt-1 max-h-40 overflow-y-auto space-y-1 border border-gray-300 dark:border-gray-600 rounded-md p-2
                                bg-white dark:bg-white">
                        {{-- Explizit weißer Hintergrund --}}
                        @forelse($availableGenres as $genre)
                            <label
                                class="flex items-center space-x-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-200 p-1 rounded">
                                {{-- Heller Hover --}}
                                <input type="checkbox" name="genres[]" value="{{ $genre->slug }}"
                                    class="h-4 w-4 rounded border-gray-300 dark:border-gray-400 text-indigo-600 focus:ring-indigo-500 bg-gray-100 dark:bg-gray-200 checked:bg-indigo-600 dark:checked:bg-indigo-500"
                                    {{-- Nutzt die form-checkbox-field Logik, aber mit angepasstem BG für hellen Kontext --}}
                                    {{ in_array($genre->slug, $filterGenres ?? []) ? 'checked' : '' }}>
                                <span class="text-sm text-gray-800 dark:text-gray-800">{{ $genre->name }}</span>
                                {{-- Immer dunkler Text --}}
                            </label>
                        @empty
                            <p class="text-xs italic text-gray-500 dark:text-gray-500 p-1">Keine spezifischen Genres für
                                aktuelle Auswahl.</p> {{-- Angepasste Textfarbe für Lesbarkeit auf hellem Grund --}}
                        @endforelse
                    </div>
                    @error('genres')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                    @error('genres.*')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Buttons --}}
                <div class="pt-5 mt-3 border-t border-gray-200 dark:border-gray-700 space-y-3">
                    <button type="submit" class="btn-primary w-full text-sm">Filter anwenden</button>
                    <a href="{{ route('map.index') }}" class="btn-secondary w-full text-center block text-sm">Alle
                        Filter zurücksetzen</a>
                </div>
            </form>
        </aside>
        {{-- Ende Filter Sidenav --}}


        {{-- Hauptbereich für die Karte --}}
        <div class="flex-1 h-full relative">
            {{-- Button zum Öffnen der Filter-Sidenav auf Mobile --}}
            <button @click="showFilters = !showFilters" title="Filter anzeigen/ausblenden"
                class="lg:hidden absolute top-4 left-4 z-40 bg-white dark:bg-gray-800 p-2.5 rounded-md shadow-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <svg x-show="!showFilters" class="w-6 h-6" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
                <svg x-show="showFilters" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>

            <div id="fullpage-map" class="w-full h-full z-0"> {{-- z-0 ist wichtig, damit Sidenav-Toggle darüber liegt --}}
                <div class="flex items-center justify-center h-full bg-gray-200 dark:bg-gray-700 text-gray-500 italic">
                    Karte wird geladen...</div>
            </div>
        </div>
        {{-- Ende Hauptbereich Karte --}}

    </div>

    {{-- In resources/views/frontend/map/index.blade.php --}}

    {{-- ... (HTML für Sidenav und Karten-Div wie zuvor) ... --}}

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const mapElement = document.getElementById('fullpage-map');
                const mapData = @json($mapData ?? []); // Daten vom Controller
                let mapInstance = null; // Hält die Leaflet Karteninstanz

                const useCurrentLocationButton = document.getElementById('use-current-location-btn');
                const locationStatusElement = document.getElementById('location-status');

                // Hilfsfunktion zum Anzeigen von Nachrichten auf der Karte
                function displayMapMessage(message) {
                    if (mapElement) {
                        mapElement.innerHTML =
                            `<div class="flex items-center justify-center h-full text-gray-500 dark:text-gray-400 italic p-4 text-center">${message}</div>`;
                    }
                }

                // Hilfsfunktion zum Aktualisieren der Statusmeldung für Geolocation
                function updateLocationStatus(message, isError = false) {
                    if (locationStatusElement) {
                        locationStatusElement.textContent = message;
                        locationStatusElement.className = 'mt-1 text-xs ' + (isError ?
                            'text-red-500 dark:text-red-400' : 'text-green-600 dark:text-green-400');
                    }
                }

                // Funktion zum Hinzufügen von Markern zur Karte
                function addMarkersToMap(data, group) {
                    group.clearLayers(); // Bestehende Marker aus der Gruppe löschen
                    data.forEach(item => {
                        if (item.lat && item.lng) {
                            let currentIcon;
                            // Beispiel für unterschiedliche Icons (benötigt SVG-Dateien in public/images/)
                            // if (item.type === 'club') {
                            //     currentIcon = L.icon({ iconUrl: '/images/map-marker-club.svg', iconSize: [28, 28], iconAnchor: [14, 28], popupAnchor: [0, -28] });
                            // } else if (item.type === 'event') {
                            //     currentIcon = L.icon({ iconUrl: '/images/map-marker-event.svg', iconSize: [28, 28], iconAnchor: [14, 28], popupAnchor: [0, -28] });
                            // }

                            const marker = L.marker([item.lat, item.lng], {
                                    // icon: currentIcon, // Hier das Icon setzen, falls definiert
                                    title: item.title // Für Hover-Tooltip
                                })
                                .bindPopup(item.popupContent, {
                                    maxWidth: 250,
                                    minWidth: 200
                                });
                            group.addLayer(marker);
                        }
                    });
                }

                // Hauptfunktion zur Initialisierung und Aktualisierung der Karte
                function initializeOrUpdateMap() {
                    if (!mapElement || typeof L === 'undefined') {
                        displayMapMessage('Fehler beim Laden der Karten-Bibliothek.');
                        return;
                    }

                    // Wenn Karte noch nicht existiert, initialisiere sie
                    if (!mapInstance) {
                        mapElement.innerHTML = ''; // Lade-Text entfernen
                        let initialView = [51.16, 10.45]; // Deutschland als Standard
                        let initialZoom = 6;

                        mapInstance = L.map(mapElement, {
                            // scrollWheelZoom: false // Optional
                        }).setView(initialView, initialZoom);

                        // mapInstance.on('focus', function() { mapInstance.scrollWheelZoom.enable(); });
                        // mapInstance.on('blur', function() { mapInstance.scrollWheelZoom.disable(); });


                        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_nolabels/{z}/{x}/{y}{r}.png', {
                            attribution: '© <a href="https://www.openstreetmap.org/copyright">OSM</a> © <a href="https://carto.com/attributions">CARTO</a>',
                            subdomains: 'abcd',
                            maxZoom: 19
                        }).addTo(mapInstance);

                        mapInstance.options.manualZoomSet = false; // Initialisiere das Flag
                    }

                    // Marker (neu) hinzufügen oder aktualisieren
                    const markerGroup = L.featureGroup().addTo(
                        mapInstance); // Immer neu erstellen oder bestehende leeren
                    addMarkersToMap(mapData, markerGroup);

                    if (markerGroup.getLayers().length > 0) {
                        // Nur fitten, wenn nicht gerade durch Geolocation ein View gesetzt wurde
                        if (!mapInstance.options.manualZoomSet) {
                            mapInstance.fitBounds(markerGroup.getBounds(), {
                                padding: [60, 60],
                                maxZoom: 16
                            });
                        }
                        mapInstance.options.manualZoomSet =
                            false; // Flag für den nächsten normalen Ladevorgang zurücksetzen
                    } else if (mapData.length === 0 && Object.keys({{ Js::from(request()->except('page')) }}).length >
                        0 && !(Object.keys({{ Js::from(request()->except('page')) }}).length === 1 &&
                            {{ Js::from(request()->except('page')) }}.hasOwnProperty('_token'))) {
                        displayMapMessage(
                            'Keine Einträge für die aktuellen Filter gefunden. Versuche andere Filteroptionen.');
                    } else if (mapData.length === 0) {
                        displayMapMessage('Keine Einträge für die Karte gefunden.');
                    }
                }

                // Initialisiere die Karte beim Laden der Seite
                initializeOrUpdateMap();

                // Event Listener für "Meinen Standort verwenden"-Button
                if (useCurrentLocationButton) {
                    useCurrentLocationButton.addEventListener('click', function() {
                        if (!navigator.geolocation) {
                            updateLocationStatus('Geolocation wird von deinem Browser nicht unterstützt.',
                                true);
                            return;
                        }

                        updateLocationStatus('Standort wird ermittelt...', false);
                        this.disabled = true; // Deaktiviere den Button

                        navigator.geolocation.getCurrentPosition(
                            (position) => {
                                const userLat = position.coords.latitude;
                                const userLng = position.coords.longitude;

                                updateLocationStatus(`Standort gefunden! Karte zentriert.`, false);
                                if (mapInstance) {
                                    mapInstance.setView([userLat, userLng],
                                        13); // Zentriere Karte und zoome näher ran
                                    mapInstance.options.manualZoomSet =
                                        true; // Setze Flag, dass Zoom/View manuell gesetzt wurde
                                }
                                this.disabled = false; // Aktiviere Button wieder
                            },
                            (error) => {
                                let message = 'Standort konnte nicht ermittelt werden.';
                                if (error.code === error.PERMISSION_DENIED) message =
                                    'Standortzugriff verweigert.';
                                else if (error.code === error.POSITION_UNAVAILABLE) message =
                                    'Standortinformation ist nicht verfügbar.';
                                else if (error.code === error.TIMEOUT) message =
                                    'Timeout bei der Standortabfrage.';
                                updateLocationStatus(message, true);
                                this.disabled = false; // Aktiviere Button wieder
                            }, {
                                enableHighAccuracy: false,
                                timeout: 10000,
                                maximumAge: 0
                            }
                        );
                    });
                }
            });
        </script>
    @endpush
</x-frontend-layout>
