<x-frontend-layout :title="$title" :description="$description">
    <div x-data="{ lightboxOpen: false, lightboxImage: '' }" @keydown.escape.window="lightboxOpen = false" class="relative">

        {{-- Header-Bereich für den Club --}}
        <div
            class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 dark:from-indigo-800 dark:via-purple-800 dark:to-pink-800 text-white pt-10 pb-8 md:pt-12 md:pb-10 shadow-lg">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
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
                <h1 class="text-3xl md:text-4xl font-bold text-white mb-1">{{ $club->name }}</h1>
                @if ($club->city)
                    <p class="text-lg text-indigo-100 dark:text-indigo-300 opacity-90">{{ $club->city }}</p>
                @endif
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
                                    </svg>
                                @elseif ($roundedAvg >= $i - 0.5)
                                    <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0v15z" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 fill-current text-white opacity-30" viewBox="0 0 20 20">
                                        <path
                                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                    </svg>
                                @endif
                            @endfor
                        </div>
                        <span
                            class="ml-2 text-sm text-indigo-100 dark:text-indigo-300 opacity-90">{{ number_format($averageRating, 1) }}
                            ({{ $ratingCount }} {{ Str::plural('Bewertung', $ratingCount) }})</span>
                    @else
                        <span class="text-sm text-indigo-100 dark:text-indigo-300 opacity-80 italic">Noch keine
                            Bewertungen</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">
                <div class="lg:col-span-2 space-y-10">
                    <section id="gallery-carousel">
                        @if ($club->galleryImages->isNotEmpty())
                            @php
                                $images = $club->galleryImages
                                    ->map(fn($img) => asset('storage/' . $img->path))
                                    ->values()
                                    ->all();
                                $autoplayDuration = 5000;
                            @endphp
                            <div x-data="{
                                images: {{ json_encode($images) }},
                                activeIndex: 0,
                                isPaused: false,
                                isHovering: false,
                                progress: 0,
                                ticker: null,
                                autoplayDuration: {{ $autoplayDuration }},
                                tick() { if (this.isPaused || this.isHovering) return;
                                    this.progress += (100 / (this.autoplayDuration / 50)); if (this.progress >= 100) { this.next(); } },
                                changeSlide(index) { this.activeIndex = index;
                                    this.progress = 0; },
                                next() { this.changeSlide((this.activeIndex + 1) % this.images.length); },
                                prev() { this.changeSlide((this.activeIndex - 1 + this.images.length) % this.images.length); },
                                togglePause() { this.isPaused = !this.isPaused; },
                                isDragging: false,
                                startX: 0,
                                scrollLeft: 0,
                                handleDragStart(e) { const el = this.$refs.thumbnailsContainer;
                                    this.isDragging = true;
                                    this.startX = (e.pageX || e.touches[0].pageX) - el.offsetLeft;
                                    this.scrollLeft = el.scrollLeft; },
                                handleDragEnd() { this.isDragging = false; },
                                handleDrag(e) { if (!this.isDragging) return;
                                    e.preventDefault(); const x = (e.pageX || e.touches[0].pageX) - this.$refs.thumbnailsContainer.offsetLeft; const walk = (x - this.startX) * 2;
                                    this.$refs.thumbnailsContainer.scrollLeft = this.scrollLeft - walk; }
                            }" x-init="ticker = setInterval(() => tick(), 50)" @mouseenter="isHovering = true"
                                @mouseleave="isHovering = false">

                                <div class="relative bg-black rounded-lg shadow-2xl overflow-hidden">
                                    <div class="aspect-video w-full">
                                        <template x-for="(image, index) in images" :key="index">
                                            <div x-show="activeIndex === index"
                                                x-transition:enter="transition ease-in-out duration-500"
                                                x-transition:enter-start="opacity-0"
                                                x-transition:enter-end="opacity-100"
                                                x-transition:leave="transition ease-in-out duration-300 absolute"
                                                x-transition:leave-start="opacity-100"
                                                x-transition:leave-end="opacity-0" class="absolute inset-0"><img
                                                    :src="image"
                                                    @click="lightboxOpen = true; lightboxImage = image"
                                                    alt="Galeriebild {{ $club->name }}"
                                                    class="w-full h-full object-cover cursor-pointer"></div>
                                        </template>
                                    </div>
                                    <button @click="prev()"
                                        class="absolute left-3 top-1/2 -translate-y-1/2 bg-black/40 text-white p-2 rounded-full hover:bg-black/60 focus:outline-none transition z-10"><svg
                                            class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7" />
                                        </svg></button>
                                    <button @click="next()"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 bg-black/40 text-white p-2 rounded-full hover:bg-black/60 focus:outline-none transition z-10"><svg
                                            class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg></button>
                                    <div class="absolute top-3 right-3 flex items-center space-x-2 z-10">
                                        <button @click="togglePause()"
                                            class="bg-black/40 text-white p-2 rounded-full hover:bg-black/60 focus:outline-none transition"><span
                                                x-show="!isPaused" title="Pause"><svg class="w-5 h-5"
                                                    fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M5 4h3v12H5V4zm7 0h3v12h-3V4z" />
                                                </svg></span><span x-show="isPaused" title="Play"
                                                style="display: none;"><svg class="w-5 h-5" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path
                                                        d="M4.018 14.382A.999.999 0 005 15h10a1 1 0 00.982-1.618l-5-7a1 1 0 00-1.964 0l-5 7z"
                                                        transform="rotate(90 10 10)" />
                                                </svg></span></button>
                                        <div class="relative w-9 h-9"><svg class="w-full h-full" viewBox="0 0 36 36">
                                                <circle class="text-white/20" stroke-width="4" stroke="currentColor"
                                                    fill="transparent" r="16" cx="18" cy="18" />
                                                <circle class="text-white" stroke-width="4" :stroke-dasharray="100"
                                                    :stroke-dashoffset="100 - progress" stroke-linecap="round"
                                                    stroke="currentColor" fill="transparent" r="16" cx="18"
                                                    cy="18"
                                                    style="transform: rotate(-90deg); transform-origin: 50% 50%;" />
                                            </svg></div>
                                    </div>
                                </div>
                                <div class="mt-4 relative">
                                    <div
                                        class="absolute inset-y-0 left-0 w-8 bg-gradient-to-r from-gray-50 dark:from-gray-900 to-transparent pointer-events-none z-10">
                                    </div>
                                    <div
                                        class="absolute inset-y-0 right-0 w-8 bg-gradient-to-l from-gray-50 dark:from-gray-900 to-transparent pointer-events-none z-10">
                                    </div>
                                    <div x-ref="thumbnailsContainer"
                                        class="flex space-x-3 overflow-x-auto pb-2 px-2 no-scrollbar"
                                        :class="{ 'cursor-grab': !isDragging, 'cursor-grabbing': isDragging }"
                                        @mousedown.prevent="handleDragStart" @touchstart.passive="handleDragStart"
                                        @mouseleave="handleDragEnd" @touchend="handleDragEnd"
                                        @mousemove.throttle.16="handleDrag"
                                        @touchmove.throttle.16.passive="handleDrag">
                                        <template x-for="(image, index) in images" :key="index"><button
                                                @click="changeSlide(index)"
                                                :class="{ 'ring-2 ring-indigo-500 ring-offset-2 dark:ring-offset-gray-900': activeIndex ===
                                                        index }"
                                                class="flex-shrink-0 w-24 rounded-md overflow-hidden focus:outline-none transition bg-black">
                                                <div class="aspect-video w-full"><img :src="image"
                                                        alt="Thumbnail"
                                                        class="w-full h-full object-cover pointer-events-none"></div>
                                            </button></template>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div
                                class="bg-gray-200 dark:bg-gray-800/50 rounded-lg aspect-video w-full flex items-center justify-center text-gray-500 dark:text-gray-400 border border-dashed border-gray-300 dark:border-gray-700">
                                <p>Keine Galeriebilder vorhanden</p>
                            </div>
                        @endif
                    </section>

                    @if ($club->description)
                        <section>
                            <h2 class="text-2xl font-semibold mb-3 text-gray-900 dark:text-white">Beschreibung</h2>
                            <div class="prose prose-lg dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                                {!! nl2br(e($club->description)) !!}</div>
                        </section>
                    @endif
                    <section>
                        <h2 class="text-2xl font-semibold mb-4 text-gray-900 dark:text-white">Anstehende Events</h2>
                        @if ($club->upcomingEvents->isNotEmpty())
                            <div class="space-y-4">
                                @foreach ($club->upcomingEvents as $event)
                                    <a href="{{ route('events.show', $event) }}"
                                        class="block p-4 bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                                        <div class="flex flex-col sm:flex-row gap-4 items-start">
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
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-0.5">
                                                    {{ $event->name }}</h3>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $event->start_time->format('H:i') }} Uhr @if ($event->end_time)
                                                        - {{ $event->end_time->format('H:i') }} Uhr
                                                    @endif
                                                </p>
                                                @if ($event->genres->isNotEmpty())
                                                    <p class="text-xs mt-1 text-indigo-500 dark:text-indigo-400">
                                                        {{ $event->genres->take(3)->pluck('name')->implode(', ') }}</p>
                                                @endif
                                            </div>
                                            <div class="flex-shrink-0 text-right self-center mt-2 sm:mt-0"><span
                                                    class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-1">{{ $event->formattedPriceAttribute }}</span><span
                                                    class="inline-block btn-secondary !text-xs !py-1 !px-3">Details
                                                    ansehen</span></div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else<p class="text-gray-600 dark:text-gray-400 italic">Zurzeit keine anstehenden Events in
                                diesem Club geplant.</p>
                        @endif
                    </section>
                    <section id="reviews" class="pt-8 mt-8 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Bewertungen @if ($ratingCount > 0)
                                    ({{ $ratingCount }})
                                @endif
                            </h2>
                        </div>@auth @if (Auth::user()->hasVerifiedEmail())
                            <div
                                class="mb-8 bg-white dark:bg-gray-800/50 p-4 sm:p-6 rounded-lg shadow-md border dark:border-gray-700">
                                <h3 class="text-lg font-medium mb-3 text-gray-900 dark:text-white">Deine Bewertung für
                                    {{ $club->name }}:</h3>
                                @if (session('rating_success'))
                                    <div
                                        class="mb-4 p-3 bg-green-100 dark:bg-green-700 border border-green-200 dark:border-green-600 text-green-700 dark:text-green-200 rounded text-sm">
                                        {{ session('rating_success') }}</div>
                                    @endif @if (session('rating_error'))
                                        <div
                                            class="mb-4 p-3 bg-red-100 dark:bg-red-700 border border-red-200 dark:border-red-600 text-red-700 dark:text-red-200 rounded text-sm">
                                            {{ session('rating_error') }}</div>
                                    @endif
                                    <form action="{{ route('ratings.store', $club) }}" method="POST">
                                        @csrf<div class="mb-4" x-data="{ rating: {{ old('rating', 0) }}, hoverRating: 0 }"><label
                                                class="form-label mb-1">Bewertung (Sterne):</label>
                                            <div
                                                class="flex items-center space-x-1 text-2xl text-gray-300 dark:text-gray-600">
                                                @for ($s = 1; $s <= 5; $s++)
                                                    <button type="button" @click="rating = {{ $s }}"
                                                        @mouseenter="hoverRating = {{ $s }}"
                                                        @mouseleave="hoverRating = 0"
                                                        class="focus:outline-none transition-colors duration-150"
                                                        title="{{ $s }} Stern{{ $s > 1 ? 'e' : '' }}"><svg
                                                            class="w-7 h-7"
                                                            :class="{ 'text-yellow-400 dark:text-yellow-400': hoverRating >=
                                                                    {{ $s }} || rating >=
                                                                    {{ $s }}, 'text-gray-300 dark:text-gray-600': hoverRating <
                                                                    {{ $s }} && rating <
                                                                    {{ $s }} }"
                                                            fill="currentColor" viewBox="0 0 20 20">
                                                            <path
                                                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                                        </svg></button>
                                                @endfor
                                                <input type="hidden" name="rating" x-model.number="rating">
                                            </div>
                                            @error('rating')
                                                <p class="form-error text-xs mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div><label for="comment" class="form-label">Kommentar (Optional):</label>
                                            <textarea name="comment" id="comment" rows="3" class="form-textarea-field w-full mt-1"
                                                placeholder="Deine Erfahrungen mit dem Club...">{{ old('comment') }}</textarea>
                                            @error('comment')
                                                <p class="form-error mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="mt-4 text-right"><button type="submit"
                                                class="btn-primary">Bewertung absenden</button></div>
                                    </form>
                        </div>@else<p
                                class="mb-6 text-sm text-gray-600 dark:text-gray-400 p-4 bg-yellow-50 dark:bg-gray-700 rounded-md">
                                Bitte <a href="{{ route('verification.notice') }}"
                                    class="text-indigo-600 dark:text-indigo-400 hover:underline">verifiziere deine
                                    E-Mail-Adresse</a>, um eine Bewertung abzugeben.</p>
                    @endif @else<p
                            class="mb-6 text-sm text-gray-600 dark:text-gray-400 p-4 bg-gray-100 dark:bg-gray-700/50 rounded-md">
                            <a href="{{ route('login', ['redirect' => url()->current()]) }}"
                                class="text-indigo-600 dark:text-indigo-400 hover:underline font-semibold">Melde dich
                                an</a> oder <a href="{{ route('register') }}"
                                class="text-indigo-600 dark:text-indigo-400 hover:underline font-semibold">registriere
                            dich</a>, um eine Bewertung zu schreiben.</p>@endauth
                    <div class="space-y-6">
                        @forelse($reviews as $review)
                            <article
                                class="p-4 bg-white dark:bg-gray-800/70 rounded-lg shadow-sm flex gap-x-4 border border-gray-200 dark:border-gray-700/50">
                                <div class="flex-shrink-0"><img
                                        class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-700"
                                        src="https://ui-avatars.com/api/?name={{ urlencode($review->user->name ?? 'Gast') }}&color=7F9CF5&background=EBF4FF&size=128"
                                        alt="{{ $review->user->name ?? 'Anonym' }}"></div>
                                <div class="flex-grow">
                                    <div class="flex items-baseline justify-between mb-1"><span
                                            class="font-semibold text-gray-900 dark:text-white">{{ $review->user->name ?? 'Anonym' }}</span><time
                                            datetime="{{ $review->created_at->toIso8601String() }}"
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
                                            {!! nl2br(e($review->comment)) !!}</div>
                                    @endif
                                </div>
                        </article>@empty<p class="text-gray-600 dark:text-gray-400 italic py-4 text-center">
                                Noch keine Kommentare für diesen Club vorhanden.</p>@endforelse @if ($reviews->hasPages())
                                <div class="mt-8"> {{ $reviews->links(data: ['pageName' => 'reviewsPage']) }}
                                </div>
                            @endif
                    </div>
                </section>
            </div>

            <aside class="lg:col-span-1 space-y-6">
                {{-- <<<< HIER IST DIE NEUE, FUNKTIONIERENDE KARTE >>>> --}}
                @if ($club->latitude && $club->longitude)
                    <a href="https://www.google.com/maps/search/?api=1&query={{ $club->latitude }},{{ $club->longitude }}"
                        target="_blank" rel="noopener noreferrer" class="block group">
                        <div id="club-map"
                            class="bg-gray-200 dark:bg-gray-700 rounded-lg h-60 shadow z-0 group-hover:ring-2 group-hover:ring-indigo-500 transition-all duration-300">
                        </div>
                    </a>
                @else
                    <div
                        class="bg-gray-200 dark:bg-gray-700 rounded-lg h-60 shadow z-0 flex items-center justify-center text-gray-500 dark:text-gray-400 italic">
                        Keine Standortdaten verfügbar
                    </div>
                @endif

                {{-- Rest der Sidebar bleibt unverändert --}}
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                    <h3
                        class="text-lg font-semibold mb-3 border-b border-gray-200 dark:border-gray-700 pb-2 text-gray-900 dark:text-white">
                        Adresse & Kontakt</h3>
                    <address class="not-italic text-sm text-gray-700 dark:text-gray-300 space-y-2">
                        @if ($club->fullAddressAttribute)
                            <p class="flex items-start"><svg
                                    class="w-4 h-4 mr-2 mt-0.5 text-gray-400 flex-shrink-0" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                        clip-rule="evenodd"></path>
                                </svg> {{ $club->fullAddressAttribute }}</p>
                            @endif @if ($club->phone)
                                <p class="flex items-center"><svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z">
                                        </path>
                                    </svg><a href="tel:{{ $club->phone }}"
                                        class="hover:text-indigo-600">{{ $club->phone }}</a></p>
                                @endif @if ($club->email)
                                    <p class="flex items-center"><svg
                                            class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path
                                                d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z">
                                            </path>
                                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z">
                                            </path>
                                        </svg><a href="mailto:{{ $club->email }}"
                                            class="hover:text-indigo-600">{{ $club->email }}</a></p>
                                    @endif @if ($club->website)
                                        <p class="flex items-center"><svg
                                                class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0"
                                                fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0l-4-4a2 2 0 012.828-2.828l3 3a2 2 0 010 2.828l-3 3a2 2 0 11-2.828-2.828l4-4a2 2 0 012.828 0z"
                                                    clip-rule="evenodd"></path>
                                            </svg><a href="{{ $club->website }}" target="_blank"
                                                rel="noopener noreferrer" class="hover:text-indigo-600">Webseite
                                                besuchen</a></p>
                                    @endif
                    </address>
                </div>
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                    <h3
                        class="text-lg font-semibold mb-3 border-b border-gray-200 dark:border-gray-700 pb-2 text-gray-900 dark:text-white">
                        Öffnungszeiten</h3>
                    <div class="text-sm text-gray-700 dark:text-gray-300 space-y-2">@php
                        $daysOrder = ['Mon' => 'Montag', 'Tue' => 'Dienstag', 'Wed' => 'Mittwoch', 'Thu' => 'Donnerstag', 'Fri' => 'Freitag', 'Sat' => 'Samstag', 'Sun' => 'Sonntag'];
                        $openingHoursData = $club->opening_hours ?? [];
                        $todayKey = date('D');
                    @endphp @foreach ($daysOrder as $dayKey => $dayName)
                            @php
                                $timeData = $openingHoursData[$dayKey] ?? null;
                                $isToday = $dayKey === $todayKey;
                                $isClosed = $timeData === 'closed' || empty($timeData);
                                $displayTime = '<span class="text-gray-400 italic">n.a.</span>';
                                if ($isClosed && $timeData === 'closed') {
                                    $displayTime = '<span class="text-red-500 dark:text-red-400">Geschlossen</span>';
                                } elseif (!empty($timeData) && $timeData !== 'closed') {
                                    $displayTime = e(str_replace('-', ' - ', $timeData)) . ' Uhr';
                                }
                            @endphp<div @class([
                                'flex justify-between items-center py-1',
                                'bg-indigo-50 dark:bg-indigo-900/30 px-2 -mx-2 rounded' => $isToday,
                            ])><span
                                    @class([
                                        'font-medium w-20 sm:w-24',
                                        'text-indigo-700 dark:text-indigo-300' => $isToday,
                                    ])>{{ $dayName }}:</span><span
                                    @class([
                                        'text-right',
                                        'font-semibold' => $isToday && !$isClosed && $timeData !== 'closed',
                                    ])>{!! $displayTime !!}</span></div>
                            @endforeach @if (empty($openingHoursData) && !collect($daysOrder)->contains(fn($name, $key) => isset($openingHoursData[$key])))
                                <p class="text-gray-500 italic mt-2">Keine Öffnungszeiten angegeben.</p>
                            @endif
                    </div>
                </div>
                @if (
                    $club->genres->isNotEmpty() ||
                        $club->price_level ||
                        !empty(array_filter((array) ($club->accessibility_features ?? []))))
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                        <h3
                            class="text-lg font-semibold mb-3 border-b border-gray-200 dark:border-gray-700 pb-2 text-gray-900 dark:text-white">
                            Details</h3>
                        <div class="space-y-4">
                            @if ($club->genres->isNotEmpty())
                                <div>
                                    <h4
                                        class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400 tracking-wider mb-1">
                                        Genres</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($club->genres as $genre)
                                            <span class="badge-default">{{ $genre->name }}</span>
                                        @endforeach
                                    </div>
                                </div>@endif @if ($club->price_level)
                                    <div>
                                        <h4
                                            class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400 tracking-wider mb-1">
                                            Preisniveau</h4>
                                        <p class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                                            {{ $club->price_level }}</p>
                                    </div>
                                    @endif @if (!empty(array_filter((array) ($club->accessibility_features ?? []))))
                                        <div>
                                            <h4
                                                class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400 tracking-wider mb-1">
                                                Barrierefreiheit</h4>
                                            <ul class="space-y-1 text-sm text-gray-700 dark:text-gray-300">
                                                @if ($club->accessibility_features['wheelchair_accessible'] ?? false)
                                                    <li class="flex items-center"><svg
                                                            class="w-4 h-4 mr-2 text-green-500"
                                                            fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                clip-rule="evenodd"></path>
                                                        </svg> Rollstuhlgerecht</li>
                                                    @endif @if ($club->accessibility_features['accessible_restrooms'] ?? false)
                                                        <li class="flex items-center"><svg
                                                                class="w-4 h-4 mr-2 text-green-500"
                                                                fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                    clip-rule="evenodd"></path>
                                                            </svg> Barrierefreie Toiletten</li>
                                                        @endif @if ($club->accessibility_features['low_counter'] ?? false)
                                                            <li class="flex items-center"><svg
                                                                    class="w-4 h-4 mr-2 text-green-500"
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
                        </div>
                    </div>
                @endif
            </aside>
        </div>
    </div>

    <div x-show="lightboxOpen" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50 p-4"
        @click.self="lightboxOpen = false" style="display: none;">
        <button @click="lightboxOpen = false"
            class="absolute top-4 right-4 text-white text-4xl opacity-80 hover:opacity-100 focus:outline-none z-10">×</button>
        <img :src="lightboxImage" alt="Galeriebild in Großansicht"
            class="max-w-[90vw] max-h-[90vh] rounded-lg shadow-2xl">
    </div>

    @push('styles')
        <style>
            .no-scrollbar::-webkit-scrollbar {
                display: none;
            }

            .no-scrollbar {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
        </style>
    @endpush
    @push('scripts')
        @if ($club->latitude && $club->longitude)
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const lat = {{ $club->latitude }};
                    const lng = {{ $club->longitude }};
                    const clubName = "{{ Js::from($club->name) }}";
                    const mapElement = document.getElementById('club-map');

                    if (!mapElement || typeof L === 'undefined') return;

                    let map = L.map(mapElement, {
                        scrollWheelZoom: false,
                        zoomControl: false, // Deaktiviert die +/- Zoom-Buttons
                        dragging: false, // Deaktiviert das Ziehen der Karte
                        tap: false, // Deaktiviert Klick/Tap-Events auf der Karte
                        touchZoom: false // Deaktiviert Pinch-to-Zoom
                    }).setView([lat, lng], 15);

                    let activeTileLayer = null;

                    function setTileLayer() {
                        const isDarkMode = document.documentElement.classList.contains('dark');
                        const newTileUrl = isDarkMode ?
                            'https://{s}.basemaps.cartocdn.com/dark_nolabels/{z}/{x}/{y}{r}.png' // Carto Dark Matter (S/W)
                            :
                            'https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png'; // Carto Positron (S/W)

                        if (activeTileLayer) map.removeLayer(activeTileLayer);

                        activeTileLayer = L.tileLayer(newTileUrl, {
                            attribution: '© <a href="https://www.openstreetmap.org/copyright">OSM</a> © <a href="https://carto.com/attributions">CARTO</a>'
                        }).addTo(map);
                    }

                    L.marker([lat, lng]).addTo(map);
                    setTileLayer();

                    const themeObserver = new MutationObserver(() => setTileLayer());
                    themeObserver.observe(document.documentElement, {
                        attributes: true,
                        attributeFilter: ['class']
                    });
                });
            </script>
        @endif
    @endpush
</div>
</x-frontend-layout>
