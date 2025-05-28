<x-frontend-layout :title="$title ?? 'Meine gemerkten Events'">
    <div class="bg-gray-200 dark:bg-gray-800 py-8">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $title ?? 'Meine gemerkten Events' }}</h1>
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @if (session('bookmark_status'))
            <div
                class="mb-6 p-4 bg-green-100 dark:bg-green-700 border border-green-200 dark:border-green-600 text-green-700 dark:text-green-200 rounded text-sm">
                {{ session('bookmark_status') }}
            </div>
        @endif
        @if (session('error'))
            {{-- Für generelle Fehler --}}
            <div
                class="mb-6 p-4 bg-red-100 dark:bg-red-700 border border-red-200 dark:border-red-600 text-red-700 dark:text-red-200 rounded text-sm">
                {{ session('error') }}
            </div>
        @endif

        @if ($bookmarkedEvents->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($bookmarkedEvents as $event)
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden flex flex-col group transform hover:-translate-y-1 transition-all duration-300 h-full">
                        {{-- Bild Bereich mit Bookmark Icon (Hier nicht nötig, da schon gebookmarked) --}}
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
                            <div
                                class="absolute top-2 left-2 bg-black/70 text-white px-2.5 py-1 rounded text-center leading-tight shadow-lg">
                                <span
                                    class="text-xs uppercase font-semibold tracking-wide">{{ $event->start_time->translatedFormat('M') }}</span><br>
                                <span class="text-lg font-bold">{{ $event->start_time->format('d') }}</span>
                            </div>
                        </div>

                        {{-- Text Inhalt --}}
                        <div class="p-4 flex flex-col flex-grow">
                            <h3
                                class="text-lg font-semibold mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                                <a href="{{ route('events.show', $event) }}">{{ Str::limit($event->name, 50) }}</a>
                            </h3>
                            {{-- Zeit & Club Details --}}
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                                <svg class="w-3.5 h-3.5 inline-block mr-1 -mt-0.5 text-gray-400" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.414-1.415L11 9.586V6z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                {{ $event->start_time->translatedFormat('D') }},
                                {{ $event->start_time->format('H:i') }} Uhr
                                @if ($event->end_time)
                                    - {{ $event->end_time->format('H:i') }} Uhr
                                @endif
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
                            {{-- Genres --}}
                            @if ($event->genres->isNotEmpty())
                                <div class="text-xs mb-3 flex flex-wrap gap-1">
                                    @foreach ($event->genres->take(3) as $genre)
                                        <span
                                            class="inline-block bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300 px-2 py-0.5 rounded font-medium">{{ $genre->name }}</span>
                                    @endforeach
                                    @if ($event->genres->count() > 3)
                                        <span
                                            class="inline-block bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-0.5 rounded font-medium">...</span>
                                    @endif
                                </div>
                            @endif
                            {{-- Preis --}}
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">
                                {{ $event->formattedPriceAttribute }}
                            </p>
                            {{-- Buttons unten auf der Karte --}}
                            <div
                                class="mt-auto pt-3 border-t border-gray-200 dark:border-gray-700 flex justify-between items-center">
                                <a href="{{ route('events.show', $event) }}"
                                    class="btn-secondary !text-xs !py-1.5 !px-3">Details ansehen</a>
                                {{-- Entfernen-Button mit JavaScript-Bestätigung --}}
                                <form x-data
                                    @submit.prevent="
                                    if (confirm('Möchtest du dieses Event wirklich von deiner Merkliste entfernen?')) {
                                        let form = $event.target;
                                        fetch(form.action, {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': form.querySelector('input[name=\'_token\']').value,
                                                'Accept': 'application/json',
                                                'X-Requested-With': 'XMLHttpRequest'
                                            }
                                        })
                                        .then(response => response.json().then(data => ({status: response.status, body: data})))
                                        .then(({status, body}) => {
                                            if (status === 200 && body.bookmarked === false) {
                                                window.location.reload(); // Einfaches Neuladen
                                            } else {
                                                alert(body.message || 'Fehler beim Entfernen des Bookmarks.');
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Entfernen Fehler:', error);
                                            alert('Ein Fehler ist aufgetreten.');
                                        });
                                    }"
                                    action="{{ route('events.bookmark.toggle', $event) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="text-xs text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 focus:outline-none font-medium flex items-center hover:bg-red-50 dark:hover:bg-red-900/30 p-1 rounded"
                                        title="Von Merkliste entfernen">
                                        <svg class="w-4 h-4 inline-block mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        Entfernen
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-12">
                {{ $bookmarkedEvents->appends(request()->query())->links() }} {{-- Hänge Query-Parameter an, falls welche existieren --}}
            </div>
        @else
            <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                </svg>
                <h2 class="mt-2 text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">Deine Merkliste ist leer
                </h2>
                <p class="text-gray-500 dark:text-gray-400">Du hast dir noch keine Events gemerkt.</p>
                <div class="mt-6">
                    <a href="{{ route('events.index') }}" class="btn-primary">Events entdecken</a>
                </div>
            </div>
        @endif
    </div>
</x-frontend-layout>
