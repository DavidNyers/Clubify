<x-admin-layout>
    <x-slot name="header" title="Partnerantrag: {{ $user->name }}">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Partnerantrag: <span class="text-indigo-600 dark:text-indigo-400">{{ $user->name }}</span>
                </h2>
                <p class="text-sm text-gray-500">Status:
                    <span
                        class="font-medium
                      @if ($user->partner_status == 'pending') text-yellow-600 dark:text-yellow-400 @endif
                      @if ($user->partner_status == 'approved') text-green-600 dark:text-green-400 @endif
                      @if ($user->partner_status == 'rejected') text-red-600 dark:text-red-400 @endif
                  ">
                        {{ ucfirst($user->partner_status) }}
                    </span>
                    @if ($user->partner_status_processed_at)
                        <span class="text-xs text-gray-400"> (Bearbeitet am:
                            {{ $user->partner_status_processed_at->format('d.m.y H:i') }} von
                            {{ $user->partnerStatusProcessor?->name ?? 'Unbekannt' }})</span>
                    @endif
                </p>
                @if ($user->partner_status == 'rejected' && $user->partner_application_notes)
                    <p class="text-xs text-red-500 mt-1">Ablehnungsgrund: {{ $user->partner_application_notes }}</p>
                @endif
            </div>
            <div>
                <a href="{{ route('admin.partner-applications.index') }}" class="btn-secondary text-sm">Zurück zur
                    Übersicht</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">

        {{-- Abschnitt: Benutzerinformationen --}}
        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Antragsteller Informationen
                </h3>
            </div>
            <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                {{-- Hier die Details aus users.show einfügen oder anpassen --}}
                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">{{ $user->name }}
                    </dd>
                </div>
                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">E-Mail</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">{{ $user->email }}
                    </dd>
                </div>
                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Registriert</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">
                        {{ $user->created_at->format('d.m.Y H:i') }}</dd>
                </div>
                {{-- Weitere relevante User-Infos hier anzeigen --}}
                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Aktuelle Rollen</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">
                        @forelse ($user->roles as $role)
                        <span class="mr-1 text-xs">{{ $role->name }}</span> @empty -
                        @endforelse
                    </dd>
                </div>
                {{-- TODO: Hier könnten später spezifische Bewerbungsdaten stehen (z.B. Link zum Club/DJ-Profil Entwurf) --}}
            </dl>
        </div>

        {{-- Abschnitt: Aktionen (Nur wenn Status 'pending') --}}
        @if ($user->partner_status === 'pending')
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Antrag bearbeiten</h3>
                </div>
                <div class="px-4 py-5 sm:p-6 space-y-6">

                    {{-- Formular zum Annehmen --}}
                    <form action="{{ route('admin.partner-applications.approve', $user) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div
                            class="space-y-3 p-4 border border-green-300 dark:border-green-700 rounded-md bg-green-50 dark:bg-gray-800">
                            <h4 class="font-medium text-green-800 dark:text-green-300">Antrag annehmen</h4>
                            <div>
                                <label for="role_to_assign" class="form-label text-sm">Partner-Rolle zuweisen:<span
                                        class="text-red-500">*</span></label>
                                <select name="role_to_assign" id="role_to_assign" required
                                    class="form-select-field w-full sm:w-auto mt-1">
                                    <option value="">-- Rolle auswählen --</option>
                                    @foreach ($partnerRoles as $role)
                                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                @error('role_to_assign')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="text-right">
                                <button type="submit"
                                    class="btn-primary bg-green-600 hover:bg-green-700 focus:ring-green-500">Annehmen &
                                    Rolle zuweisen</button>
                            </div>
                        </div>
                    </form>

                    {{-- Formular zum Ablehnen --}}
                    <form action="{{ route('admin.partner-applications.reject', $user) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div
                            class="space-y-3 p-4 border border-red-300 dark:border-red-700 rounded-md bg-red-50 dark:bg-gray-800">
                            <h4 class="font-medium text-red-800 dark:text-red-300">Antrag ablehnen</h4>
                            <div>
                                <label for="rejection_reason" class="form-label text-sm">Grund (Optional):</label>
                                <textarea name="rejection_reason" id="rejection_reason" rows="2" class="form-textarea-field w-full mt-1"
                                    placeholder="Grund für interne Notizen oder E-Mail an Benutzer..."></textarea>
                                @error('rejection_reason')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="text-right">
                                <button type="submit"
                                    class="btn-primary bg-red-600 hover:bg-red-700 focus:ring-red-500">Antrag
                                    ablehnen</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        @endif

    </div>
</x-admin-layout>
