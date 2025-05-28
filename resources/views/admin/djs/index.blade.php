<x-admin-layout>
    <x-slot name="header" title="DJs verwalten">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('DJ-Profile verwalten') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if (session('success'))
                        <div
                            class="mb-4 p-3 bg-green-100 dark:bg-green-900 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-200 rounded text-sm">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div
                            class="mb-4 p-3 bg-red-100 dark:bg-red-900 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-200 rounded text-sm">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Filter/Such Formular und Create Button --}}
                    <div class="mb-4 flex flex-col md:flex-row justify-between items-start gap-4">
                        <form action="{{ route('admin.djs.index') }}" method="GET"
                            class="flex flex-wrap items-end gap-3 grow">
                            {{-- Suche --}}
                            <div class="flex-1 min-w-[200px]">
                                <label for="search" class="form-label text-xs">DJ / Benutzername</label>
                                <input type="text" name="search" id="search" placeholder="Name suchen..."
                                    value="{{ request('search') }}" class="form-input-field text-sm w-full mt-1">
                            </div>
                            {{-- Filter Verifiziert --}}
                            <div class="min-w-[120px]">
                                <label for="filter_verified" class="form-label text-xs">Verifiziert</label>
                                <select name="filter_verified" id="filter_verified"
                                    class="form-select-field text-sm w-full mt-1">
                                    <option value="">Alle</option>
                                    <option value="yes" {{ request('filter_verified') == 'yes' ? 'selected' : '' }}>
                                        Ja</option>
                                    <option value="no" {{ request('filter_verified') == 'no' ? 'selected' : '' }}>
                                        Nein</option>
                                </select>
                            </div>
                            {{-- Filter Sichtbar --}}
                            <div class="min-w-[120px]">
                                <label for="filter_visible" class="form-label text-xs">Sichtbar</label>
                                <select name="filter_visible" id="filter_visible"
                                    class="form-select-field text-sm w-full mt-1">
                                    <option value="">Alle</option>
                                    <option value="yes" {{ request('filter_visible') == 'yes' ? 'selected' : '' }}>Ja
                                    </option>
                                    <option value="no" {{ request('filter_visible') == 'no' ? 'selected' : '' }}>
                                        Nein</option>
                                </select>
                            </div>
                            {{-- Buttons --}}
                            <div class="flex-shrink-0 flex space-x-2">
                                <button type="submit" class="btn-primary py-1.5 px-3 text-sm">
                                    <svg class="w-4 h-4 inline-block mr-1" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                    </svg>
                                    Filtern
                                </button>
                                @if (request('search') || request('filter_verified') || request('filter_visible'))
                                    <a href="{{ route('admin.djs.index') }}" class="btn-secondary py-1.5 px-3 text-sm"
                                        title="Filter zurücksetzen">Reset</a>
                                @endif
                            </div>
                        </form>
                        <div class="flex-shrink-0 w-full md:w-auto pt-4 md:pt-0">
                            <a href="{{ route('admin.djs.create') }}"
                                class="btn-primary block md:inline-block w-full text-center">
                                Neues DJ-Profil erstellen
                            </a>
                        </div>
                    </div>


                    {{-- DJ Tabelle --}}
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-6">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Stage Name</th>
                                    <th scope="col" class="px-6 py-3">Benutzer</th>
                                    <th scope="col" class="px-6 py-3 text-center">Sichtbar</th>
                                    <th scope="col" class="px-6 py-3 text-center">Verifiziert</th>
                                    <th scope="col" class="px-6 py-3 text-right">Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($djProfiles as $djProfile)
                                    <tr
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td
                                            class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                            {{ $djProfile->stage_name ?? $djProfile->user->name }}
                                            {{-- Zeige Stage Name oder User Name --}}
                                            @if ($djProfile->stage_name && $djProfile->stage_name !== $djProfile->user->name)
                                                <span
                                                    class="text-xs text-gray-500">({{ $djProfile->user->name }})</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if ($djProfile->user)
                                                <a href="{{ route('admin.users.show', $djProfile->user) }}"
                                                    class="hover:underline" title="Benutzerdetails">
                                                    {{ $djProfile->user->email }}
                                                </a>
                                            @else
                                                <span class="text-red-500 italic">Fehlender Benutzer!</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if ($djProfile->is_visible)
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Ja</span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Nein</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if ($djProfile->is_verified)
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">Ja</span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">Nein</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right flex justify-end space-x-2 whitespace-nowrap">
                                            <a href="{{ route('admin.djs.edit', $djProfile) }}"
                                                class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Profil
                                                bearb.</a>
                                            <form action="{{ route('admin.djs.destroy', $djProfile) }}" method="POST"
                                                onsubmit="return confirm('Sind Sie sicher, dass Sie dieses DJ-Profil löschen möchten? Der Benutzeraccount bleibt bestehen.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="font-medium text-red-600 dark:text-red-500 hover:underline">Profil
                                                    lösch.</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td colspan="5"
                                            class="px-6 py-4 text-center text-gray-500 dark:text-gray-400 italic">
                                            Keine DJ-Profile gefunden, die den Filterkriterien entsprechen.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination Links --}}
                    <div class="mt-4">
                        {{ $djProfiles->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
    {{-- Stelle sicher, dass die globalen Formular- und Button-Styles geladen werden (für Filter) --}}
</x-admin-layout>
