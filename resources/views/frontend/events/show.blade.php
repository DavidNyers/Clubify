<x-frontend-layout :title="$title" :description="$description">
    {{-- Event Header Sektion --}}
    <div
        class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 dark:from-indigo-800 dark:via-purple-800 dark:to-pink-800 text-white pt-12 pb-8 md:pt-16 md:pb-12 shadow-lg relative">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumbs --}}
            <nav class="text-sm mb-4 opacity-90" aria-label="Breadcrumb">
                <ol class="list-none p-0 inline-flex space-x-1 items-center">
                    <li class="flex items-center"><a href="{{ route('home') }}" class="hover:underline">Home</a></li>
                    <li class="flex items-center"><svg class="fill-current w-3 h-3 mx-1 text-indigo-200"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" />
                        </svg><a href="{{ route('events.index') }}" class="hover:underline">Events</a></li>
                    <li class="flex items-center"><svg class="fill-current w-3 h-3 mx-1 text-indigo-200"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" />
                        </svg><span class="font-medium text-white"
                            aria-current="page">{{ Str::limit($event->name, 40) }}</span></li>
                </ol>
            </nav>

            {{-- Haupt-Header-Inhalt mit Bookmark-Button --}}
            <div class="md:flex md:items-end md:justify-between">
                <div class="max-w-3xl"> {{-- Container für Textlinks, um Überlappung zu vermeiden --}}
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-2">{{ $event->name }}</h1>
                    {{-- Datum & Zeit --}}
                    <div class="flex flex-wrap items-center text-lg mb-2 opacity-90">
                        <svg class="w-5 h-5 mr-2 inline-block flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ $event->start_time->translatedFormat('l, d. F Y') }}</span>
                        <span class="mx-2 opacity-70">|</span>
                        <svg class="w-5 h-5 mr-1 inline-block flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.414-1.415L11 9.586V6z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ $event->start_time->format('H:i') }} Uhr @if ($event->end_time)
                                - {{ $event->end_time->format('H:i') }} Uhr
                            @else
                                <span class="text-sm">(Open End)</span>
                            @endif
                        </span>
                    </div>
                    {{-- Club / Location --}}
                    @if ($event->club)
                        <div class="flex items-center text-base opacity-90 mt-1">
                            <svg class="w-4 h-4 mr-2 inline-block flex-shrink-0" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span>@</span>
                            <a href="{{ route('clubs.show', $event->club) }}"
                                class="ml-1 hover:underline font-medium">{{ $event->club->name }}</a>
                            @if ($event->club->city)
                                <span class="ml-1 text-indigo-200">({{ $event->club->city }})</span>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Bookmark-Button hier platziert --}}
                @auth
                    <div x-data="{
                        initialIsBookmarked: {{ Auth::user()->bookmarkedEvents()->where('event_id', $event->id)->exists() ? 'true' : 'false' }},
                        isBookmarked: false,
                        isLoading: false,
                        message: ''
                    }" x-init="isBookmarked = initialIsBookmarked" class="mt-4 md:mt-0 flex-shrink-0">
                        <button
                            @click="
                            isLoading = true;
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
                                isBookmarked = data.bookmarked;
                                message = data.message;
                            })
                            .catch(error => {
                                message = 'Fehler beim Bookmarken.';
                                console.error('Bookmark Error:', error);
                            })
                            .finally(() => {
                                isLoading = false;
                            });
                        "
                            :disabled="isLoading" type="button"
                            class="p-2.5 rounded-full bg-white/20 dark:bg-gray-900/40 text-white hover:bg-white/30 dark:hover:bg-gray-700/60 focus:outline-none focus:ring-2 focus:ring-white transition-colors shadow-lg flex items-center w-full md:w-auto justify-center"
                            :title="isBookmarked ? 'Event von Merkliste entfernen' : 'Event merken'">
                            <span x-show="isLoading"
                                class="animate-spin inline-block w-5 h-5 border-2 border-current border-t-transparent rounded-full"
                                role="status"></span>
                            <svg x-show="!isLoading && isBookmarked" x-cloak class="w-5 h-5 fill-current text-pink-400"
                                viewBox="0 0 20 20">
                                <path
                                    d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" />
                            </svg>
                            <svg x-show="!isLoading && !isBookmarked" x-cloak class="w-5 h-5" fill="none"
                                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                </path>
                            </svg>
                            <span x-show="!isLoading" class="ml-2 text-sm hidden sm:inline"
                                x-text="isBookmarked ? 'Gemerkt' : 'Merken'"></span>
                        </button>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">

            {{-- Hauptinhalt (links) --}}
            <div class="lg:col-span-2 space-y-10"> {{-- Etwas mehr Abstand zwischen Sektionen --}}
                {{-- Cover Bild --}}
                <div
                    class="rounded-lg shadow-xl overflow-hidden aspect-w-16 aspect-h-9 md:aspect-h-8 lg:aspect-h-7 bg-gray-300 dark:bg-gray-700">
                    @if ($event->cover_image_path && Storage::disk('public')->exists($event->cover_image_path))
                        <img src="{{ Storage::url($event->cover_image_path) }}"
                            alt="Coverbild für {{ $event->name }}" class="w-full h-full object-cover">
                    @else
                        <div
                            class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600">
                            <h2 class="text-white text-opacity-80 text-3xl font-bold px-4 text-center">
                                {{ $event->name }}</h2>
                        </div>
                    @endif
                </div>

                {{-- Beschreibung --}}
                @if ($event->description)
                    <section>
                        <h2 class="text-2xl md:text-3xl font-semibold mb-4 text-gray-900 dark:text-white">Über dieses
                            Event</h2>
                        <div
                            class="prose prose-lg dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 leading-relaxed">
                            {!! nl2br(e($event->description)) !!}
                        </div>
                    </section>
                @endif

                {{-- Lineup / DJs --}}
                @if ($event->djs->isNotEmpty())
                    <section>
                        <h2 class="text-2xl md:text-3xl font-semibold mb-6 text-gray-900 dark:text-white">Lineup</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-x-6 gap-y-8">
                            {{-- Größere Gaps --}}
                            @foreach ($event->djs as $djUser)
                                <a href="{{ $djUser->djProfile ? route('djs.show', $djUser->djProfile) : '#' }}"
                                    class="block group text-center transition-transform duration-200 hover:-translate-y-1">
                                    <div class="relative w-28 h-28 sm:w-32 sm:h-32 mx-auto mb-3">
                                        @if (
                                            $djUser->djProfile &&
                                                $djUser->djProfile->profile_image_path &&
                                                Storage::disk('public')->exists($djUser->djProfile->profile_image_path))
                                            <img class="w-full h-full rounded-full object-cover shadow-lg ring-2 ring-white dark:ring-gray-700 group-hover:ring-indigo-500 transition-all"
                                                src="{{ Storage::url($djUser->djProfile->profile_image_path) }}"
                                                alt="{{ $djUser->djProfile->displayName ?? $djUser->name }}">
                                        @else
                                            <img class="w-full h-full rounded-full object-cover shadow-lg ring-2 ring-white dark:ring-gray-700 group-hover:ring-indigo-500 transition-all bg-gray-200 dark:bg-gray-700"
                                                src="https://ui-avatars.com/api/?name={{ urlencode($djUser->djProfile->displayName ?? $djUser->name) }}&color=7F9CF5&background=EBF4FF&size=128&font-size=0.33&bold=true"
                                                alt="{{ $djUser->djProfile->displayName ?? $djUser->name }}">
                                        @endif
                                        @if ($djUser->djProfile && $djUser->djProfile->is_verified)
                                            {{-- Prüfe djProfile für Verifizierung --}}
                                            <span
                                                class="absolute bottom-0 right-0 block h-5 w-5 sm:h-6 sm:w-6 rounded-full ring-2 ring-white dark:ring-gray-800 bg-blue-500 text-white flex items-center justify-center text-xs"
                                                title="Verifizierter DJ">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                            </span>
                                        @endif
                                    </div>
                                    <h4
                                        class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                        {{ $djUser->djProfile->displayName ?? $djUser->name }}
                                    </h4>
                                    {{-- TODO: Hauptgenre(s) des DJs anzeigen --}}
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Electronic, Techno</p>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>

            {{-- Sidebar (rechts) --}}
            <aside class="lg:col-span-1 space-y-6">
                <div class="sticky top-24 space-y-6"> {{-- Sticky mit Abstand --}}
                    {{-- Preis & Tickets --}}
                    <div class="bg-white dark:bg-gray-800 p-5 rounded-lg shadow-lg">
                        <h3
                            class="text-xl font-semibold mb-3 text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                            Tickets & Einlass</h3>
                        <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                            {{ $event->formattedPriceAttribute }}</p>
                        @if ($event->allows_presale)
                            <a href="#"
                                class="btn-primary bg-green-600 hover:bg-green-500 w-full text-center block mb-2 shadow-md text-base py-2.5">
                                <svg class="w-5 h-5 inline-block mr-1.5 -ml-1 relative -top-px" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"></path>
                                </svg>
                                Tickets sichern (Link TODO)
                            </a>
                        @endif
                        @if ($event->allows_guestlist)
                            <a href="#"
                                class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline block text-center @if ($event->allows_presale) mt-3 @endif">Gästelisten-Anfrage
                                (Link TODO)</a>
                        @endif
                        @if (!$event->allows_presale && !$event->allows_guestlist)
                            <p
                                class="text-sm text-gray-500 dark:text-gray-400 mb-3 text-center bg-gray-100 dark:bg-gray-700 p-3 rounded-md">
                                Nur Abendkasse.</p>
                        @endif
                    </div>

                    {{-- Club Info --}}
                    @if ($event->club)
                        <div class="bg-white dark:bg-gray-800 p-5 rounded-lg shadow-lg">
                            <h3
                                class="text-xl font-semibold mb-4 text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                                Veranstaltungsort</h3>
                            <div class="flex items-start space-x-4 mb-3">
                                <a href="{{ route('clubs.show', $event->club) }}"
                                    class="flex-shrink-0 w-16 h-16 bg-gray-300 dark:bg-gray-700 rounded-md overflow-hidden shadow">
                                    {{-- TODO: Erstes Galeriebild des Clubs laden --}}
                                    <img src="https://source.unsplash.com/random/100x100/?architecture,nightclub&r={{ $event->club->id }}"
                                        alt="{{ $event->club->name }}" class="w-full h-full object-cover">
                                </a>
                                <div>
                                    <a href="{{ route('clubs.show', $event->club) }}"
                                        class="text-lg font-semibold text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 leading-tight">{{ $event->club->name }}</a>
                                    <address
                                        class="not-italic text-sm text-gray-500 dark:text-gray-400 leading-snug mt-0.5">
                                        <span class="block flex items-start">
                                            <svg class="w-4 h-4 mr-1.5 mt-0.5 text-gray-400 flex-shrink-0"
                                                fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            <span>{{ $event->club->street_address }}<br>{{ $event->club->zip_code }}
                                                {{ $event->club->city }}</span>
                                        </span>
                                    </address>
                                </div>
                            </div>
                            <div
                                class="mt-4 h-48 bg-gray-200 dark:bg-gray-700/50 rounded-lg flex items-center justify-center text-sm text-gray-500 dark:text-gray-400 italic shadow-inner">
                                [ Mini-Karte für Club "{{ Str::limit($event->club->name, 20) }}" Placeholder ]
                            </div>
                        </div>
                    @endif

                    {{-- Genres des Events --}}
                    @if ($event->genres->isNotEmpty())
                        <div class="bg-white dark:bg-gray-800 p-5 rounded-lg shadow-lg">
                            <h3
                                class="text-xl font-semibold mb-3 text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                                Musik</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($event->genres as $genre)
                                    <span
                                        class="inline-block bg-indigo-100 text-indigo-800 dark:bg-indigo-900/70 dark:text-indigo-200 text-sm px-3 py-1 rounded-full font-medium">
                                        {{ $genre->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Veranstalter Info --}}
                    @if ($event->organizer)
                        <div class="bg-white dark:bg-gray-800 p-5 rounded-lg shadow-lg">
                            <h3
                                class="text-xl font-semibold mb-3 text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                                Veranstalter</h3>
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <img class="h-12 w-12 rounded-full bg-gray-200 dark:bg-gray-700 object-cover"
                                        src="https://ui-avatars.com/api/?name={{ urlencode($event->organizer->name) }}&color=7F9CF5&background=EBF4FF&size=128&font-size=0.33&bold=true"
                                        alt="{{ $event->organizer->name }}">
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white text-lg">
                                        {{ $event->organizer->name }}</p>
                                    {{-- <a href="#" class="text-xs text-indigo-600 hover:underline">Mehr vom Veranstalter</a> --}}
                                </div>
                            </div>
                        </div>
                    @endif
                </div> {{-- Ende Sticky Wrapper --}}
            </aside>

        </div>
    </div>
</x-frontend-layout>
