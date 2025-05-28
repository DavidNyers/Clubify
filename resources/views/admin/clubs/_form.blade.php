@csrf
<div class="space-y-6"> {{-- Hauptcontainer (wie Event) --}}

    {{-- Sektion: Basisinformationen --}}
    <section>
        <h3 class="text-base font-semibold leading-7 text-gray-900 dark:text-gray-200">Basisinformationen</h3>
        <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-6">
            {{-- Name --}}
            <div class="sm:col-span-6">
                <label for="name" class="form-label">Clubname <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" required class="form-input-field mt-1"
                    value="{{ old('name', $club->name ?? '') }}">
                @error('name')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
            {{-- Beschreibung --}}
            <div class="sm:col-span-6">
                <label for="description" class="form-label">Beschreibung</label>
                <textarea id="description" name="description" rows="4" class="form-textarea-field mt-1">{{ old('description', $club->description ?? '') }}</textarea>
                @error('description')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </section>

    <div class="border-t border-gray-200 dark:border-white/10 pt-6"></div>

    {{-- Sektion: Standort --}}
    <section>
        <h3 class="text-base font-semibold leading-7 text-gray-900 dark:text-gray-200">Standort</h3>
        <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-6">
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
                <select id="country" name="country" required class="form-select-field mt-1">
                    @foreach ($countries as $code => $name)
                        <option value="{{ $code }}"
                            {{ strtolower(old('country', $club->country ?? config('app.country_code', 'DE'))) == strtolower($code) ? 'selected' : '' }}>
                            {{ $name }}</option>
                    @endforeach
                </select>
                @error('country')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
            <div class="sm:col-span-3">
                <label for="latitude" class="form-label">Latitude <span
                        class="text-xs text-gray-500">(Opt.)</span></label>
                <input id="latitude" name="latitude" type="number" step="any" class="form-input-field mt-1"
                    value="{{ old('latitude', $club->latitude ?? '') }}" />
                @error('latitude')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
            <div class="sm:col-span-3">
                <label for="longitude" class="form-label">Longitude <span
                        class="text-xs text-gray-500">(Opt.)</span></label>
                <input id="longitude" name="longitude" type="number" step="any" class="form-input-field mt-1"
                    value="{{ old('longitude', $club->longitude ?? '') }}" />
                @error('longitude')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </section>

    <div class="border-t border-gray-200 dark:border-white/10 pt-6"></div>

    {{-- Sektion: Kontakt & Details --}}
    <section>
        <h3 class="text-base font-semibold leading-7 text-gray-900 dark:text-gray-200">Kontakt & Details</h3>
        <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-6">
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
            <div class="sm:col-span-4">
                <label for="email" class="form-label">Kontakt E-Mail</label>
                <input type="email" name="email" id="email" class="form-input-field mt-1"
                    value="{{ old('email', $club->email ?? '') }}">
                @error('email')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
            <div class="sm:col-span-2">
                <label for="price_level" class="form-label">Preisniveau</label>
                <select id="price_level" name="price_level" class="form-select-field mt-1">
                    <option value="" {{ old('price_level', $club->price_level ?? '') == '' ? 'selected' : '' }}>
                        Keine Angabe</option>
                    <option value="$"
                        {{ old('price_level', $club->price_level ?? '') == '$' ? 'selected' : '' }}>$</option>
                    <option value="$$"
                        {{ old('price_level', $club->price_level ?? '') == '$$' ? 'selected' : '' }}>$$</option>
                    <option value="$$$"
                        {{ old('price_level', $club->price_level ?? '') == '$$$' ? 'selected' : '' }}>$$$</option>
                </select>
                @error('price_level')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </section>

    <div class="border-t border-gray-200 dark:border-white/10 pt-6"></div>

    {{-- Sektion: Musik & Öffnungszeiten (Angepasst an Event-Layout) --}}
    <section>
        <h3 class="text-base font-semibold leading-7 text-gray-900 dark:text-gray-200">Musik & Öffnungszeiten</h3>
        <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2"> {{-- 2 Spalten --}}
            {{-- Genres --}}
            <div x-data="{ genreSearch: '' }">
                <label for="clubGenreSearchInput" class="form-label">Musikgenres</label>
                <input type="search" id="clubGenreSearchInput" x-model="genreSearch"
                    placeholder="Genres filtern..." class="form-input-field text-sm mb-2 mt-1">
                <div class="mt-1 max-h-60 overflow-y-auto form-content-box"> {{-- Box-Styling --}}
                    <div class="divide-y divide-gray-200 dark:divide-gray-600">
                        @php $currentGenres = old('genres', isset($club) ? $club->genres->pluck('id')->toArray() : []); @endphp
                        @forelse ($genres as $genre)
                            <div x-show="genreSearch === '' || '{{ strtolower($genre->name) }}'.includes(genreSearch.toLowerCase())"
                                x-transition
                                class="flex items-center justify-between py-2 px-1 hover:bg-gray-200 dark:hover:bg-gray-200 rounded">
                                <label for="club-genre-{{ $genre->id }}"
                                    class="select-none text-sm font-medium cursor-pointer flex-grow mr-2">{{ $genre->name }}</label>
                                <input id="club-genre-{{ $genre->id }}" name="genres[]" type="checkbox"
                                    value="{{ $genre->id }}" class="form-checkbox-field"
                                    @if (in_array($genre->id, $currentGenres)) checked @endif>
                            </div>
                        @empty
                            <p class="text-center text-sm italic py-4">Keine Genres verfügbar.</p>
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

            {{-- Öffnungszeiten --}}
            <div>
                <label class="form-label mb-1">Öffnungszeiten</label>
                <div class="space-y-2 form-content-box"> {{-- Box-Styling --}}
                    @php $days = ['Mon'=>'Mo', 'Tue'=>'Di', 'Wed'=>'Mi', 'Thu'=>'Do', 'Fri'=>'Fr', 'Sat'=>'Sa', 'Sun'=>'So']; @endphp
                    @foreach ($days as $key => $label)
                        @php $dayData = old('opening_hours_structured.'.$key, $openingHoursStructured[$key] ?? ['start' => '', 'end' => '', 'closed' => false]); @endphp
                        <div x-data="{ closed: {{ $dayData['closed'] ? 'true' : 'false' }} }" class="flex items-center space-x-2 md:space-x-4">
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
                                <label for="oh_{{ $key }}_start" class="sr-only">Start</label>
                                <input type="time" id="oh_{{ $key }}_start"
                                    name="opening_hours_structured[{{ $key }}][start]"
                                    class="form-input-field" :disabled="closed" value="{{ $dayData['start'] }}">
                                <span class="text-gray-500 dark:text-gray-600">-</span>
                                <label for="oh_{{ $key }}_end" class="sr-only">Ende</label>
                                <input type="time" id="oh_{{ $key }}_end"
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
                @enderror
            </div>
        </div>
    </section>

    <div class="border-t border-gray-200 dark:border-white/10 pt-6"></div>

    {{-- Sektion: Zugänglichkeit & Verwaltung --}}
    <section>
        <h3 class="text-base font-semibold leading-7 text-gray-900 dark:text-gray-200">Zugänglichkeit & Verwaltung</h3>
        <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">
            {{-- Barrierefreiheit --}}
            <div class="sm:col-span-6">
                <label class="form-label mb-1">Barrierefreiheit</label>
                <div class="space-y-4 form-content-box"> {{-- Box-Styling --}}
                    {{-- Checkbox Optionen --}}
                    <fieldset>
                        <legend class="sr-only">Barrierefreiheits-Optionen</legend>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-x-4 gap-y-2">
                            @php $accessData = old('accessibility_structured', $accessibilityStructured ?? ['wheelchair_accessible' => false, 'accessible_restrooms' => false, 'low_counter' => false, 'details' => '']); @endphp
                            <label class="flex items-center space-x-2 cursor-pointer py-1">
                                <input id="access-wheelchair" name="accessibility_structured[wheelchair_accessible]"
                                    type="checkbox" value="1" class="form-checkbox-field"
                                    {{ $accessData['wheelchair_accessible'] ? 'checked' : '' }}>
                                <span class="text-sm">Rollstuhlgerecht</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer py-1">
                                <input id="access-restrooms" name="accessibility_structured[accessible_restrooms]"
                                    type="checkbox" value="1" class="form-checkbox-field"
                                    {{ $accessData['accessible_restrooms'] ? 'checked' : '' }}>
                                <span class="text-sm">Barrierefreie Toiletten</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer py-1">
                                <input id="access-counter" name="accessibility_structured[low_counter]"
                                    type="checkbox" value="1" class="form-checkbox-field"
                                    {{ $accessData['low_counter'] ? 'checked' : '' }}>
                                <span class="text-sm">Niedriger Tresen</span>
                            </label>
                        </div>
                    </fieldset>
                    {{-- Details Textarea --}}
                    <div>
                        <label for="accessibility_details"
                            class="form-label sr-only">{{ __('Weitere Details zur Barrierefreiheit') }}</label>
                        <textarea id="accessibility_details" name="accessibility_structured[details]" rows="2"
                            class="form-textarea-field" placeholder="Weitere Details zur Barrierefreiheit...">{{ $accessData['details'] }}</textarea>
                    </div>
                </div>
                @error('accessibility_features')
                    <p class="form-error">{{ $message }}</p>
                @enderror
                @error('accessibility_features_json')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Besitzer --}}
            <div class="sm:col-span-4">
                <label for="owner_id" class="form-label">Besitzer (ClubOwner)</label>
                <select id="owner_id" name="owner_id" class="form-select-field mt-1">
                    <option value="">-- Kein Besitzer zugewiesen --</option>
                    @foreach ($clubOwners as $owner)
                        <option value="{{ $owner->id }}"
                            {{ old('owner_id', $club->owner_id ?? '') == $owner->id ? 'selected' : '' }}>
                            {{ $owner->name }} ({{ $owner->email }})</option>
                    @endforeach
                </select>
                @error('owner_id')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status Checkboxen --}}
            <div class="sm:col-span-6">
                <label class="form-label">Status</label>
                <div class="mt-2 flex flex-col space-y-2 sm:flex-row sm:items-center sm:space-y-0 sm:space-x-6">
                    <label class="flex items-center cursor-pointer">
                        <input id="is_active" name="is_active" type="checkbox" value="1"
                            class="form-checkbox-field"
                            {{ old('is_active', $club->is_active ?? true) ? 'checked' : '' }}>
                        <span class="ml-2 text-sm leading-6 text-gray-900 dark:text-gray-200">Aktiv (sichtbar)</span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input id="is_verified" name="is_verified" type="checkbox" value="1"
                            class="form-checkbox-field"
                            {{ old('is_verified', $club->is_verified ?? false) ? 'checked' : '' }}>
                        <span class="ml-2 text-sm leading-6 text-gray-900 dark:text-gray-200">Verifiziert
                            (Admin)</span>
                    </label>
                </div>
            </div>
        </div>
    </section>

</div>

{{-- KEIN @push('styles') HIER! Styles sind jetzt global in app.css --}}
