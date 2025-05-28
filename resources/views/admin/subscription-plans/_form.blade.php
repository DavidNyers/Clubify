@csrf
<div class="space-y-6"> {{-- Haupt-Grid-Container mit vertikalem Abstand --}}

    {{-- Sektion: Basisinformationen --}}
    <section>
        <h3 class="text-base font-semibold leading-7 text-gray-900 dark:text-gray-200">Basisinformationen</h3>
        <p class="mt-1 text-sm leading-6 text-gray-600 dark:text-gray-400">Grundlegende Details und Status des Clubs.</p>
        <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
            {{-- Name --}}
            <div class="sm:col-span-4">
                <label for="name" class="form-label">Clubname <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" required class="form-input-field mt-1"
                    value="{{ old('name', $club->name ?? '') }}">
                @error('name')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Besitzer (ClubOwner) --}}
            <div class="sm:col-span-2">
                <label for="owner_id" class="form-label">Besitzer (Optional)</label>
                <select id="owner_id" name="owner_id" class="form-select-field mt-1">
                    <option value="">-- Kein Besitzer --</option>
                    @foreach ($clubOwners as $owner)
                        {{-- $clubOwners muss vom Controller kommen --}}
                        <option value="{{ $owner->id }}"
                            {{ old('owner_id', $club->owner_id ?? '') == $owner->id ? 'selected' : '' }}>
                            {{ $owner->name }}
                        </option>
                    @endforeach
                </select>
                @error('owner_id')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Beschreibung --}}
            <div class="sm:col-span-6">
                <label for="description" class="form-label">Beschreibung</label>
                <textarea id="description" name="description" rows="5" class="form-textarea-field mt-1">{{ old('description', $club->description ?? '') }}</textarea>
                @error('description')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status Checkboxen --}}
            <div class="sm:col-span-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <label
                    class="flex items-center space-x-3 cursor-pointer p-3 border dark:border-gray-700 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <input id="is_active" name="is_active" type="checkbox" value="1" class="form-checkbox-field"
                        {{ old('is_active', $club->is_active ?? true) ? 'checked' : '' }}>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Club ist aktiv (sichtbar)</span>
                </label>
                <label
                    class="flex items-center space-x-3 cursor-pointer p-3 border dark:border-gray-700 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <input id="is_verified" name="is_verified" type="checkbox" value="1"
                        class="form-checkbox-field"
                        {{ old('is_verified', $club->is_verified ?? false) ? 'checked' : '' }}>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Club ist verifiziert
                        (Admin)</span>
                </label>
            </div>
        </div>
    </section>

    <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6"></div>

    {{-- Sektion: Standort --}}
    <section>
        <h3 class="text-base font-semibold leading-7 text-gray-900 dark:text-gray-200">Standort</h3>
        <p class="mt-1 text-sm leading-6 text-gray-600 dark:text-gray-400">Adresse und geografische Koordinaten.</p>
        <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
            <div class="sm:col-span-4">
                <label for="street_address" class="form-label">Straße & Nr.</label>
                <input type="text" name="street_address" id="street_address" class="form-input-field mt-1"
                    value="{{ old('street_address', $club->street_address ?? '') }}">
                @error('street_address')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
            <div class="sm:col-span-2">
                <label for="zip_code" class="form-label">PLZ</label>
                <input type="text" name="zip_code" id="zip_code" class="form-input-field mt-1"
                    value="{{ old('zip_code', $club->zip_code ?? '') }}">
                @error('zip_code')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
            <div class="sm:col-span-3">
                <label for="city" class="form-label">Stadt</label>
                <input type="text" name="city" id="city" class="form-input-field mt-1"
                    value="{{ old('city', $club->city ?? '') }}">
                @error('city')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
            <div class="sm:col-span-3">
                <label for="country" class="form-label">Land <span class="text-red-500">*</span></label>
                <select name="country" id="country" required class="form-select-field mt-1">
                    @foreach ($countries as $code => $name)
                        {{-- $countries muss vom Controller kommen --}}
                        <option value="{{ $code }}"
                            {{ strtolower(old('country', $club->country ?? config('app.country_code', 'DE'))) == strtolower($code) ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
                @error('country')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
            <div class="sm:col-span-3">
                <label for="latitude" class="form-label">Latitude <span
                        class="text-xs text-gray-500">(Optional)</span></label>
                <input id="latitude" name="latitude" type="number" step="any" class="form-input-field mt-1"
                    value="{{ old('latitude', $club->latitude ?? '') }}" />
                @error('latitude')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
            <div class="sm:col-span-3">
                <label for="longitude" class="form-label">Longitude <span
                        class="text-xs text-gray-500">(Optional)</span></label>
                <input id="longitude" name="longitude" type="number" step="any" class="form-input-field mt-1"
                    value="{{ old('longitude', $club->longitude ?? '') }}" />
                @error('longitude')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </section>

    <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6"></div>

    {{-- Sektion: Kontakt & Webseite --}}
    <section>
        <h3 class="text-base font-semibold leading-7 text-gray-900 dark:text-gray-200">Kontakt & Webseite</h3>
        <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
            <div class="sm:col-span-3">
                <label for="website" class="form-label">Webseite</label>
                <input type="url" name="website" id="website" class="form-input-field mt-1"
                    value="{{ old('website', $club->website ?? '') }}" placeholder="https://...">
                @error('website')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
            <div class="sm:col-span-3">
                <label for="phone" class="form-label">Telefon</label>
                <input type="tel" name="phone" id="phone" class="form-input-field mt-1"
                    value="{{ old('phone', $club->phone ?? '') }}">
                @error('phone')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
            <div class="sm:col-span-3">
                <label for="email" class="form-label">Kontakt E-Mail (öffentlich)</label>
                <input type="email" name="email" id="email" class="form-input-field mt-1"
                    value="{{ old('email', $club->email ?? '') }}">
                @error('email')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
            <div class="sm:col-span-3">
                <label for="price_level" class="form-label">Preisniveau</label>
                <select id="price_level" name="price_level" class="form-select-field mt-1">
                    <option value="" {{ old('price_level', $club->price_level ?? '') == '' ? 'selected' : '' }}>
                        Keine Angabe</option>
                    <option value="$"
                        {{ old('price_level', $club->price_level ?? '') == '$' ? 'selected' : '' }}>$ (Günstig)
                    </option>
                    <option value="$$"
                        {{ old('price_level', $club->price_level ?? '') == '$$' ? 'selected' : '' }}>$$ (Mittel)
                    </option>
                    <option value="$$$"
                        {{ old('price_level', $club->price_level ?? '') == '$$$' ? 'selected' : '' }}>$$$ (Teuer)
                    </option>
                </select>
                @error('price_level')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </section>

    <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6"></div>

    {{-- Sektion: Musik & Öffnungszeiten --}}
    <section>
        <h3 class="text-base font-semibold leading-7 text-gray-900 dark:text-gray-200">Musik & Öffnungszeiten</h3>
        <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2"> {{-- 2 Spalten für Genres & Zeiten --}}
            {{-- Genres (Checkbox Liste mit Suche) --}}
            <div x-data="{ genreSearch: '' }">
                <label for="clubGenreSearch" class="form-label">Musikgenres</label>
                <input type="search" x-model="genreSearch" id="clubGenreSearch" placeholder="Genres filtern..."
                    class="form-input-field text-sm mb-2 mt-1">
                <div class="mt-1 max-h-60 overflow-y-auto form-content-box">
                    <div class="divide-y divide-gray-200 dark:divide-gray-600 px-1">
                        @php $currentClubGenres = old('genres', isset($club) ? $club->genres->pluck('id')->toArray() : []); @endphp
                        @forelse ($genres as $genre)
                            {{-- $genres muss vom Controller kommen --}}
                            <div x-show="genreSearch === '' || '{{ strtolower($genre->name) }}'.includes(genreSearch.toLowerCase())"
                                x-transition class="flex items-center justify-between py-1.5">
                                <label for="club-genre-{{ $genre->id }}"
                                    class="select-none text-sm font-medium cursor-pointer flex-grow mr-2">{{ $genre->name }}</label>
                                <input id="club-genre-{{ $genre->id }}" name="genres[]" type="checkbox"
                                    value="{{ $genre->id }}" class="form-checkbox-field"
                                    @if (in_array($genre->id, $currentClubGenres)) checked @endif>
                            </div>
                        @empty
                            <p class="text-center text-sm italic py-4">Keine Genres definiert.</p>
                        @endforelse
                    </div>
                </div>
                @error('genres')
                    <p class="form-error">{{ $message }}</p>
                @enderror
                @error('genres.*')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Öffnungszeiten (Strukturiert) --}}
            <div>
                <label class="form-label mb-1">Öffnungszeiten</label>
                <div class="space-y-2 form-content-box">
                    @php $days = ['Mon'=>'Mo', 'Tue'=>'Di', 'Wed'=>'Mi', 'Thu'=>'Do', 'Fri'=>'Fr', 'Sat'=>'Sa', 'Sun'=>'So']; @endphp
                    @foreach ($days as $key => $label)
                        @php
                            $dayData = old(
                                'opening_hours_structured.' . $key,
                                $openingHoursStructured[$key] ?? ['start' => '', 'end' => '', 'closed' => false],
                            );
                        @endphp
                        <div x-data="{ closed: {{ $dayData['closed'] ? 'true' : 'false' }} }" class="flex items-center space-x-2 md:space-x-3">
                            <span class="w-8 flex-shrink-0 text-sm font-medium text-center">{{ $label }}</span>
                            <div class="flex-shrink-0">
                                <label class="flex items-center space-x-1 cursor-pointer">
                                    <input type="checkbox"
                                        name="opening_hours_structured[{{ $key }}][closed]" value="1"
                                        x-model="closed" class="form-checkbox-field">
                                    <span class="text-xs">Geschl.</span>
                                </label>
                            </div>
                            <div class="flex items-center space-x-1 flex-grow" :class="{ 'opacity-40': closed }">
                                <label for="club_oh_{{ $key }}_start" class="sr-only">Start</label>
                                <input type="time" id="club_oh_{{ $key }}_start"
                                    name="opening_hours_structured[{{ $key }}][start]"
                                    class="form-input-field" :disabled="closed" value="{{ $dayData['start'] }}">
                                <span class="text-gray-500 dark:text-gray-400">-</span>
                                <label for="club_oh_{{ $key }}_end" class="sr-only">Ende</label>
                                <input type="time" id="club_oh_{{ $key }}_end"
                                    name="opening_hours_structured[{{ $key }}][end]"
                                    class="form-input-field" :disabled="closed" value="{{ $dayData['end'] }}">
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('opening_hours')
                    <p class="form-error">{{ $message }}</p>
                @enderror
                @error('opening_hours_json')
                    <p class="form-error">{{ $message }}</p>
                @enderror {{-- Für Controller-JSON-Fehler --}}
            </div>
        </div>
    </section>

    <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6"></div>

    {{-- Sektion: Club Galerie --}}
    <section>
        <h3 class="text-base font-semibold leading-7 text-gray-900 dark:text-gray-200">Club Galerie</h3>
        <p class="mt-1 text-sm leading-6 text-gray-600 dark:text-gray-400">Lade hier Bilder für die Galerie des Clubs
            hoch.</p>
        <div class="mt-4 space-y-6">
            {{-- Feld für neue Bilder --}}
            <div>
                <label for="gallery_images" class="form-label">Neue Bilder hinzufügen <span
                        class="text-xs text-gray-500">(Mehrfachauswahl möglich)</span></label>
                <input type="file" name="gallery_images[]" id="gallery_images" multiple
                    class="form-input-field mt-1 file:mr-4 file:py-1.5 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-100 dark:file:bg-indigo-900 file:text-indigo-700 dark:file:text-indigo-300 hover:file:bg-indigo-200 dark:hover:file:bg-indigo-800 cursor-pointer">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Erlaubt: JPG, PNG, WebP, GIF. Max. 5MB pro
                    Bild.</p>
                @error('gallery_images')
                    <p class="form-error">{{ $message }}</p>
                @enderror>
                @error('gallery_images.*')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Bereits hochgeladene Bilder anzeigen (nur im Edit-Modus) --}}
            @if (isset($club) && $club->galleryImages->isNotEmpty())
                <div class="mt-6">
                    <p class="form-label mb-2">Vorhandene Galeriebilder:</p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                        @foreach ($club->galleryImages as $image)
                            <div
                                class="relative group border border-gray-300 dark:border-gray-600 rounded-md p-1 shadow-sm">
                                <img src="{{ Storage::url($image->path) }}"
                                    alt="{{ $image->caption ?? 'Galeriebild ' . $loop->iteration }}"
                                    class="rounded-sm aspect-square object-cover">
                                <div class="absolute top-1.5 right-1.5 z-10">
                                    <label for="delete_images_{{ $image->id }}"
                                        class="flex items-center justify-center p-1 bg-red-600 text-white rounded-full cursor-pointer hover:bg-red-700 shadow-md transition-colors w-6 h-6"
                                        title="Bild zum Löschen markieren">
                                        <input type="checkbox" name="delete_images[]" value="{{ $image->id }}"
                                            id="delete_images_{{ $image->id }}" class="sr-only peer">
                                        <svg class="w-3 h-3 peer-checked:hidden" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        <svg class="w-3 h-3 hidden peer-checked:block text-white" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    </label>
                                </div>
                                {{-- TODO: Optional Felder für Caption/Order hier pro Bild --}}
                            </div>
                        @endforeach
                    </div>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Markiere Bilder zum Löschen und speichere
                        das Formular.</p>
                </div>
            @endif
        </div>
    </section>

    <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6"></div>

    {{-- Sektion: Zugänglichkeit --}}
    <section>
        <h3 class="text-base font-semibold leading-7 text-gray-900 dark:text-gray-200">Barrierefreiheit</h3>
        <div class="mt-4 space-y-4 form-content-box">
            @php
                $accessData = old(
                    'accessibility_structured',
                    $accessibilityStructured ?? [
                        'wheelchair_accessible' => false,
                        'accessible_restrooms' => false,
                        'low_counter' => false,
                        'details' => '',
                    ],
                );
            @endphp
            <fieldset>
                <legend class="sr-only">Barrierefreiheits-Optionen</legend>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-x-4 gap-y-2">
                    <label class="flex items-center space-x-2 cursor-pointer py-1">
                        <input type="checkbox" name="accessibility_structured[wheelchair_accessible]" value="1"
                            class="form-checkbox-field" {{ $accessData['wheelchair_accessible'] ? 'checked' : '' }}>
                        <span class="text-sm">Rollstuhlgerecht</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer py-1">
                        <input type="checkbox" name="accessibility_structured[accessible_restrooms]" value="1"
                            class="form-checkbox-field" {{ $accessData['accessible_restrooms'] ? 'checked' : '' }}>
                        <span class="text-sm">Barrierefreie Toiletten</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer py-1">
                        <input type="checkbox" name="accessibility_structured[low_counter]" value="1"
                            class="form-checkbox-field" {{ $accessData['low_counter'] ? 'checked' : '' }}>
                        <span class="text-sm">Niedriger Tresen</span>
                    </label>
                </div>
            </fieldset>
            <div>
                <label for="accessibility_details" class="form-label text-sm">Weitere Details zur
                    Barrierefreiheit</label>
                <textarea id="accessibility_details" name="accessibility_structured[details]" rows="3"
                    class="form-textarea-field mt-1" placeholder="Zusätzliche Informationen...">{{ $accessData['details'] }}</textarea>
            </div>
            @error('accessibility_features')
                <p class="form-error">{{ $message }}</p>
            @enderror
            @error('accessibility_features_json')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>
    </section>

</div>
