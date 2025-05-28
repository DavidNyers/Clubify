<x-admin-layout>
    <x-slot name="header" title="Benutzer bearbeiten: {{ $user->name }}">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Benutzer bearbeiten:') }} <span
                    class="text-indigo-600 dark:text-indigo-400">{{ $user->name }}</span>
            </h2>
            {{-- Ggf. Aktionen hier --}}
        </div>
    </x-slot>

    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @method('PUT')
        @csrf
        <div class="space-y-6"> {{-- Hauptcontainer --}}

            {{-- Sektion: Benutzerdaten --}}
            <section>
                <h3 class="text-base font-semibold leading-7 text-gray-900 dark:text-gray-200">Benutzerdaten</h3>
                <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-6">
                    {{-- Name --}}
                    <div class="sm:col-span-3">
                        <label for="name" class="form-label">Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" required class="form-input-field mt-1"
                            value="{{ old('name', $user->name) }}">
                        @error('name')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="sm:col-span-3">
                        <label for="email" class="form-label">E-Mail <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" required class="form-input-field mt-1"
                            value="{{ old('email', $user->email) }}">
                        @error('email')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Passwort (Optional) --}}
                    <div class="sm:col-span-3">
                        <label for="password" class="form-label">Neues Passwort (Optional)</label>
                        <input type="password" name="password" id="password" class="form-input-field mt-1"
                            autocomplete="new-password">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leer lassen, um das aktuelle Passwort
                            beizubehalten.</p>
                        @error('password')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-3">
                        <label for="password_confirmation" class="form-label">Neues Passwort bestätigen</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="form-input-field mt-1" autocomplete="new-password">
                        @error('password_confirmation')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email Verifiziert Info --}}
                    <div class="sm:col-span-6">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            E-Mail verifiziert:
                            @if ($user->hasVerifiedEmail())
                                <span
                                    class="text-green-600 dark:text-green-400 font-medium">{{ $user->email_verified_at->format('d.m.Y H:i') }}</span>
                            @else
                                <span class="text-red-600 dark:text-red-400 font-medium">Nein</span>
                                {{-- Optional: Button zum erneuten Senden der Verifizierungsmail? --}}
                                {{-- <button type="button" class="ml-2 text-xs btn-secondary">Verifizierung senden</button> --}}
                            @endif
                        </p>
                    </div>
                </div>
            </section>

            <div class="border-t border-gray-200 dark:border-white/10 pt-6"></div>

            {{-- Sektion: Rollen --}}
            <section>
                <h3 class="text-base font-semibold leading-7 text-gray-900 dark:text-gray-200">Rollen</h3>
                <p class="mt-1 text-sm leading-6 text-gray-600 dark:text-gray-400">Wähle die Rollen für diesen Benutzer
                    aus.</p>
                <div class="mt-4 space-y-3 form-content-box"> {{-- Box Styling für Rollenbereich --}}
                    @php $userRoles = old('roles', $user->roles->pluck('name')->toArray()); @endphp
                    {{-- Mehrspaltiges Grid für bessere Übersicht bei vielen Rollen --}}
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-x-4 gap-y-2">
                        @foreach ($roles as $role)
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                    class="form-checkbox-field" @if (in_array($role->name, $userRoles)) checked @endif
                                    {{-- Deaktiviere Checkbox, wenn es der letzte Admin ist und man selbst (Sicherheitscheck aus Controller hier optional repliziert) --}}
                                    @if (
                                        $role->name == 'Administrator' &&
                                            $user->hasRole('Administrator') &&
                                            $user->id === auth()->id() &&
                                            \App\Models\User::role('Administrator')->count() <= 1) disabled
                                          title="Der letzte Administrator kann seine Rolle nicht verlieren." @endif>
                                <span class="text-sm">{{ $role->name }}</span> {{-- Textfarbe wird von form-content-box gesteuert --}}
                            </label>
                        @endforeach
                    </div>
                </div>
                @error('roles')
                    <p class="form-error">{{ $message }}</p>
                @enderror
                @error('roles.*')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </section>

            {{-- Action Buttons --}}
            <div class="mt-6 pt-5 border-t border-gray-200 dark:border-white/10 flex justify-end space-x-3">
                <a href="{{ route('admin.users.index') }}" class="btn-secondary"> {{ __('Abbrechen') }} </a>
                <button type="submit" class="btn-primary"> {{ __('Änderungen speichern') }} </button>
            </div>

        </div>
    </form>

    {{-- Die globalen Styles aus app.css oder dem Layout werden verwendet --}}

</x-admin-layout>
