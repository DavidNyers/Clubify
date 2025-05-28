<x-frontend-layout :title="$title" :description="$description">
    {{-- Event Header Sektion --}}
    <div
        class="bg-gradient-to-r from-indigo-600  via-purple-600 to-pink-600 dark:from-indigo-800 dark:via-purple-800 dark:to-pink-800 text-white pt-12 pb-8 md:pt-6 md:pb-6 shadow-lg">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumbs (Beispiel) --}}
            <nav class="text-sm mb-2" aria-label="Breadcrumb">
                <ol class="list-none p-0 inline-flex space-x-2">
                    <li class="flex items-center">
                        <a href="{{ route('home') }}" class="text-gray-500 dark:text-gray-400 hover:text-red-600">Home</a>
                    </li>
                    <li class="flex items-center">
                        <svg class="fill-current w-3 h-3 mx-1 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20">
                            <path
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" />
                        </svg>
                        <a href="{{ route('events.index') }}"
                            class="text-gray-500 dark:text-gray-400 hover:text-red-600">Events</a>
                    </li>
                    <li class="flex items-center">
                        <svg class="fill-current w-3 h-3 mx-1 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20">
                            <path
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" />
                        </svg>
                        <span class="text-gray-700 dark:text-gray-300" aria-current="page">{{ $event->name }}</span>
                    </li>
                </ol>
            </nav>

            {{-- Event Titel --}}
            <h1 class="text-3xl md:text-4xl font-bold mb-2">{{ $event->name }}</h1>

            {{-- Datum & Zeit --}}
            <div class="flex flex-wrap items-center text-lg mb-3 opacity-90">
                <svg class="w-5 h-5 mr-2 inline-block" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                        clip-rule="evenodd"></path>
                </svg>
                <span>{{ $event->start_time->translatedFormat('l, d. F Y') }}</span> {{-- Formatiertes Datum --}}
                <span class="mx-2">|</span>
                <svg class="w-5 h-5 mr-1 inline-block" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.414-1.415L11 9.586V6z"
                        clip-rule="evenodd"></path>
                </svg>
                <span>{{ $event->start_time->format('H:i') }} Uhr @if ($event->end_time)
                        - {{ $event->end_time->format('H:i') }} Uhr
                    @endif
                </span>
            </div>

            {{-- Club / Location --}}
            @if ($event->club)
                <div class="flex items-center text-lg opacity-90">
                    <svg class="w-5 h-5 mr-2 inline-block" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <span>@</span>
                    <a href="{{ route('clubs.show', $event->club) }}"
                        class="ml-1 hover:underline font-medium">{{ $event->club->name }}</a>
                    <span class="ml-1 text-base">({{ $event->club->city }})</span>
                </div>
            @endif
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">

            {{-- Hauptinhalt (links) --}}
            <div class="lg:col-span-2 space-y-8">

                {{-- Cover Bild Placeholder --}}
                <div
                    class="rounded-lg shadow-lg overflow-hidden aspect-w-16 aspect-h-9 md:aspect-h-7 lg:aspect-h-6 bg-gray-300 dark:bg-gray-700">
                    {{-- Aspect Ratio für Responsivität --}}
                    @if ($event->cover_image_path && Storage::disk('public')->exists($event->cover_image_path))
                        <img src="{{ Storage::url($event->cover_image_path) }}"
                            alt="Coverbild für {{ $event->name }}" class="w-full h-full object-cover">
                    @else
                        {{-- Fallback-Bild oder ansprechender Placeholder --}}
                        <div
                            class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-500 via-purple-600 to-pink-600">
                            <span
                                class="text-white text-opacity-80 text-2xl font-bold px-4 text-center">{{ $event->name }}</span>
                            <svg class="w-24 h-24 text-white opacity-50" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>

                        </div>
                    @endif
                </div>

                {{-- Beschreibung --}}
                @if ($event->description)
                    <section>
                        <h2 class="text-2xl font-semibold mb-4 text-gray-900 dark:text-white">Beschreibung</h2>
                        <div class="prose prose-lg dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                            {!! nl2br(e($event->description)) !!} {{-- Sicher für einfachen Text, später anpassen für Rich Text --}}
                        </div>
                    </section>
                @endif

                {{-- Lineup / DJs --}}
                @if ($event->djs->isNotEmpty())
                    <section>
                        <h2 class="text-2xl font-semibold mb-4 text-gray-900 dark:text-white">Lineup</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach ($event->djs as $djUser)
                                <div
                                    class="flex items-center space-x-3 bg-white dark:bg-gray-800 p-3 rounded-lg shadow">
                                    <div class="flex-shrink-0">
                                        {{-- TODO: DJ Profilbild laden --}}
                                        <img class="h-12 w-12 rounded-full bg-gray-300 dark:bg-gray-700"
                                            src="https://ui-avatars.com/api/?name={{ urlencode($djUser->djProfile->displayName ?? $djUser->name) }}&color=7F9CF5&background=EBF4FF"
                                            alt="{{ $djUser->djProfile->displayName ?? $djUser->name }}">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        {{-- TODO: Link zum DJ Profil erstellen --}}
                                        <a href="#"
                                            class="text-sm font-semibold text-gray-900 dark:text-white hover:text-indigo-600 truncate block">{{ $djUser->djProfile->displayName ?? $djUser->name }}</a>
                                        {{-- Optional: Genre des DJs? --}}
                                        {{-- <p class="text-xs text-gray-500 dark:text-gray-400">Techno, House</p> --}}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

            </div>

            {{-- Sidebar (rechts) --}}
            <aside class="lg:col-span-1 space-y-6">
                {{-- Preis & Tickets --}}
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-3 text-gray-900 dark:text-white">Preis & Tickets</h3>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-3">
                        {{ $event->formattedPriceAttribute }}</p>
                    @if ($event->allows_presale)
                        <a href="#" class="btn-primary w-full text-center block">Tickets / VVK (TODO)</a>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">Nur Abendkasse oder Gästeliste.</p>
                    @endif
                    {{-- TODO: Link zur Gästeliste, wenn aktiviert --}}
                    @if ($event->allows_guestlist)
                        <a href="#"
                            class="mt-2 text-sm text-indigo-600 dark:text-indigo-400 hover:underline block text-center">Zur
                            Gästeliste (TODO)</a>
                    @endif
                </div>

                {{-- Club Info --}}
                @if ($event->club)
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                        <h3
                            class="text-lg font-semibold mb-3 border-b border-gray-200 dark:border-gray-700 pb-2 text-gray-900 dark:text-white">
                            Location</h3>
                        <div class="flex items-center space-x-3">
                            {{-- TODO: Club Bild? --}}
                            <div class="flex-shrink-0 w-12 h-12 bg-gray-300 dark:bg-gray-700 rounded"></div>
                            <div>
                                <a href="{{ route('clubs.show', $event->club) }}"
                                    class="font-semibold text-gray-900 dark:text-white hover:text-indigo-600">{{ $event->club->name }}</a>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $event->club->fullAddressAttribute }}</p>
                            </div>
                        </div>
                        {{-- TODO: Kleine Karte für den Club? --}}
                        <div
                            class="mt-3 h-32 bg-gray-200 dark:bg-gray-700 rounded flex items-center justify-center text-sm text-gray-500">
                            [ Karte Club Placeholder ]</div>
                    </div>
                @endif

                {{-- Genres --}}
                @if ($event->genres->isNotEmpty())
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                        <h3
                            class="text-lg font-semibold mb-3 border-b border-gray-200 dark:border-gray-700 pb-2 text-gray-900 dark:text-white">
                            Musikgenres</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($event->genres as $genre)
                                {{-- TODO: Link zur Genre-Seite? --}}
                                <span
                                    class="inline-block bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-3 py-1 rounded-full text-sm font-medium">
                                    {{ $genre->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Veranstalter Info (falls vorhanden) --}}
                @if ($event->organizer)
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                        <h3
                            class="text-lg font-semibold mb-3 border-b border-gray-200 dark:border-gray-700 pb-2 text-gray-900 dark:text-white">
                            Veranstalter</h3>
                        <div class="flex items-center space-x-3">
                            {{-- TODO: Veranstalter Logo? --}}
                            <div class="flex-shrink-0 w-10 h-10 bg-gray-300 dark:bg-gray-700 rounded-full"></div>
                            <div>
                                {{-- TODO: Link zur Veranstalter-Seite? --}}
                                <p class="font-medium text-gray-900 dark:text-white">{{ $event->organizer->name }}</p>
                                {{-- <p class="text-sm text-gray-500 dark:text-gray-400">Kontakt: ...</p> --}}
                            </div>
                        </div>
                    </div>
                @endif

            </aside>

        </div>
    </div>

</x-frontend-layout>
