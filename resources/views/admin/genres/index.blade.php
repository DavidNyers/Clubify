<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Musikgenres verwalten') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Flash Message für Erfolg --}}
                    @if (session('success'))
                        <div
                            class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-200 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Actions Row (Create Button & Search Form) --}}
                    <div class="mb-4 flex flex-col sm:flex-row justify-between items-center gap-4">
                        {{-- Suchformular --}}
                        <form action="{{ route('admin.genres.index') }}" method="GET"
                            class="flex items-center w-full sm:w-auto">
                            <label for="search" class="sr-only">Suchen</label> {{-- Für Screenreader --}}
                            <input type="text" name="search" id="search" placeholder="Genre suchen..."
                                value="{{ request('search') }}" {{-- Wert beibehalten --}}
                                class="block w-full sm:w-auto border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm text-sm">
                            <button type="submit"
                                class="ml-2 inline-flex items-center justify-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                </svg>
                            </button>
                            {{-- Optional: Clear Button --}}
                            @if (request('search'))
                                <a href="{{ route('admin.genres.index') }}"
                                    class="ml-2 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200"
                                    title="Suche zurücksetzen">×</a>
                            @endif
                        </form>

                        {{-- Neues Genre erstellen Button --}}
                        <a href="{{ route('admin.genres.create') }}"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-blue-600 dark:bg-blue-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 dark:hover:bg-blue-600 active:bg-blue-700 dark:active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Neues Genre erstellen
                        </a>
                    </div>


                    {{-- Tabelle (Rest bleibt gleich) --}}
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            {{-- thead bleibt gleich --}}
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        Name
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Slug
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Erstellt am
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right">
                                        Aktionen
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($genres as $genre)
                                    {{-- tr bleibt gleich --}}
                                    <tr
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <th scope="row"
                                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $genre->name }}
                                        </th>
                                        <td class="px-6 py-4">
                                            {{ $genre->slug }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $genre->created_at->format('d.m.Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 text-right flex justify-end space-x-2">
                                            <a href="{{ route('admin.genres.edit', $genre) }}"
                                                class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Bearbeiten</a>
                                            <form action="{{ route('admin.genres.destroy', $genre) }}" method="POST"
                                                onsubmit="return confirm('Sind Sie sicher, dass Sie dieses Genre löschen möchten?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="font-medium text-red-600 dark:text-red-500 hover:underline">Löschen</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    {{-- tr für leere Ergebnisse bleibt gleich --}}
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td colspan="4"
                                            class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                            Keine Genres für "{{ request('search') ?? 'alle' }}" gefunden.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination Links --}}
                    <div class="mt-4">
                        {{-- WICHTIG: appends() hinzufügen, damit die Suche bei Paginierung erhalten bleibt --}}
                        {{ $genres->appends(request()->query())->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
