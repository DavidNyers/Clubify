<x-frontend-layout :title="$title" :description="$description">

    {{-- === Hero Section === --}}
    <section
        class="relative bg-gray-900 text-white min-h-[60vh] md:min-h-[75vh] flex items-center justify-center overflow-hidden">
        {{-- Hintergrundbild/-video (Beispiel mit Bild) --}}
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/hero-background.jpg') }}" {{-- Passe den Pfad an --}} alt="Clubify Nightlife Experience"
                class="w-full h-full object-cover opacity-40 dark:opacity-30">
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/70 to-transparent"></div>
        </div>

        <div class="container relative z-10 mx-auto px-4 sm:px-6 lg:px-8 text-center py-16 md:py-24">
            <h1
                class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-extrabold mb-6 leading-tight animate-fade-in-down">
                <span class="block">Dein<span class="text-indigo-400"> Nightlife.</span></span>
                <span class="block mt-2">Deine<span class="text-pink-400"> Regeln.</span></span>
            </h1>
            <p class="text-lg md:text-xl mb-10 max-w-2xl mx-auto text-gray-300 animate-fade-in-up animation-delay-300">
                Tauche ein in die Welt der besten Clubs, unvergesslichsten Events und angesagtesten DJs.
            </p>
            <div
                class="flex flex-col sm:flex-row justify-center items-center gap-4 animate-fade-in-up animation-delay-600">
                <a href="{{ route('events.index') }}"
                    class="btn-primary !bg-indigo-600 hover:!bg-indigo-500 !px-10 !py-3 !text-lg !font-semibold !rounded-lg !shadow-lg transform hover:scale-105 transition-transform duration-150">
                    Events entdecken
                </a>
                <a href="{{ route('clubs.index') }}"
                    class="btn-secondary !bg-white/10 hover:!bg-white/20 !border-white/30 !text-white !px-10 !py-3 !text-lg !font-semibold !rounded-lg !shadow-lg transform hover:scale-105 transition-transform duration-150">
                    Clubs finden
                </a>
            </div>
        </div>
    </section>
    {{-- === /Hero Section === --}}

    {{-- Hauptcontainer für weitere Sektionen --}}
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16 space-y-16 md:space-y-20">

        {{-- === "Nächste Events" Sektion === --}}
        @if ($upcomingEvents->isNotEmpty())
            <section id="upcoming-events">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white">Nächste Events</h2>
                    <a href="{{ route('events.index') }}" class="btn-secondary !text-sm">Alle Events</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($upcomingEvents as $event)
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden flex flex-col group transform hover:-translate-y-1 transition-all duration-300">
                            <a href="{{ route('events.show', $event) }}"
                                class="block relative h-48 bg-gray-300 dark:bg-gray-700 group-hover:opacity-90">
                                @if ($event->cover_image_path && Storage::disk('public')->exists($event->cover_image_path))
                                    <img src="{{ Storage::url($event->cover_image_path) }}" alt="{{ $event->name }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <div
                                        class="w-full h-full flex items-center justify-center bg-gradient-to-br from-purple-500 to-pink-500 text-white text-xl font-semibold">
                                        {{ Str::limit($event->name, 20) }}
                                    </div>
                                @endif
                                <div
                                    class="absolute top-2 left-2 bg-black/70 text-white px-2.5 py-1 rounded text-center leading-tight shadow-lg">
                                    <span
                                        class="text-xs uppercase font-semibold tracking-wide">{{ $event->start_time->translatedFormat('M') }}</span><br>
                                    <span class="text-lg font-bold">{{ $event->start_time->format('d') }}</span>
                                </div>
                            </a>
                            <div class="p-4 flex flex-col flex-grow">
                                <h3 class="text-lg font-semibold mb-1">
                                    <a href="{{ route('events.show', $event) }}"
                                        class="hover:text-indigo-600 dark:hover:text-indigo-400">{{ $event->name }}</a>
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                                    <svg class="w-3.5 h-3.5 inline-block mr-1 -mt-0.5" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.414-1.415L11 9.586V6z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $event->start_time->translatedFormat('D') }},
                                    {{ $event->start_time->format('H:i') }} Uhr
                                </p>
                                @if ($event->club)
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                                        <svg class="w-3.5 h-3.5 inline-block mr-1 -mt-0.5" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        <a href="{{ route('clubs.show', $event->club) }}"
                                            class="hover:underline">{{ $event->club->name }}</a>,
                                        {{ $event->club->city }}
                                    </p>
                                @endif
                                @if ($event->genres->isNotEmpty())
                                    <div class="text-xs mb-3 flex flex-wrap gap-1">
                                        @foreach ($event->genres->take(2) as $genre)
                                            <span
                                                class="badge-default bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300">{{ $genre->name }}</span>
                                        @endforeach
                                        @if ($event->genres->count() > 2)
                                            <span class="badge-default bg-gray-200 dark:bg-gray-700">...</span>
                                        @endif
                                    </div>
                                @endif
                                <div class="mt-auto pt-3 border-t border-gray-200 dark:border-gray-700 text-right">
                                    <a href="{{ route('events.show', $event) }}"
                                        class="btn-secondary !text-xs !py-1.5 !px-3">Details</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
        {{-- === /Nächste Events === --}}


        {{-- === Interaktive Karte (beibehalten) === --}}
        @if ($mapData->count() > 0)
            <section id="map-section">
                <h2 class="text-3xl md:text-4xl font-bold mb-8 text-gray-900 dark:text-white text-center">Entdecke auf
                    der Karte</h2>
                <div id="homepage-map"
                    class="w-full h-96 md:h-[500px] rounded-lg shadow-xl bg-gray-200 dark:bg-gray-700 z-0 overflow-hidden">
                    <div class="flex items-center justify-center h-full text-gray-500 italic">Karte wird geladen...
                    </div>
                </div>
            </section>
        @endif
        {{-- === /Interaktive Karte === --}}

        {{-- === Featured Clubs === --}}
        @if ($featuredClubs->isNotEmpty())
            <section id="featured-clubs">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white">Top Clubs</h2>
                    <a href="{{ route('clubs.index') }}" class="btn-secondary !text-sm">Alle Clubs</a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($featuredClubs as $club)
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden flex flex-col group transform hover:-translate-y-1 transition-all duration-300">
                            <a href="{{ route('clubs.show', $club) }}"
                                class="block relative h-40 bg-gray-300 dark:bg-gray-700 group-hover:opacity-90">
                                {{-- TODO: Club-Bild laden (erstes Galeriebild oder dediziertes Cover) --}}
                                <div
                                    class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-400 to-gray-600 text-white font-semibold">
                                    Club-Bild
                                </div>
                                @if ($club->is_verified)
                                    <span
                                        class="absolute top-2 right-2 bg-blue-500 text-white text-xs px-2 py-0.5 rounded-full shadow-md"
                                        title="Verifiziert">✓</span>
                                @endif
                            </a>
                            <div class="p-4 flex-grow">
                                <h3
                                    class="font-semibold text-lg group-hover:text-indigo-600 dark:group-hover:text-indigo-400 mb-0.5">
                                    <a href="{{ route('clubs.show', $club) }}">{{ $club->name }}</a>
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">{{ $club->city }}</p>
                                {{-- Rating --}}
                                @if ($club->ratings_count > 0)
                                    <div class="flex items-center text-xs text-yellow-400 mb-2"
                                        title="{{ number_format($club->average_rating ?? 0, 1) }} / 5">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <svg class="w-3.5 h-3.5 fill-current {{ $i <= round($club->average_rating ?? 0) ? '' : 'text-gray-300 dark:text-gray-600' }}"
                                                viewBox="0 0 20 20">
                                                <path
                                                    d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                            </svg>
                                        @endfor
                                        <span
                                            class="ml-1 text-gray-500 dark:text-gray-400">({{ $club->ratings_count }})</span>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4 pt-0 mt-auto text-right">
                                <a href="{{ route('clubs.show', $club) }}"
                                    class="btn-secondary !text-xs !py-1.5 !px-3">Details</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
        {{-- === /Featured Clubs === --}}

        {{-- === Featured DJs === --}}
        @if ($featuredDjs->isNotEmpty())
            <section id="featured-djs">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white">Angesagte DJs</h2>
                    <a href="{{ route('djs.index') }}" class="btn-secondary !text-sm">Alle DJs</a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach ($featuredDjs as $djProfile)
                        <div
                            class="text-center bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg group transform hover:scale-105 transition-transform duration-300">
                            <a href="{{ route('djs.show', $djProfile) }}" class="block mb-3">
                                {{-- TODO: DJ Profilbild --}}
                                <div
                                    class="w-24 h-24 mx-auto rounded-full bg-gray-300 dark:bg-gray-700 mb-3 group-hover:ring-4 group-hover:ring-indigo-500 dark:group-hover:ring-indigo-400 transition-all flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="currentColor"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z">
                                        </path>
                                    </svg>
                                </div>
                                <h3
                                    class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                                    {{ $djProfile->displayName }}</h3>
                            </a>
                            {{-- TODO: Hauptgenres des DJs --}}
                            <p class="text-sm text-indigo-500 dark:text-indigo-400">Techno, Deep House</p>
                            @if ($djProfile->is_verified)
                                <span
                                    class="mt-2 inline-block bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300 text-xs px-2 py-0.5 rounded-full">Verifiziert</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
        {{-- === /Featured DJs === --}}


        {{-- Call to Action / Werde Partner (Placeholder) --}}
        <section class="mt-16 md:mt-24 py-12 md:py-16 bg-gray-100 dark:bg-gray-800 rounded-lg shadow-inner">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Mach mit bei Clubify!</h2>
                <p class="text-lg text-gray-600 dark:text-gray-300 mb-8 max-w-xl mx-auto">
                    Bist du Club-Besitzer, Veranstalter oder DJ? Präsentiere dich auf Clubify und erreiche tausende
                    Nightlife-Enthusiasten.
                </p>
                <a href="#"
                    class="btn-primary !bg-pink-600 hover:!bg-pink-500 !px-10 !py-3 !text-lg !font-semibold !rounded-lg !shadow-lg">Jetzt
                    Partner werden (TODO)</a>
            </div>
        </section>

    </div> {{-- Ende Hauptcontainer --}}


    @push('scripts')
        <script>
            // Das Karten-Initialisierungs-Script (unverändert)
            document.addEventListener('DOMContentLoaded', function() {
                const mapElement = document.getElementById('homepage-map');
                const mapData = @json($mapData ?? []);
                if (mapElement && typeof L !== 'undefined' && mapData.length > 0) {
                    mapElement.innerHTML = '';
                    const initialLat = mapData[0].lat || 51.16; // Verwende ersten Punkt oder Fallback
                    const initialLng = mapData[0].lng || 10.45;
                    const initialZoom = mapData.length === 1 ? 12 :
                        6; // Zoom näher, wenn nur 1 Punkt oder nach Geolocation
                    const map = L.map(mapElement).setView([initialLat, initialLng], initialZoom);
                    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_nolabels/{z}/{x}/{y}{r}.png', {
                        /* ... */
                    }).addTo(map);
                    const markerGroup = L.featureGroup().addTo(map);
                    mapData.forEach(item => {
                        /* ... Marker hinzufügen ... */
                    });
                    if (markerGroup.getLayers().length > 0) {
                        map.fitBounds(markerGroup.getBounds(), {
                            padding: [50, 50],
                            maxZoom: 15
                        });
                    }
                    // Geolocation Logik wie gehabt, aber optional für Zentrierung
                    if ('geolocation' in navigator) {
                        navigator.geolocation.getCurrentPosition((position) => {
                            map.setView([position.coords.latitude, position.coords.longitude], 13);
                            // L.marker([position.coords.latitude, position.coords.longitude]).addTo(map).bindPopup("Dein Standort").openPopup();
                        }, () => {
                            /* Fehler oder Ablehnung */
                        }, {
                            timeout: 8000
                        });
                    }
                } else if (mapElement) {
                    mapElement.innerHTML =
                        '<div class="flex items-center justify-center h-full text-gray-500 italic">Keine Kartendaten verfügbar.</div>';
                }
            });
        </script>
    @endpush

</x-frontend-layout>


<style>
    .animate-fade-in-down {
        animation: fadeInDown 0.8s ease-out forwards;
    }

    .animate-fade-in-up {
        animation: fadeInUp 0.8s ease-out forwards;
    }

    .animation-delay-300 {
        animation-delay: 0.3s;
        opacity: 0;
    }

    .animation-delay-600 {
        animation-delay: 0.6s;
        opacity: 0;
    }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Badge Default Styling */
    .badge-default {
        @apply inline-block px-2.5 py-0.5 rounded-full text-xs font-medium;
    }
</style>
