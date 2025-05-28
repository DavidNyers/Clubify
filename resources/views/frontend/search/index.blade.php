<x-frontend-layout :title="$title" :description="$description">

    {{-- Header für die Seite --}}
    <div class="bg-gray-100 dark:bg-gray-800 py-8 border-b border-gray-200 dark:border-gray-700">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            @if ($searchTerm)
                <h1 class="text-2xl md:text-3xl font-semibold text-gray-900 dark:text-white">
                    Suchergebnisse für: "<span class="text-indigo-600 dark:text-indigo-400">{{ $searchTerm }}</span>"
                </h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ $resultCount }} {{ Str::plural('Ergebnis', $resultCount) }} gefunden.
                </p>
            @else
                <h1 class="text-2xl md:text-3xl font-semibold text-gray-900 dark:text-white">
                    Suche
                </h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Bitte gib einen Suchbegriff in die Suchleiste oben ein.
                </p>
            @endif
            {{-- Optional: Hier könnte die Suchleiste erneut platziert werden --}}
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @if ($searchTerm && $resultCount > 0)
            <div class="space-y-10">
                {{-- Clubs Results --}}
                @if ($results['clubs']->isNotEmpty())
                    <section>
                        <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">Gefundene Clubs
                            ({{ $results['clubs']->count() }})</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($results['clubs'] as $club)
                                {{-- Club Card (Auszug oder eigene Komponente) --}}
                                <div
                                    class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden flex flex-col group">
                                    <a href="{{ route('clubs.show', $club) }}"
                                        class="block relative h-40 bg-gray-300 dark:bg-gray-700 group-hover:opacity-90">
                                        {{-- Club Bild Placeholder --}}
                                        <span
                                            class="absolute inset-0 flex items-center justify-center text-gray-500 dark:text-gray-400">Club-Bild</span>
                                    </a>
                                    <div class="p-4">
                                        <h3
                                            class="text-lg font-semibold group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                                            <a href="{{ route('clubs.show', $club) }}">{{ $club->name }}</a></h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $club->city ?? 'Unbekannt' }}</p>
                                        {{-- Ggf. Genres des Clubs anzeigen --}}
                                        @if ($club->genres->isNotEmpty())
                                            <p class="text-xs mt-1 text-indigo-500 dark:text-indigo-400">
                                                {{ $club->genres->take(2)->pluck('name')->implode(', ') }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if ($results['clubs']->count() >= 10)
                            <p class="text-xs text-gray-500 mt-2 italic">(Zeige erste 10 Treffer für Clubs. Verfeinere
                                deine Suche oder besuche das Club-Verzeichnis.)</p>
                        @endif
                    </section>
                @endif

                {{-- Events Results --}}
                @if ($results['events']->isNotEmpty())
                    <section>
                        <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">Gefundene Events
                            ({{ $results['events']->count() }})</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($results['events'] as $event)
                                {{-- Event Card (Auszug oder eigene Komponente) --}}
                                <div
                                    class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden flex flex-col group">
                                    <a href="{{ route('events.show', $event) }}"
                                        class="block relative h-40 bg-gray-300 dark:bg-gray-700 group-hover:opacity-90">
                                        @if ($event->cover_image_path && Storage::disk('public')->exists($event->cover_image_path))
                                            <img src="{{ Storage::url($event->cover_image_path) }}"
                                                alt="{{ $event->name }}" class="w-full h-full object-cover">
                                        @else
                                            <span
                                                class="absolute inset-0 flex items-center justify-center text-gray-500 dark:text-gray-400">Event-Bild</span>
                                        @endif
                                        <div
                                            class="absolute top-2 left-2 bg-black/70 text-white px-2 py-0.5 rounded text-xs leading-tight shadow">
                                            {{ $event->start_time->translatedFormat('d M') }}</div>
                                    </a>
                                    <div class="p-4">
                                        <h3
                                            class="text-lg font-semibold group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                                            <a href="{{ route('events.show', $event) }}">{{ $event->name }}</a></h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $event->start_time->format('H:i') }} Uhr @
                                            {{ $event->club->name ?? 'Unbekannt' }}</p>
                                        @if ($event->genres->isNotEmpty())
                                            <p class="text-xs mt-1 text-indigo-500 dark:text-indigo-400">
                                                {{ $event->genres->take(2)->pluck('name')->implode(', ') }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if ($results['events']->count() >= 10)
                            <p class="text-xs text-gray-500 mt-2 italic">(Zeige erste 10 Treffer für Events. Verfeinere
                                deine Suche oder besuche das Event-Verzeichnis.)</p>
                        @endif
                    </section>
                @endif

                {{-- DJs Results --}}
                @if ($results['djs']->isNotEmpty())
                    <section>
                        <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">Gefundene DJs
                            ({{ $results['djs']->count() }})</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            @foreach ($results['djs'] as $djProfile)
                                {{-- DJ Card (Auszug oder eigene Komponente) --}}
                                <div class="text-center bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md group">
                                    <a href="{{ route('djs.show', $djProfile) }}" class="block mb-2">
                                        {{-- DJ Bild Placeholder --}}
                                        <div
                                            class="w-20 h-20 mx-auto rounded-full bg-gray-300 dark:bg-gray-700 mb-2 group-hover:ring-2 group-hover:ring-indigo-500 flex items-center justify-center">
                                            <svg class="w-10 h-10 text-gray-400" fill="currentColor"
                                                viewBox="0 0 24 24">...</svg>
                                        </div>
                                        <h3
                                            class="text-md font-semibold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                                            {{ $djProfile->displayName }}</h3>
                                    </a>
                                    {{-- <p class="text-xs text-indigo-500 dark:text-indigo-400">Genres...</p> --}}
                                </div>
                            @endforeach
                        </div>
                        @if ($results['djs']->count() >= 10)
                            <p class="text-xs text-gray-500 mt-2 italic">(Zeige erste 10 Treffer für DJs. Verfeinere
                                deine Suche oder besuche das DJ-Verzeichnis.)</p>
                        @endif
                    </section>
                @endif

            </div>
        @elseif($searchTerm)
            <div class="text-center py-16">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">Keine Ergebnisse</h2>
                <p class="text-gray-500 dark:text-gray-400">Für deine Suche "<span
                        class="font-medium">{{ $searchTerm }}</span>" wurden leider keine Ergebnisse gefunden.</p>
                <p class="mt-4"><a href="{{ route('home') }}" class="btn-primary">Zurück zur Startseite</a></p>
            </div>
        @endif
    </div>

</x-frontend-layout>
