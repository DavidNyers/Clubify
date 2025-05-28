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
                {{-- Filter Genre --}}
                <div class="sm:col-span-1 md:col-span-1 lg:col-span-1">
                    <label for="filter-genre" class="form-label text-xs">Genre</label>
                    <select id="filter-genre" name="genre" class="form-select-field text-sm w-full mt-1">
                        <option value="">Alle Genres</option>
                        @foreach ($availableGenres as $genre)
                            <option value="{{ $genre->slug }}" {{ $genreFilter == $genre->slug ? 'selected' : '' }}>
                                {{ $genre->name }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Filter Stadt --}} {{-- <<< GEÄNDERT --}}
                <div class="sm:col-span-1 md:col-span-1 lg:col-span-1">
                    <label for="filter-city" class="form-label text-xs">Stadt</label>
                    <select id="filter-city" name="city" class="form-select-field text-sm w-full mt-1">
                        <option value="">Alle Städte</option>
                        @foreach ($availableCities as $city)
                            {{-- Nutze $availableCities --}}
                            <option value="{{ $city }}" {{ $cityFilter == $city ? 'selected' : '' }}>
                                {{ $city }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Filter Datum --}}
                <div class="sm:col-span-1 md:col-span-1 lg:col-span-1">
                    <label for="filter-date" class="form-label text-xs">Datum</label>
                    <input type="date" id="filter-date" name="date" value="{{ $dateFilter }}"
                        class="form-input-field text-sm w-full mt-1">
                </div>

                {{-- Sortierfeld --}}
                <div class="lg:col-start-4">
                    <label for="sort-events" class="form-label text-xs">Sortieren nach</label>
                    <select id="sort-events" name="sort" class="form-select-field text-sm w-full mt-1">
                        <option value="date-asc" {{ $sortValue == 'date-asc' ? 'selected' : '' }}>Datum (Nächste)
                        </option>
                        <option value="date-desc" {{ $sortValue == 'date-desc' ? 'selected' : '' }}>Datum (Neueste)
                        </option>
                        <option value="name-asc" {{ $sortValue == 'name-asc' ? 'selected' : '' }}>Name (A-Z)</option>
                        <option value="name-desc" {{ $sortValue == 'name-desc' ? 'selected' : '' }}>Name (Z-A)</option>
                    </select>
                </div>

                {{-- Submit/Reset Buttons --}}
                <div class="flex items-center space-x-2 lg:col-start-5">
                    <button type="submit" class="btn-primary w-full py-1.5 text-sm">Anwenden</button>
                    {{-- Passe die Reset-Bedingung an --}}
                    @if (request()->hasAny(['genre', 'city', 'date', 'sort']))
                        <a href="{{ route('events.index') }}" class="btn-secondary w-full py-1.5 text-sm text-center"
                            title="Filter/Sortierung zurücksetzen">Reset</a>
                    @endif
                </div>
                {{-- Versteckte Felder (Passe ggf. 'except' an) --}}
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
                    {{-- Event Card --}}
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden flex flex-col group transition-shadow duration-200 hover:shadow-xl">
                        {{-- Bild Bereich --}}
                        <a href="{{ route('events.show', $event) }}"
                            class="block relative h-48 bg-gray-300 dark:bg-gray-700 group-hover:opacity-90 transition duration-150 ease-in-out overflow-hidden rounded-t-lg">
                            @if ($event->cover_image_path && Storage::disk('public')->exists($event->cover_image_path))
                                {{-- Zeige das hochgeladene Bild --}}
                                <img src="{{ Storage::url($event->cover_image_path) }}"
                                    alt="Coverbild für {{ $event->name }}"
                                    class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                            @else
                                {{-- Fallback-Bild oder Placeholder-Styling --}}
                                <div
                                    class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-500 to-purple-600">
                                    <span
                                        class="text-white text-opacity-80 text-lg font-medium">{{ $event->name }}</span>
                                    {{-- Optional: Ein generisches Icon --}}
                                    {{-- <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg> --}}
                                </div>
                            @endif
                            {{-- Datum Badge bleibt gleich --}}
                            <div
                                class="absolute top-2 left-2 bg-black/70 text-white px-2.5 py-1 rounded text-center leading-tight shadow-lg">
                                <span
                                    class="text-xs uppercase font-semibold tracking-wide">{{ $event->start_time->translatedFormat('M') }}</span><br>
                                <span class="text-lg font-bold">{{ $event->start_time->format('d') }}</span>
                            </div>
                        </a>
                        {{-- Text Inhalt --}}
                        <div class="p-4 flex flex-col flex-grow">
                            {{-- Titel --}}
                            <h3
                                class="text-lg font-semibold mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition duration-150 ease-in-out">
                                <a href="{{ route('events.show', $event) }}">
                                    {{ $event->name }}
                                </a>
                            </h3>
                            {{-- Zeit & Club --}}
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                                <svg class="w-3 h-3 inline-block mr-1 -mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.414-1.415L11 9.586V6z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                {{ $event->start_time->format('H:i') }} Uhr @if ($event->end_time)
                                    - {{ $event->end_time->format('H:i') }} Uhr
                                @endif
                                @if ($event->club)
                                    <span class="mx-1">|</span>
                                    <svg class="w-3 h-3 inline-block mr-1 -mt-0.5" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <a href="{{ route('clubs.show', $event->club) }}"
                                        class="hover:underline">{{ $event->club->name }}</a>
                                @endif
                            </p>

                            {{-- Genres --}}
                            @if ($event->genres->isNotEmpty())
                                <div class="text-xs mb-3 flex flex-wrap gap-1">
                                    @foreach ($event->genres->take(3) as $genre)
                                        <span
                                            class="inline-block bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2 py-0.5 rounded">
                                            {{ $genre->name }}
                                        </span>
                                    @endforeach
                                    @if ($event->genres->count() > 3)
                                        <span class="text-gray-400 dark:text-gray-500">...</span>
                                    @endif
                                </div>
                            @endif

                            {{-- Preis --}}
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">
                                {{ $event->formattedPriceAttribute }}
                            </p>

                            {{-- Button unten --}}
                            <div class="mt-auto pt-3 border-t border-gray-200 dark:border-gray-700 text-right">
                                <a href="{{ route('events.show', $event) }}"
                                    class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                                    Event Details →
                                </a>
                            </div>
                        </div>
                    </div>
                    {{-- Ende Event Card --}}
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-12">
                {{ $events->links() }}
            </div>
        @else
            {{-- Keine Events gefunden Nachricht --}}
            <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12v-.008ZM15 12h.008v.008H15v-.008ZM15 15h.008v.008H15v-.008ZM9 15h.008v.008H9v-.008Z" />
                </svg>
                <h2 class="mt-2 text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">Keine Events gefunden</h2>
                <p class="text-gray-500 dark:text-gray-400">Es wurden keine Events gefunden, die den aktuellen Kriterien
                    entsprechen.</p>
                @if (request()->hasAny(['genre', 'club', 'date', 'sort']))
                    <div class="mt-6">
                        <a href="{{ route('events.index') }}" class="btn-secondary">Alle Filter zurücksetzen</a>
                    </div>
                @endif
            </div>
        @endif

    </div> {{-- Ende Container --}}

</x-frontend-layout>
