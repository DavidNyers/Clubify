@csrf
<div class="space-y-6"> {{-- Hauptcontainer --}}

    {{-- Sektion: Basisinformationen --}}
    <section>
        <h3 class="text-base font-semibold leading-7 text-gray-900 dark:text-gray-200">Basisinformationen</h3>
        <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-6">
            {{-- Name --}}
            <div class="sm:col-span-6">
                <label for="name"
                    class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200 mb-1">Eventname <span
                        class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" required
                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 dark:text-gray-900 bg-white dark:bg-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-500 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:focus:ring-indigo-500 sm:text-sm sm:leading-6"
                    value="{{ old('name', $event->name ?? '') }}">
                @error('name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Beschreibung --}}
            <div class="sm:col-span-6">
                <label for="description"
                    class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200 mb-1">Beschreibung</label>
                <textarea id="description" name="description" rows="6"
                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 dark:text-gray-900 bg-white dark:bg-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-500 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:focus:ring-indigo-500 sm:text-sm sm:leading-6 leading-relaxed">{{ old('description', $event->description ?? '') }}</textarea>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Detaillierte Beschreibung des Events. Markdown
                    oder Rich-Text (später).</p>
                @error('description')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Club Auswahl --}}
            <div class="sm:col-span-3">
                <label for="club_id"
                    class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200 mb-1">Club / Location
                    <span class="text-red-500">*</span></label>
                <select id="club_id" name="club_id" required
                    class="block w-full rounded-md border-0 py-1.5 px-3 pr-8 text-gray-900 dark:text-gray-900 bg-white dark:bg-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-500 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:focus:ring-indigo-500 sm:text-sm sm:leading-6">
                    <option value="">-- Bitte wählen --</option>
                    @foreach ($clubs as $id => $clubName)
                        <option value="{{ $id }}"
                            {{ old('club_id', $event->club_id ?? '') == $id ? 'selected' : '' }}>
                            {{ $clubName }}
                        </option>
                    @endforeach
                </select>
                @error('club_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Veranstalter Auswahl --}}
            <div class="sm:col-span-3">
                <label for="organizer_id"
                    class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200 mb-1">Veranstalter
                    (Optional)</label>
                <select id="organizer_id" name="organizer_id"
                    class="block w-full rounded-md border-0 py-1.5 px-3 pr-8 text-gray-900 dark:text-gray-900 bg-white dark:bg-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-500 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:focus:ring-indigo-500 sm:text-sm sm:leading-6">
                    <option value="">-- Kein Veranstalter zugewiesen --</option>
                    @foreach ($organizers as $organizer)
                        <option value="{{ $organizer->id }}"
                            {{ old('organizer_id', $event->organizer_id ?? '') == $organizer->id ? 'selected' : '' }}>
                            {{ $organizer->name }} ({{ $organizer->email }})
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Nur Benutzer mit der Rolle 'Organizer'.</p>
                @error('organizer_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Event Cover Bild --}}
            <div class="sm:col-span-6">
                <label for="cover_image" class="form-label">Coverbild</label>
                @if (isset($event) && $event->cover_image_path)
                    <div class="mt-2 mb-2">
                        <img src="{{ Storage::url($event->cover_image_path) }}"
                            alt="Aktuelles Coverbild für {{ $event->name }}" class="max-h-48 rounded-md shadow">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Aktuelles Bild. Ein neues Bild
                            überschreibt das alte.</p>
                    </div>
                @endif
                <input type="file" name="cover_image" id="cover_image"
                    class="form-input-field mt-1 file:mr-4 file:py-1.5 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-900 file:text-indigo-700 dark:file:text-indigo-300 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-800">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Empfohlen: JPG, PNG. Max. 2MB. Wird auf ca.
                    1200px Breite skaliert.</p>
                @error('cover_image')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </section>

    <div class="border-t border-gray-200 dark:border-white/10 pt-6"></div>

    {{-- Sektion: Zeit & Preis --}}
    <section>
        <h3 class="text-base font-semibold leading-7 text-gray-900 dark:text-gray-200">Zeit & Preis</h3>
        <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-6">
            {{-- Startzeit --}}
            <div class="sm:col-span-3">
                <label for="start_time"
                    class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200 mb-1">Startzeit <span
                        class="text-red-500">*</span></label>
                <input type="datetime-local" id="start_time" name="start_time" required
                    class="block w-full rounded-md border-0 py-1 px-2 text-gray-900 dark:text-gray-900 bg-white dark:bg-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-500 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:focus:ring-indigo-500 sm:text-sm sm:leading-6"
                    value="{{ old('start_time', isset($event->start_time) ? $event->start_time->format('Y-m-d\TH:i') : '') }}">
                @error('start_time')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Endzeit --}}
            <div class="sm:col-span-3">
                <label for="end_time"
                    class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200 mb-1">Endzeit
                    (Optional)</label>
                <input type="datetime-local" id="end_time" name="end_time"
                    class="block w-full rounded-md border-0 py-1 px-2 text-gray-900 dark:text-gray-900 bg-white dark:bg-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-500 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:focus:ring-indigo-500 sm:text-sm sm:leading-6"
                    value="{{ old('end_time', isset($event->end_time) ? $event->end_time->format('Y-m-d\TH:i') : '') }}">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Wenn leer, ist es ein Open-End-Event.</p>
                @error('end_time')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Preis --}}
            <div class="sm:col-span-3">
                <label for="price"
                    class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200 mb-1">Eintrittspreis
                    (Optional)</label>
                <input type="number" id="price" name="price" min="0" step="0.01"
                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 dark:text-gray-900 bg-white dark:bg-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-500 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:focus:ring-indigo-500 sm:text-sm sm:leading-6"
                    value="{{ old('price', $event->price ?? '') }}" placeholder="z.B. 15.00">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leer oder 0 für kostenlosen Eintritt.</p>
                @error('price')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Währung --}}
            <div class="sm:col-span-3">
                <label for="currency"
                    class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200 mb-1">Währung</label>
                <input type="text" id="currency" name="currency" maxlength="3" required
                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 dark:text-gray-900 bg-white dark:bg-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-500 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:focus:ring-indigo-500 sm:text-sm sm:leading-6"
                    value="{{ old('currency', $event->currency ?? 'EUR') }}">
                @error('currency')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </section>

    <div class="border-t border-gray-200 dark:border-white/10 pt-6"></div>

    {{-- Sektion: Lineup & Genres --}}
    <section>
        <h3 class="text-base font-semibold leading-7 text-gray-900 dark:text-gray-200">Lineup & Genres</h3>
        <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2"> {{-- 2 Spalten für Genres & DJs --}}
            {{-- Genres --}}
            <div x-data="{ genreSearch: '' }">
                <label for="genreSearchInputEvent"
                    class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200 mb-1">Musikgenres</label>
                <input type="search" id="genreSearchInputEvent" x-model="genreSearch"
                    placeholder="Genres filtern..."
                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 dark:text-gray-900 bg-white dark:bg-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-500 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:focus:ring-indigo-500 sm:text-sm sm:leading-6 mb-2">
                <div
                    class="mt-1 max-h-60 overflow-y-auto rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-100 p-2 space-y-1">
                    @php $currentGenres = old('genres', isset($event) ? $event->genres->pluck('id')->toArray() : []); @endphp
                    @forelse ($genres as $genre)
                        <div x-show="genreSearch === '' || '{{ strtolower($genre->name) }}'.includes(genreSearch.toLowerCase())"
                            x-transition
                            class="flex items-center justify-between hover:bg-gray-200 dark:hover:bg-gray-200 px-2 py-1 rounded">
                            <label for="event-genre-{{ $genre->id }}"
                                class="select-none text-sm font-medium text-gray-800 cursor-pointer flex-grow mr-2">{{ $genre->name }}</label>
                            <input id="event-genre-{{ $genre->id }}" name="genres[]" type="checkbox"
                                value="{{ $genre->id }}"
                                class="h-4 w-4 rounded border-gray-300 dark:border-gray-500 text-indigo-600 dark:text-indigo-500 focus:ring-indigo-600 dark:focus:ring-indigo-500 bg-white dark:bg-gray-200 checked:bg-indigo-600 dark:checked:bg-indigo-500"
                                @if (in_array($genre->id, $currentGenres)) checked @endif>
                        </div>
                    @empty
                        <p class="text-center text-sm italic text-gray-600 py-4">Keine Genres verfügbar.</p>
                    @endforelse
                </div>
                @error('genres')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                @error('genres.*')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- DJs --}}
            <div x-data="{ djSearch: '' }">
                <label for="djSearchInputEvent"
                    class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200 mb-1">Lineup /
                    DJs</label>
                <input type="search" id="djSearchInputEvent" x-model="djSearch" placeholder="DJs filtern..."
                    class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 dark:text-gray-900 bg-white dark:bg-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-500 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:focus:ring-indigo-500 sm:text-sm sm:leading-6 mb-2">
                <div
                    class="mt-1 max-h-60 overflow-y-auto rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-100 p-2 space-y-1">
                    @php $currentDjs = old('djs', isset($event) ? $event->djs->pluck('id')->toArray() : []); @endphp
                    @forelse ($djs as $dj)
                        <div x-show="djSearch === '' || '{{ strtolower($dj->name) }}'.includes(djSearch.toLowerCase())"
                            x-transition
                            class="flex items-center justify-between hover:bg-gray-200 dark:hover:bg-gray-200 px-2 py-1 rounded">
                            <label for="event-dj-{{ $dj->id }}"
                                class="select-none text-sm font-medium text-gray-800 cursor-pointer flex-grow mr-2">{{ $dj->name }}</label>
                            <input id="event-dj-{{ $dj->id }}" name="djs[]" type="checkbox"
                                value="{{ $dj->id }}"
                                class="h-4 w-4 rounded border-gray-300 dark:border-gray-500 text-indigo-600 dark:text-indigo-500 focus:ring-indigo-600 dark:focus:ring-indigo-500 bg-white dark:bg-gray-200 checked:bg-indigo-600 dark:checked:bg-indigo-500"
                                @if (in_array($dj->id, $currentDjs)) checked @endif>
                        </div>
                    @empty
                        <p class="text-center text-sm italic text-gray-600 py-4">Keine DJs (Benutzer mit Rolle 'DJ')
                            gefunden.</p>
                    @endforelse
                </div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Nur Benutzer mit der Rolle 'DJ'.</p>
                @error('djs')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                @error('djs.*')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </section>

    <div class="border-t border-gray-200 dark:border-white/10 pt-6"></div>

    {{-- Sektion: Optionen & Status --}}
    <section>
        <h3 class="text-base font-semibold leading-7 text-gray-900 dark:text-gray-200">Optionen & Status</h3>
        <div class="mt-4 space-y-5">
            {{-- Options Checkboxen --}}
            <fieldset>
                <legend class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-200 mb-1">Funktionen</legend>
                <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label
                        class="flex items-center space-x-2 cursor-pointer rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-100 p-3 hover:bg-gray-50 dark:hover:bg-gray-200">
                        <input id="allows_presale" name="allows_presale" type="checkbox" value="1"
                            class="h-4 w-4 rounded border-gray-300 dark:border-gray-500 text-indigo-600 dark:text-indigo-500 focus:ring-indigo-600 dark:focus:ring-indigo-500 bg-white dark:bg-gray-200 checked:bg-indigo-600 dark:checked:bg-indigo-500"
                            {{ old('allows_presale', $event->allows_presale ?? false) ? 'checked' : '' }}>
                        <span class="text-sm text-gray-800">Vorverkauf / Tischreservierung aktiviert</span>
                    </label>
                    <label
                        class="flex items-center space-x-2 cursor-pointer rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-100 p-3 hover:bg-gray-50 dark:hover:bg-gray-200">
                        <input id="allows_guestlist" name="allows_guestlist" type="checkbox" value="1"
                            class="h-4 w-4 rounded border-gray-300 dark:border-gray-500 text-indigo-600 dark:text-indigo-500 focus:ring-indigo-600 dark:focus:ring-indigo-500 bg-white dark:bg-gray-200 checked:bg-indigo-600 dark:checked:bg-indigo-500"
                            {{ old('allows_guestlist', $event->allows_guestlist ?? false) ? 'checked' : '' }}>
                        <span class="text-sm text-gray-800">Gästeliste aktiviert</span>
                    </label>
                </div>
            </fieldset>

            {{-- Status Checkboxen --}}
            <fieldset>
                <legend class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-200 mb-1">Status Management
                </legend>
                <div class="mt-2 flex flex-col space-y-2">
                    <label class="flex items-center cursor-pointer">
                        <input id="is_active" name="is_active" type="checkbox" value="1"
                            class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-600 dark:bg-gray-100 checked:bg-indigo-600 dark:checked:bg-indigo-500"
                            {{ old('is_active', $event->is_active ?? true) ? 'checked' : '' }}>
                        <span class="ml-2 text-sm leading-6 text-gray-900 dark:text-gray-200">Event ist aktiv
                            (sichtbar)</span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input id="requires_approval" name="requires_approval" type="checkbox" value="1"
                            class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-600 dark:bg-gray-100 checked:bg-indigo-600 dark:checked:bg-indigo-500"
                            {{ old('requires_approval', $event->requires_approval ?? true) ? 'checked' : '' }}>
                        <span class="ml-2 text-sm leading-6 text-gray-900 dark:text-gray-200">Benötigt
                            Admin-Freigabe</span>
                        <span class="ml-1 text-xs text-gray-500">(Wenn aktiv, wird Event bei Deaktivierung
                            unsichtbar)</span>
                    </label>
                    {{-- Nur im Edit-Mode anzeigen --}}
                    @if (isset($event))
                        <label class="flex items-center cursor-pointer">
                            <input id="is_cancelled" name="is_cancelled" type="checkbox" value="1"
                                class="h-4 w-4 rounded border-red-300 dark:border-red-500 text-red-600 focus:ring-red-600 dark:bg-gray-100 checked:bg-red-600 dark:checked:bg-red-500"
                                {{ old('is_cancelled', $event->isCancelled()) ? 'checked' : '' }}>
                            <span class="ml-2 text-sm leading-6 text-red-600 dark:text-red-400 font-medium">Event als
                                abgesagt markieren</span>
                        </label>
                    @endif
                </div>
            </fieldset>
        </div>
    </section>
</div>
