@csrf
<div class="space-y-6">

    {{-- Sektion: Verknüpfter Benutzer & Stage Name --}}
    <section>
        <h3 class="text-base font-semibold leading-7 text-gray-900 dark:text-gray-200">Benutzer & Stage Name</h3>
        <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-6">
            {{-- Benutzer Auswahl (nur beim Erstellen) --}}
            @if (!isset($dj)) {{-- Nur im Create-Modus anzeigen --}}
                <div class="sm:col-span-4">
                    <label for="user_id" class="form-label">Benutzer (mit DJ-Rolle) <span
                            class="text-red-500">*</span></label>
                    <select name="user_id" id="user_id" required class="form-select-field mt-1">
                        <option value="">-- Wähle einen DJ aus --</option>
                        @foreach ($availableDjs as $djUser)
                            <option value="{{ $djUser->id }}" {{ old('user_id') == $djUser->id ? 'selected' : '' }}>
                                {{ $djUser->name }} ({{ $djUser->email }})
                            </option>
                        @endforeach
                    </select>
                    @if ($availableDjs->isEmpty())
                        <p class="mt-1 text-sm text-yellow-600 dark:text-yellow-400">Keine verfügbaren Benutzer mit
                            DJ-Rolle gefunden, die noch kein Profil haben.</p>
                    @endif
                    @error('user_id')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            @else
                {{-- Im Edit-Modus anzeigen, aber nicht änderbar --}}
                <div class="sm:col-span-3">
                    <label class="form-label">Verknüpfter Benutzer</label>
                    <p
                        class="mt-1 text-sm text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 p-2 rounded-md">
                        {{ $dj->user->name }} ({{ $dj->user->email }}) <span class="text-xs text-gray-500"> - kann nicht
                            geändert werden</span>
                    </p>
                    {{-- Verstecktes Feld nicht nötig, da wir user_id im Controller aus $validated entfernen --}}
                </div>
            @endif

            {{-- Stage Name --}}
            <div class="sm:col-span-3">
                <label for="stage_name" class="form-label">Stage Name (Optional)</label>
                <input type="text" name="stage_name" id="stage_name" class="form-input-field mt-1"
                    value="{{ old('stage_name', $dj->stage_name ?? '') }}">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Wenn leer, wird der Benutzername verwendet.</p>
                @error('stage_name')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </section>

    <div class="border-t border-gray-200 dark:border-white/10 pt-6"></div>

    {{-- Sektion: Profil Details --}}
    <section>
        <h3 class="text-base font-semibold leading-7 text-gray-900 dark:text-gray-200">Profil Details</h3>
        <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-6">
            {{-- Bio --}}
            <div class="sm:col-span-6">
                <label for="bio" class="form-label">Biografie</label>
                <textarea id="bio" name="bio" rows="6" class="form-textarea-field mt-1">{{ old('bio', $dj->bio ?? '') }}</textarea>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Beschreibe den DJ. Markdown oder Rich-Text
                    (später).</p>
                @error('bio')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- TODO: Profilbild & Banner Upload Felder hier einfügen --}}
            <div class="sm:col-span-3">
                <label for="profile_image_path" class="form-label">Profilbild (TODO)</label>
                <input type="file" name="profile_image" id="profile_image_path"
                    class="form-input-field mt-1 file:mr-4 file:py-1 file:px-2 file:rounded-full file:border-0 file:text-xs file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300 disabled:opacity-50"
                    disabled>
                @error('profile_image')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
            <div class="sm:col-span-3">
                <label for="banner_image_path" class="form-label">Bannerbild (TODO)</label>
                <input type="file" name="banner_image" id="banner_image_path"
                    class="form-input-field mt-1 file:mr-4 file:py-1 file:px-2 file:rounded-full file:border-0 file:text-xs file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300 disabled:opacity-50"
                    disabled>
                @error('banner_image')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Social Links (JSON) --}}
            <div class="sm:col-span-3">
                <label for="social_links" class="form-label">Social Media Links (JSON)</label>
                <textarea id="social_links" name="social_links" rows="5" class="form-textarea-field mt-1 font-mono text-xs"
                    placeholder='{
"instagram": "https://...",
"facebook": "https://..."
}'>{{ old('social_links', $socialLinksJson ?? '') }}</textarea>
                @error('social_links')
                    <p class="form-error">{{ $message }}</p>
                @enderror
                @error('social_links_json')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Music Links (JSON) --}}
            <div class="sm:col-span-3">
                <label for="music_links" class="form-label">Musik Links (JSON)</label>
                <textarea id="music_links" name="music_links" rows="5" class="form-textarea-field mt-1 font-mono text-xs"
                    placeholder='{
"soundcloud": "https://...",
"mixcloud": "https://..."
}'>{{ old('music_links', $musicLinksJson ?? '') }}</textarea>
                @error('music_links')
                    <p class="form-error">{{ $message }}</p>
                @enderror
                @error('music_links_json')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </section>

    <div class="border-t border-gray-200 dark:border-white/10 pt-6"></div>

    {{-- Sektion: Booking & Status --}}
    <section>
        <h3 class="text-base font-semibold leading-7 text-gray-900 dark:text-gray-200">Booking & Status</h3>
        <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-6">
            {{-- Booking Email --}}
            <div class="sm:col-span-3">
                <label for="booking_email" class="form-label">Booking E-Mail (Optional)</label>
                <input type="email" name="booking_email" id="booking_email" class="form-input-field mt-1"
                    value="{{ old('booking_email', $dj->booking_email ?? '') }}">
                @error('booking_email')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- TODO: Technical Rider Upload Feld --}}
            <div class="sm:col-span-3">
                <label for="technical_rider_path" class="form-label">Technical Rider (PDF - TODO)</label>
                <input type="file" name="technical_rider" id="technical_rider_path"
                    class="form-input-field mt-1 file:mr-4 file:py-1 file:px-2 file:rounded-full file:border-0 file:text-xs file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300 disabled:opacity-50"
                    disabled>
                @error('technical_rider')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status Checkboxen --}}
            <div class="sm:col-span-6">
                <label class="form-label">Status</label>
                <div class="mt-2 flex flex-col space-y-2 sm:flex-row sm:items-center sm:space-y-0 sm:space-x-6">
                    <label class="flex items-center cursor-pointer">
                        <input id="is_visible" name="is_visible" type="checkbox" value="1"
                            class="form-checkbox-field"
                            {{ old('is_visible', $dj->is_visible ?? true) ? 'checked' : '' }}>
                        <span class="ml-2 text-sm leading-6 text-gray-900 dark:text-gray-200">Profil ist
                            sichtbar</span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input id="is_verified" name="is_verified" type="checkbox" value="1"
                            class="form-checkbox-field"
                            {{ old('is_verified', $dj->is_verified ?? false) ? 'checked' : '' }}>
                        <span class="ml-2 text-sm leading-6 text-gray-900 dark:text-gray-200">Profil ist verifiziert
                            (Admin)</span>
                    </label>
                </div>
            </div>
        </div>
    </section>

</div>

{{-- Die globalen Styles werden aus app.css geladen --}}
