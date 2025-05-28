<x-frontend-layout :title="$title" :description="$description">

    {{-- Header für die Seite --}}
    <div class="bg-gray-200 dark:bg-gray-800 py-8">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $title }}</h1>
            {{-- Optional: Breadcrumbs --}}
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">

        {{-- Filter & Sortierung Bar --}}
        <div class="mb-8 p-4 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <form id="filter-sort-form" action="{{ route('events.index') }}" method="GET"
                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 items-end">
                {{-- Genre Filter --}}
                <div class="sm:col-span-1 md:col-span-1 lg:col-span-1">
                    <label for="filter-genre" class="form-label text-xs">Genre</label>
                    <select id="filter-genre" name="genre" class="form-select-field text-sm w-full mt-1">
                        <option value="">Alle Genres</option>
                        @foreach ($availableGenres as $genre)
                            <option value="{{ $genre->slug }}"
                                {{ ($genreFilter ?? '') == $genre->slug ? 'selected' : '' }}>
                                {{ $genre->name }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Stadt Filter --}}
                <div class="sm:col-span-1 md:col-span-1 lg:col-span-1">
                    <label for="filter-city" class="form-label text-xs">Stadt</label>
                    <select id="filter-city" name="city" class="form-select-field text-sm w-full mt-1">
                        <option value="">Alle Städte</option>
                        @foreach ($availableCities as $city)
                            <option value="{{ $city }}" {{ ($cityFilter ?? '') == $city ? 'selected' : '' }}>
                                {{ $city }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Filter Datum --}}
                <div class="sm:col-span-1 md:col-span-1 lg:col-span-1">
                    <label for="filter-date" class="form-label text-xs">Datum</label>
                    <input type="date" id="filter-date" name="date" value="{{ $dateFilter ?? '' }}"
                        class="form-input-field text-sm w-full mt-1">
                </div>

                {{-- Sortierfeld --}}
                <div class="lg:col-start-4">
                    <label for="sort-events" class="form-label text-xs">Sortieren nach</label>
                    <select id="sort-events" name="sort" class="form-select-field text-sm w-full mt-1">
                        <option value="date-asc" {{ ($sortValue ?? 'date-asc') == 'date-asc' ? 'selected' : '' }}>Datum
                            (Nächste)</option>
                        <option value="date-desc" {{ ($sortValue ?? 'date-asc') == 'date-desc' ? 'selected' : '' }}>
                            Datum (Neueste Einträge)</option>
                        <option value="name-asc" {{ ($sortValue ?? 'date-asc') == 'name-asc' ? 'selected' : '' }}>Name
                            (A-Z)</option>
                        <option value="name-desc" {{ ($sortValue ?? 'date-asc') == 'name-desc' ? 'selected' : '' }}>
                            Name (Z-A)</option>
                    </select>
                </div>

                {{-- Submit/Reset Buttons --}}
                <div class="flex items-center space-x-2 lg:col-start-5">
                    <button type="submit" class="btn-primary w-full py-1.5 text-sm">Anwenden</button>
                    @if (request()->hasAny(['genre', 'city', 'date', 'sort']))
                        <a href="{{ route('events.index') }}" class="btn-secondary w-full py-1.5 text-sm text-center"
                            title="Filter/Sortierung zurücksetzen">Reset</a>
                    @endif
                </div>
                @foreach (request()->except(['genre', 'city', 'date', 'sort', 'page']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
            </form>
        </div>
        {{-- Ende Filter & Sortierung Bar --}}


        {{-- Event Liste Grid --}}
        @if ($events->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($events as $event)
                    {{-- Event Card mit Bookmark-Button --}}
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden flex flex-col group transform hover:-translate-y-1 transition-all duration-300">
                        {{-- Bild Bereich mit Bookmark Button oben rechts --}}
                        <div
                            class="block relative h-48 bg-gray-300 dark:bg-gray-700 group-hover:opacity-90 transition-opacity duration-150 ease-in-out overflow-hidden rounded-t-lg">
                            <a href="{{ route('events.show', $event) }}" class="block w-full h-full">
                                @if ($event->cover_image_path && Storage::disk('public')->exists($event->cover_image_path))
                                    <img src="{{ Storage::url($event->cover_image_path) }}" alt="{{ $event->name }}"
                                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                                @else
                                    <div
                                        class="w-full h-full flex items-center justify-center bg-gradient-to-br from-purple-500 to-pink-500 text-white text-xl font-semibold p-4 text-center">
                                        {{ Str::limit($event->name, 30) }}
                                    </div>
                                @endif
                            </a>
                            {{-- Datum Badge --}}
                            <div
                                class="absolute top-2 left-2 bg-black/70 text-white px-2.5 py-1 rounded text-center leading-tight shadow-lg">
                                <span
                                    class="text-xs uppercase font-semibold tracking-wide">{{ $event->start_time->translatedFormat('M') }}</span><br>
                                <span class="text-lg font-bold">{{ $event->start_time->format('d') }}</span>
                            </div>

                            {{-- Bookmark-Button --}}
                            @auth
                                <div x-data="{
                                    initialIsBookmarked: {{ Auth::user()->bookmarkedEvents()->where('event_id', $event->id)->exists() ? 'true' : 'false' }},
                                    isBookmarked: false,
                                    {{-- Wird in init gesetzt --}}
                                    isLoading: false,
                                    message: ''
                                }" x-init="isBookmarked = initialIsBookmarked" {{-- Setze den initialen Zustand basierend auf PHP --}}
                                    class="absolute top-2 right-2 z-10">
                                    <button
                                        @click="
            if (!{{ Auth::check() ? 'true' : 'false' }}) { window.location.href = '{{ route('login') }}'; return; }
            isLoading = true;
            message = '';
            fetch('{{ route('events.bookmark.toggle', $event) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
            })
            .then(response => {
                if (!response.ok) { throw new Error('Network response was not ok.'); }
                return response.json();
            })
            .then(data => {
                isBookmarked = data.bookmarked; // Aktualisiere den Alpine-Status
                message = data.message;
                // console.log('New bookmark status:', isBookmarked, 'Message:', message); // Zum Debuggen
            })
            .catch(error => {
                message = 'Fehler beim Bookmarken.';
                console.error('Bookmark Error:', error);
            })
            .finally(() => {
                isLoading = false;
                // Optional: Message nach einiger Zeit ausblenden
                // setTimeout(() => message = '', 3000);
            });
        "
                                        :disabled="isLoading" type="button"
                                        class="p-2 rounded-full bg-white/80 dark:bg-gray-900/80 hover:bg-white dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-offset-black transition-colors shadow"
                                        :title="isBookmarked ? 'Event von Merkliste entfernen' : 'Event merken'">

                                        {{-- Ladeanzeige --}}
                                        <span x-show="isLoading"
                                            class="animate-spin inline-block w-5 h-5 border-2 border-current border-t-transparent text-indigo-600 rounded-full"
                                            role="status" aria-label="loading"></span>

                                        {{-- Herz gefüllt (wenn isBookmarked true ist UND nicht lädt) --}}
                                        <svg x-show="!isLoading && isBookmarked" x-cloak
                                            class="w-5 h-5 text-pink-500 fill-current" viewBox="0 0 20 20">
                                            <path
                                                d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" />
                                        </svg>

                                        {{-- Herz leer (wenn isBookmarked false ist UND nicht lädt) --}}
                                        <svg x-show="!isLoading && !isBookmarked" x-cloak
                                            class="w-5 h-5 text-gray-400 dark:text-gray-500 group-hover:text-pink-400 transition-colors"
                                            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                            </path>
                                        </svg>
                                    </button>
                                    {{-- Feedback Message (optional) --}}
                                    {{-- <div x-show="message && !isLoading" x-text="message" ...></div> --}}
                                </div>
                            @endauth
                        </div>

                        {{-- Text Inhalt --}}
                        <div class="p-4 flex flex-col flex-grow">
                            <h3
                                class="text-lg font-semibold mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                                <a href="{{ route('events.show', $event) }}">{{ Str::limit($event->name, 50) }}</a>
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                                <svg class="w-3.5 h-3.5 inline-block mr-1 -mt-0.5 text-gray-400" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.414-1.415L11 9.586V6z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                {{ $event->start_time->translatedFormat('D') }},
                                {{ $event->start_time->format('H:i') }} Uhr
                                @if ($event->club)
                                    <span class="mx-1 text-gray-300 dark:text-gray-600">|</span>
                                    <svg class="w-3.5 h-3.5 inline-block mr-1 -mt-0.5 text-gray-400" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <a href="{{ route('clubs.show', $event->club) }}"
                                        class="hover:underline">{{ Str::limit($event->club->name, 20) }}</a>
                                @endif
                            </p>
                            @if ($event->genres->isNotEmpty())
                                <div class="text-xs mb-3 flex flex-wrap gap-1">
                                    @foreach ($event->genres->take(2) as $genre)
                                        <span
                                            class="inline-block bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300 px-2 py-0.5 rounded font-medium">{{ $genre->name }}</span>
                                    @endforeach
                                    @if ($event->genres->count() > 2)
                                        <span
                                            class="inline-block bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-0.5 rounded font-medium">...</span>
                                    @endif
                                </div>
                            @endif
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">
                                {{ $event->formattedPriceAttribute }}
                            </p>
                            <div class="mt-auto pt-3 border-t border-gray-200 dark:border-gray-700 text-right">
                                <a href="{{ route('events.show', $event) }}"
                                    class="btn-secondary !text-xs !py-1.5 !px-3">Details</a>
                            </div>
                        </div>
                    </div>
                    {{-- Ende Event Card --}}
                @endforeach
            </div>
            <div class="mt-12"> {{ $events->appends(request()->query())->links() }} </div>
        @else
            <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12v-.008ZM15 12h.008v.008H15v-.008ZM15 15h.008v.008H15v-.008ZM9 15h.008v.008H9v-.008Z" />
                </svg>
                <h2 class="mt-2 text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">Keine Events gefunden</h2>
                <p class="text-gray-500 dark:text-gray-400">Es wurden keine Events gefunden, die den aktuellen
                    Kriterien entsprechen.</p>
                @if (request()->hasAny(['genre', 'city', 'date', 'sort']))
                    <div class="mt-6"> <a href="{{ route('events.index') }}" class="btn-secondary">Alle Filter
                            zurücksetzen</a> </div>
                @endif
            </div>
        @endif
    </div>
</x-frontend-layout>
