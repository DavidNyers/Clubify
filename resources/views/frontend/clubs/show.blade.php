<x-frontend-layout :title="$title" :description="$description">
    {{-- Header-Bereich für den Club --}}
    <div
        class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 dark:from-indigo-800 dark:via-purple-800 dark:to-pink-800 text-white pt-10 pb-8 md:pt-12 md:pb-10 shadow-lg">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumbs --}}
            <nav class="text-sm mb-3 opacity-90" aria-label="Breadcrumb">
                <ol class="list-none p-0 inline-flex space-x-1 items-center">
                    <li class="flex items-center"><a href="{{ route('home') }}" class="hover:underline">Home</a></li>
                    <li class="flex items-center"><svg class="fill-current w-3 h-3 mx-1"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" />
                        </svg><a href="{{ route('clubs.index') }}" class="hover:underline">Clubs</a></li>
                    <li class="flex items-center"><svg class="fill-current w-3 h-3 mx-1"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" />
                        </svg><span class="font-medium" aria-current="page">{{ $club->name }}</span></li>
                </ol>
            </nav>
            {{-- Club Name --}}
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-1">{{ $club->name }}</h1>
            {{-- Stadt --}}
            @if ($club->city)
                <p class="text-lg text-indigo-100 dark:text-indigo-300 opacity-90">{{ $club->city }}</p>
            @endif
            {{-- Durchschnittsbewertung mit halben Sternen --}}
            <div class="flex items-center mt-3">
                @if ($ratingCount > 0 && isset($averageRating))
                    <div class="flex items-center text-yellow-400"
                        title="{{ number_format($averageRating, 1) }} / 5 Sterne Durchschnitt">
                        @php $roundedAvg = round($averageRating * 2) / 2; @endphp
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($roundedAvg >= $i)
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                    <path
                                        d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                </svg> {{-- Voller Stern --}}
                            @elseif ($roundedAvg >= $i - 0.5)
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0v15z" />
                                </svg> {{-- Halber Stern (linke Hälfte) --}}
                            @else
                                <svg class="w-5 h-5 fill-current text-white opacity-30" viewBox="0 0 20 20">
                                    <path
                                        d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                </svg> {{-- Leerer Stern --}}
                            @endif
                        @endfor
                    </div>
                    <span class="ml-2 text-sm text-indigo-100 dark:text-indigo-300 opacity-90">
                        {{ number_format($averageRating, 1) }} ({{ $ratingCount }}
                        {{ Str::plural('Bewertung', $ratingCount) }})
                    </span>
                @else
                    <span class="text-sm text-indigo-100 dark:text-indigo-300 opacity-80 italic">Noch keine
                        Bewertungen</span>
                @endif
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">

            {{-- Hauptinhalt (links) --}}
            <div class="lg:col-span-2 space-y-8">

                {{-- Bildergalerie Placeholder --}}
                <div
                    class="bg-gray-300 dark:bg-gray-700 rounded-lg aspect-video flex items-center justify-center text-gray-500">
                    [ Bildergalerie Placeholder - TODO: Implementieren ]
                </div>

                {{-- Beschreibung --}}
                @if ($club->description)
                    <section>
                        <h2 class="text-2xl font-semibold mb-3 text-gray-900 dark:text-white">Beschreibung</h2>
                        <div class="prose prose-lg dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                            {!! nl2br(e($club->description)) !!}
                        </div>
                    </section>
                @endif

                {{-- Anstehende Events im Club --}}
                <section>
                    <h2 class="text-2xl font-semibold mb-4 text-gray-900 dark:text-white">Anstehende Events</h2>
                    @if ($club->upcomingEvents->count() > 0)
                        <div class="space-y-4">
                            @foreach ($club->upcomingEvents as $event)
                                <div
                                    class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow flex flex-col sm:flex-row gap-4 items-start">
                                    <div
                                        class="flex-shrink-0 text-center border-r border-gray-200 dark:border-gray-700 sm:pr-4 w-full sm:w-20 mb-2 sm:mb-0">
                                        <div
                                            class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 uppercase">
                                            {{ $event->start_time->translatedFormat('M') }}</div>
                                        <div class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                                            {{ $event->start_time->format('d') }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $event->start_time->translatedFormat('D') }}</div>
                                    </div>
                                    <div class="flex-grow">
                                        <h3
                                            class="text-lg font-semibold hover:text-indigo-600 dark:hover:text-indigo-400 mb-0.5">
                                            <a href="{{ route('events.show', $event) }}">{{ $event->name }}</a>
                                        </h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $event->start_time->format('H:i') }} Uhr @if ($event->end_time)
                                                - {{ $event->end_time->format('H:i') }} Uhr
                                            @endif
                                        </p>
                                        @if ($event->genres->isNotEmpty())
                                            <p class="text-xs mt-1 text-indigo-500 dark:text-indigo-400">
                                                {{ $event->genres->take(3)->pluck('name')->implode(', ') }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="flex-shrink-0 text-right self-center mt-2 sm:mt-0">
                                        <span
                                            class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-1">{{ $event->formattedPriceAttribute }}</span>
                                        <a href="{{ route('events.show', $event) }}"
                                            class="btn-secondary !text-xs !py-1 !px-3">Details</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600 dark:text-gray-400 italic">Zurzeit keine anstehenden Events in diesem
                            Club geplant.</p>
                    @endif
                </section>

                {{-- Bewertungssystem --}}
                <section id="reviews" class="pt-8 mt-8 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">
                            Bewertungen @if ($ratingCount > 0)
                                ({{ $ratingCount }})
                            @endif
                        </h2>
                    </div>

                    @auth
                        @if (Auth::user()->hasVerifiedEmail())
                            {{-- TODO: Prüfen, ob User diesen Club schon bewertet hat (ggf. zum Bearbeiten anbieten) --}}
                            <div
                                class="mb-8 bg-white dark:bg-gray-800/50 p-4 sm:p-6 rounded-lg shadow-md border dark:border-gray-700">
                                <h3 class="text-lg font-medium mb-3 text-gray-900 dark:text-white">Deine Bewertung für
                                    {{ $club->name }}:</h3>
                                @if (session('rating_success'))
                                    <div
                                        class="mb-4 p-3 bg-green-100 dark:bg-green-700 border border-green-200 dark:border-green-600 text-green-700 dark:text-green-200 rounded text-sm">
                                        {{ session('rating_success') }}</div>
                                @endif
                                @if (session('rating_error'))
                                    <div
                                        class="mb-4 p-3 bg-red-100 dark:bg-red-700 border border-red-200 dark:border-red-600 text-red-700 dark:text-red-200 rounded text-sm">
                                        {{ session('rating_error') }}</div>
                                @endif

                                <form action="{{ route('ratings.store', $club) }}" method="POST">
                                    @csrf
                                    <div class="mb-4" x-data="{ rating: {{ old('rating', 0) }}, hoverRating: 0 }">
                                        <label class="form-label mb-1">Bewertung (Sterne):</label>
                                        <div class="flex items-center space-x-1 text-2xl text-gray-300 dark:text-gray-600">
                                            @for ($s = 1; $s <= 5; $s++)
                                                <button type="button" @click="rating = {{ $s }}"
                                                    @mouseenter="hoverRating = {{ $s }}"
                                                    @mouseleave="hoverRating = 0"
                                                    class="focus:outline-none transition-colors duration-150"
                                                    title="{{ $s }} Stern{{ $s > 1 ? 'e' : '' }}">
                                                    <svg class="w-7 h-7"
                                                        :class="{
                                                            'text-yellow-400 dark:text-yellow-400': hoverRating >=
                                                                {{ $s }} || rating >=
                                                                {{ $s }},
                                                            'text-gray-300 dark:text-gray-600': hoverRating <
                                                                {{ $s }} && rating < {{ $s }}
                                                        }"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                                    </svg>
                                                </button>
                                            @endfor
                                            <input type="hidden" name="rating" x-model.number="rating">
                                        </div>
                                        @error('rating')
                                            <p class="form-error text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="comment" class="form-label">Kommentar (Optional):</label>
                                        <textarea name="comment" id="comment" rows="3" class="form-textarea-field w-full mt-1"
                                            placeholder="Deine Erfahrungen mit dem Club...">{{ old('comment') }}</textarea>
                                        @error('comment')
                                            <p class="form-error mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="mt-4 text-right"> <button type="submit" class="btn-primary">Bewertung
                                            absenden</button> </div>
                                </form>
                            </div>
                        @else
                            <p
                                class="mb-6 text-sm text-gray-600 dark:text-gray-400 p-4 bg-yellow-50 dark:bg-gray-700 rounded-md">
                                Bitte <a href="{{ route('verification.notice') }}"
                                    class="text-indigo-600 dark:text-indigo-400 hover:underline">verifiziere deine
                                    E-Mail-Adresse</a>, um eine Bewertung abzugeben.</p>
                        @endif
                    @else
                        <p
                            class="mb-6 text-sm text-gray-600 dark:text-gray-400 p-4 bg-gray-100 dark:bg-gray-700/50 rounded-md">
                            <a href="{{ route('login', ['redirect' => url()->current()]) }}"
                                class="text-indigo-600 dark:text-indigo-400 hover:underline font-semibold">Melde dich an</a>
                            oder <a href="{{ route('register') }}"
                                class="text-indigo-600 dark:text-indigo-400 hover:underline font-semibold">registriere
                                dich</a>, um eine Bewertung zu schreiben.
                        </p>
                    @endauth

                    <div class="space-y-6">
                        @forelse($reviews as $review)
                            <article
                                class="p-4 bg-white dark:bg-gray-800/70 rounded-lg shadow-sm flex gap-x-4 border border-gray-200 dark:border-gray-700/50">
                                <div class="flex-shrink-0">
                                    <img class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-700"
                                        src="https://ui-avatars.com/api/?name={{ urlencode($review->user->name ?? 'Gast') }}&color=7F9CF5&background=EBF4FF&size=128"
                                        alt="{{ $review->user->name ?? 'Anonym' }}">
                                </div>
                                <div class="flex-grow">
                                    <div class="flex items-baseline justify-between mb-1">
                                        <span
                                            class="font-semibold text-gray-900 dark:text-white">{{ $review->user->name ?? 'Anonym' }}</span>
                                        <time datetime="{{ $review->created_at->toIso8601String() }}"
                                            class="text-xs text-gray-500 dark:text-gray-400"
                                            title="{{ $review->created_at->format('d.m.Y H:i') }}">{{ $review->created_at->diffForHumans() }}</time>
                                    </div>
                                    <div class="flex items-center text-yellow-400 mb-1.5">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 fill-current {{ $i <= $review->rating ? '' : 'text-gray-300 dark:text-gray-600' }}"
                                                viewBox="0 0 20 20">
                                                <path
                                                    d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                            </svg>
                                        @endfor
                                    </div>
                                    @if ($review->comment)
                                        <div
                                            class="prose prose-sm dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 leading-relaxed">
                                            {!! nl2br(e($review->comment)) !!} </div>
                                    @endif
                                </div>
                            </article>
                        @empty
                            <p class="text-gray-600 dark:text-gray-400 italic py-4 text-center">Noch keine Kommentare
                                für diesen Club vorhanden.</p>
                        @endforelse

                        @if ($reviews->hasPages())
                            <div class="mt-8"> {{ $reviews->links(data: ['pageName' => 'reviewsPage']) }} </div>
                        @endif
                    </div>
                </section>

            </div>

            {{-- Sidebar (rechts) --}}
            <aside class="lg:col-span-1 space-y-6">
                {{-- Karte Placeholder --}}
                <div
                    class="bg-gray-200 dark:bg-gray-700 rounded-lg h-60 flex items-center justify-center text-gray-500 shadow">
                    [ Karte mit Club-Standort Placeholder ] </div>

                {{-- Adress- & Kontaktdaten --}}
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                    <h3
                        class="text-lg font-semibold mb-3 border-b border-gray-200 dark:border-gray-700 pb-2 text-gray-900 dark:text-white">
                        Adresse & Kontakt</h3>
                    <address class="not-italic text-sm text-gray-700 dark:text-gray-300 space-y-2">
                        @if ($club->fullAddressAttribute)
                            <p class="flex items-start"><svg class="w-4 h-4 mr-2 mt-0.5 text-gray-400 flex-shrink-0"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                        clip-rule="evenodd"></path>
                                </svg> {{ $club->fullAddressAttribute }}</p>
                        @endif
                        @if ($club->phone)
                            <p class="flex items-center"><svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z">
                                    </path>
                                </svg><a href="tel:{{ $club->phone }}"
                                    class="hover:text-indigo-600">{{ $club->phone }}</a></p>
                        @endif
                        @if ($club->email)
                            <p class="flex items-center"><svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z">
                                    </path>
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                </svg><a href="mailto:{{ $club->email }}"
                                    class="hover:text-indigo-600">{{ $club->email }}</a></p>
                        @endif
                        @if ($club->website)
                            <p class="flex items-center"><svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0l-4-4a2 2 0 012.828-2.828l3 3a2 2 0 010 2.828l-3 3a2 2 0 11-2.828-2.828l4-4a2 2 0 012.828 0z"
                                        clip-rule="evenodd"></path>
                                </svg><a href="{{ $club->website }}" target="_blank" rel="noopener noreferrer"
                                    class="hover:text-indigo-600">Webseite besuchen</a></p>
                        @endif
                    </address>
                </div>

                {{-- Öffnungszeiten --}}
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                    <h3
                        class="text-lg font-semibold mb-3 border-b border-gray-200 dark:border-gray-700 pb-2 text-gray-900 dark:text-white">
                        Öffnungszeiten
                    </h3>
                    <div class="text-sm text-gray-700 dark:text-gray-300 space-y-2">
                        @php
                            // Definiere die Tage und ihre deutschen Namen direkt hier
                            $daysOrder = [
                                'Mon' => 'Montag',
                                'Tue' => 'Dienstag',
                                'Wed' => 'Mittwoch',
                                'Thu' => 'Donnerstag',
                                'Fri' => 'Freitag',
                                'Sat' => 'Samstag',
                                'Sun' => 'Sonntag',
                            ];
                            // Hole das Öffnungszeiten-Array aus dem Model
                            $openingHoursData = $club->opening_hours ?? []; // Fallback auf leeres Array
                            $todayKey = date('D'); // Gibt 'Mon', 'Tue', etc. für heute zurück
                        @endphp

                        @foreach ($daysOrder as $dayKey => $dayName)
                            @php
                                // Hole die Zeit für den aktuellen Tag oder setze Default
                                $timeData = $openingHoursData[$dayKey] ?? null;
                                $isToday = $dayKey === $todayKey; // Prüfen, ob heute ist
                                $isClosed = $timeData === 'closed' || empty($timeData);
                                $displayTime = '<span class="text-gray-400 italic">n.a.</span>'; // Standard, falls kein Eintrag

                                if ($isClosed && $timeData === 'closed') {
                                    // Explizit 'closed' prüfen
                                    $displayTime = '<span class="text-red-500 dark:text-red-400">Geschlossen</span>';
                                } elseif (!empty($timeData) && $timeData !== 'closed') {
                                    $displayTime = e(str_replace('-', ' - ', $timeData)) . ' Uhr';
                                }
                            @endphp
                            {{-- Zeile für den Tag, ggf. hervorgehoben --}}
                            <div @class([
                                'flex justify-between items-center py-1',
                                'bg-indigo-50 dark:bg-indigo-900/30 px-2 -mx-2 rounded' => $isToday, // Hervorhebung für heute
                            ])>
                                <span @class([
                                    'font-medium w-20 sm:w-24', // Breite angepasst
                                    'text-indigo-700 dark:text-indigo-300' => $isToday, // Textfarbe für heute
                                ])>
                                    {{ $dayName }}:
                                </span>
                                <span @class([
                                    'text-right',
                                    'font-semibold' => $isToday && !$isClosed && $timeData !== 'closed',
                                ])> {{-- Zeit fett, wenn heute geöffnet --}}
                                    {!! $displayTime !!}
                                </span>
                            </div>
                        @endforeach

                        @if (empty($openingHoursData) && !collect($daysOrder)->contains(fn($name, $key) => isset($openingHoursData[$key])))
                            <p class="text-gray-500 italic mt-2">Keine Öffnungszeiten angegeben.</p>
                        @endif
                    </div>
                </div>

                {{-- Genres & Preisniveau --}}
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                    <h3
                        class="text-lg font-semibold mb-3 border-b border-gray-200 dark:border-gray-700 pb-2 text-gray-900 dark:text-white">
                        Musik & Preise</h3>
                    @if ($club->genres->isNotEmpty())
                        <div class="mb-3">
                            <h4 class="text-xs uppercase ...">Genres</h4>
                            <div class="flex flex-wrap gap-1">
                                @foreach ($club->genres as $genre)
                                    <span class="badge-default">{{ $genre->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if ($club->price_level)
                        <div>
                            <h4 class="text-xs uppercase ...">Preisniveau</h4>
                            <p class="text-sm ...">{{ $club->price_level }}</p>
                        </div>
                    @endif
                </div>

                {{-- Barrierefreiheit --}}
                @if (!empty($club->accessibility_features))
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                        <h3 class="text-lg font-semibold mb-3 border-b ...">Barrierefreiheit</h3>
                        <ul class="space-y-1 text-sm text-gray-700 dark:text-gray-300">
                            @if ($club->accessibility_features['wheelchair_accessible'] ?? false)
                                <li class="flex items-center"><svg class="w-4 h-4 mr-2 text-green-500"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"></path>
                                    </svg> Rollstuhlgerecht</li>
                            @endif
                            @if ($club->accessibility_features['accessible_restrooms'] ?? false)
                                <li class="flex items-center"><svg class="w-4 h-4 mr-2 text-green-500"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"></path>
                                    </svg> Barrierefreie Toiletten</li>
                            @endif
                            @if ($club->accessibility_features['low_counter'] ?? false)
                                <li class="flex items-center"><svg class="w-4 h-4 mr-2 text-green-500"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"></path>
                                    </svg> Niedriger Tresen</li>
                            @endif
                        </ul>
                        @if (!empty($club->accessibility_features['details']))
                            <p
                                class="mt-3 text-sm text-gray-600 dark:text-gray-400 border-t border-gray-200 dark:border-gray-700 pt-2">
                                {{ $club->accessibility_features['details'] }}</p>
                        @endif
                    </div>
                @endif
            </aside>

        </div>
    </div>

    {{-- Stelle sicher, dass die globalen Formular- und Button-Styles geladen werden (für Bewertungsformular) --}}
    {{-- @push('styles') <style> .badge-default { ... } </style> @endpush --}}

</x-frontend-layout>
