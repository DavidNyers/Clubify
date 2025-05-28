<x-frontend-layout :title="$title" :description="$description">

    {{-- Header-Bereich mit Banner & Profilbild --}}
    <div class="relative h-48 md:h-64 bg-gray-300 dark:bg-gray-700">
        {{-- Bannerbild (Placeholder) --}}
        {{-- TODO: Bannerbild laden --}}
        {{-- @if ($dj->banner_image_path)
          <img src="{{ Storage::url($dj->banner_image_path) }}" alt="Banner für {{ $dj->displayName }}" class="w-full h-full object-cover">
      @else --}}
        <div class="absolute inset-0 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 opacity-80"></div>
        {{-- @endif --}}

        {{-- Inhalt über Banner --}}
        <div class="absolute inset-0 flex flex-col justify-end pb-8">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row items-center sm:items-end space-y-4 sm:space-y-0 sm:space-x-5">
                    {{-- Profilbild --}}
                    <div class="flex-shrink-0">
                        <div class="relative">
                            {{-- TODO: Profilbild laden --}}
                            {{-- @if ($dj->profile_image_path)
                              <img class="h-24 w-24 sm:h-32 sm:w-32 rounded-full object-cover ring-4 ring-white dark:ring-gray-800 shadow-lg" src="{{ Storage::url($dj->profile_image_path) }}" alt="{{ $dj->displayName }}">
                          @else --}}
                            <div
                                class="h-24 w-24 sm:h-32 sm:w-32 rounded-full bg-gray-200 dark:bg-gray-600 ring-4 ring-white dark:ring-gray-800 shadow-lg flex items-center justify-center">
                                <svg class="w-16 h-16 sm:w-20 sm:h-20 text-gray-400 dark:text-gray-500"
                                    fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z">
                                    </path>
                                </svg>
                            </div>
                            {{-- @endif --}}
                            @if ($dj->is_verified)
                                <span
                                    class="absolute -bottom-1 -right-1 bg-blue-500 text-white rounded-full p-1 border-2 border-white dark:border-gray-800"
                                    title="Verifiziert">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </span>
                            @endif
                        </div>
                    </div>
                    {{-- Name & Social Links --}}
                    <div>
                        <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white shadow-sm">
                            {{ $dj->displayName }}</h1>
                        {{-- TODO: Genres des DJs hier? --}}
                        {{-- <p class="text-sm text-indigo-200 mt-1">Techno, House</p> --}}
                        {{-- Social Links --}}
                        <div class="mt-2 flex space-x-3">
                            @if ($dj->social_links['soundcloud'] ?? null)
                                <a href="{{ $dj->social_links['soundcloud'] }}" target="_blank"
                                    rel="noopener noreferrer" class="text-white/80 hover:text-white"><span
                                        class="sr-only">Soundcloud</span><svg class="w-5 h-5" fill="currentColor"
                                        viewBox="0 0 24 24">...</svg></a>
                            @endif
                            @if ($dj->social_links['instagram'] ?? null)
                                <a href="{{ $dj->social_links['instagram'] }}" target="_blank" rel="noopener noreferrer"
                                    class="text-white/80 hover:text-white"><span class="sr-only">Instagram</span><svg
                                        class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">...</svg></a>
                            @endif
                            @if ($dj->social_links['facebook'] ?? null)
                                <a href="{{ $dj->social_links['facebook'] }}" target="_blank" rel="noopener noreferrer"
                                    class="text-white/80 hover:text-white"><span class="sr-only">Facebook</span><svg
                                        class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">...</svg></a>
                            @endif
                            {{-- Weitere Links --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">

            {{-- Hauptinhalt (links) --}}
            <div class="lg:col-span-2 space-y-8">

                {{-- Biografie --}}
                @if ($dj->bio)
                    <section>
                        <h2 class="text-2xl font-semibold mb-4 text-gray-900 dark:text-white">Über
                            {{ $dj->displayName }}</h2>
                        <div class="prose prose-lg dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                            {!! nl2br(e($dj->bio)) !!} {{-- Sicher für einfachen Text --}}
                        </div>
                    </section>
                @endif

                {{-- Musik Links / Player --}}
                @if (!empty($dj->music_links))
                    <section>
                        <h2 class="text-2xl font-semibold mb-4 text-gray-900 dark:text-white">Musik</h2>
                        <div class="space-y-4">
                            {{-- Beispiel für Soundcloud Track Embed --}}
                            @if ($dj->music_links['soundcloud_track'] ?? null)
                                <div class="aspect-w-16 aspect-h-9">
                                    <iframe width="100%" height="166" scrolling="no" frameborder="no"
                                        allow="autoplay"
                                        src="https://w.soundcloud.com/player/?url={{ urlencode($dj->music_links['soundcloud_track']) }}&color=%23ff5500&auto_play=false&hide_related=false&show_comments=true&show_user=true&show_reposts=false&show_teaser=true">
                                    </iframe>
                                </div>
                            @endif
                            {{-- Beispiel für Mixcloud Embed --}}
                            @if ($dj->music_links['mixcloud_mix'] ?? null)
                                <div>
                                    {{-- Mixcloud Embed Code hier --}}
                                    <iframe width="100%" height="120"
                                        src="https://www.mixcloud.com/widget/iframe/?hide_cover=1&feed={{ urlencode($dj->music_links['mixcloud_mix']) }}"
                                        frameborder="0"></iframe>
                                </div>
                            @endif
                            {{-- Weitere Musik Links --}}
                        </div>
                    </section>
                @endif

                {{-- Nächste Gigs --}}
                <section>
                    <h2 class="text-2xl font-semibold mb-4 text-gray-900 dark:text-white">Nächste / Letzte Gigs</h2>
                    @if ($dj->user->djGigs->count() > 0)
                        <div class="space-y-4">
                            @foreach ($dj->user->djGigs as $gig)
                                {{-- Nutze geladene Relation --}}
                                <div
                                    class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow flex flex-col sm:flex-row gap-4">
                                    {{-- Datum --}}
                                    <div
                                        class="flex-shrink-0 text-center border-r border-gray-200 dark:border-gray-700 sm:pr-4 w-full sm:w-20">
                                        <div
                                            class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 uppercase">
                                            {{ $gig->start_time->format('M') }}</div>
                                        <div class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                                            {{ $gig->start_time->format('d') }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $gig->start_time->format('D, H:i') }} Uhr</div>
                                    </div>
                                    {{-- Details --}}
                                    <div class="flex-grow">
                                        <h3
                                            class="text-lg font-semibold hover:text-indigo-600 dark:hover:text-indigo-400">
                                            <a href="{{ route('events.show', $gig) }}">{{ $gig->name }}</a>
                                        </h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            @ <a href="{{ route('clubs.show', $gig->club) }}"
                                                class="hover:underline">{{ $gig->club->name }}</a>,
                                            {{ $gig->club->city }}
                                        </p>
                                        @if ($gig->isCancelled())
                                            <span class="text-xs text-red-500 font-medium">(Abgesagt)</span>
                                        @endif
                                    </div>
                                    {{-- Link --}}
                                    <div class="flex-shrink-0 self-center">
                                        <a href="{{ route('events.show', $gig) }}"
                                            class="btn-secondary !text-xs !py-1">Event Details</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600 dark:text-gray-400 italic">Keine Gigs gefunden.</p>
                    @endif
                </section>

            </div>

            {{-- Sidebar (rechts) --}}
            <aside class="lg:col-span-1 space-y-6">

                {{-- Booking Informationen --}}
                @if ($dj->booking_email || $dj->technical_rider_path)
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                        <h3
                            class="text-lg font-semibold mb-3 border-b border-gray-200 dark:border-gray-700 pb-2 text-gray-900 dark:text-white">
                            Booking</h3>
                        <div class="space-y-2 text-sm">
                            @if ($dj->booking_email)
                                <p>
                                    <svg class="w-4 h-4 inline-block mr-1 -mt-0.5 text-gray-400" fill="currentColor"
                                        viewBox="0 0 20 20">...</svg> {{-- Email Icon --}}
                                    <a href="mailto:{{ $dj->booking_email }}"
                                        class="text-gray-700 dark:text-gray-300 hover:text-indigo-600">{{ $dj->booking_email }}</a>
                                </p>
                            @endif
                            @if ($dj->technical_rider_path)
                                <p>
                                    <svg class="w-4 h-4 inline-block mr-1 -mt-0.5 text-gray-400" fill="currentColor"
                                        viewBox="0 0 20 20">...</svg> {{-- Download/File Icon --}}
                                    {{-- TODO: Link zum Download des Riders --}}
                                    <a href="#"
                                        class="text-gray-700 dark:text-gray-300 hover:text-indigo-600">Technical Rider
                                        (PDF)</a>
                                </p>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- TODO: Genres des DJs anzeigen --}}
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                    <h3
                        class="text-lg font-semibold mb-3 border-b border-gray-200 dark:border-gray-700 pb-2 text-gray-900 dark:text-white">
                        Genres (TODO)</h3>
                    <div class="flex flex-wrap gap-2">
                        <span
                            class="inline-block bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-3 py-1 rounded-full text-sm font-medium">Techno</span>
                        <span
                            class="inline-block bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-3 py-1 rounded-full text-sm font-medium">House</span>
                    </div>
                </div>

            </aside>

        </div>
    </div>

</x-frontend-layout>
