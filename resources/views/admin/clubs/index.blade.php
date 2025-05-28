<x-admin-layout>
    <x-slot name="header" title="Clubs verwalten">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Clubs verwalten') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if (session('success'))
                        <div
                            class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-200 rounded">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div
                            class="mb-4 p-4 bg-red-100 dark:bg-red-900 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-200 rounded text-sm">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Actions Row --}}
                    <div class="mb-4 flex flex-col sm:flex-row justify-between items-center gap-4">
                        {{-- Suchformular (Suche nach Name, Stadt) --}}
                        <form action="{{ route('admin.clubs.index') }}" method="GET"
                            class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                            <label for="search_name" class="sr-only">Name</label>
                            <input type="text" name="search_name" id="search_name" placeholder="Clubname..."
                                value="{{ request('search_name') }}"
                                class="flex-grow sm:flex-grow-0 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm text-sm">

                            <label for="search_city" class="sr-only">Stadt</label>
                            <input type="text" name="search_city" id="search_city" placeholder="Stadt..."
                                value="{{ request('search_city') }}"
                                class="flex-grow sm:flex-grow-0 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm text-sm">

                            <button type="submit"
                                class="inline-flex items-center justify-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                </svg>
                                <span class="ml-1">Suchen</span>
                            </button>
                            @if (request('search_name') || request('search_city'))
                                <a href="{{ route('admin.clubs.index') }}"
                                    class="ml-2 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200"
                                    title="Suche zurücksetzen">×</a>
                            @endif
                        </form>

                        {{-- Neuer Club Button --}}
                        <a href="{{ route('admin.clubs.create') }}"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-blue-600 dark:bg-blue-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 dark:hover:bg-blue-600 active:bg-blue-700 dark:active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Neuen Club erstellen
                        </a>
                    </div>

                    {{-- Tabelle --}}
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Name</th>
                                    <th scope="col" class="px-6 py-3">Stadt</th>
                                    <th scope="col" class="px-6 py-3 text-center">Aktiv</th>
                                    <th scope="col" class="px-6 py-3 text-center">Verifiziert</th>
                                    <th scope="col" class="px-6 py-3">Besitzer</th>
                                    <th scope="col" class="px-6 py-3 text-right">Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($clubs as $club)
                                    <tr
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <th scope="row"
                                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $club->name }}</th>
                                        <td class="px-6 py-4">{{ $club->city ?? '-' }}</td>
                                        <td class="px-6 py-4 text-center">
                                            @if ($club->is_active)
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Ja</span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">Nein</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if ($club->is_verified)
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">Ja</span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">Nein</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">{{ $club->owner->name ?? '-' }}</td>
                                        <td class="px-6 py-4 text-right flex justify-end space-x-2">
                                            <a href="{{ route('admin.clubs.edit', $club) }}"
                                                class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Bearbeiten</a>
                                            <form action="{{ route('admin.clubs.destroy', $club) }}" method="POST"
                                                onsubmit="return confirm('Sind Sie sicher, dass Sie diesen Club löschen möchten?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="font-medium text-red-600 dark:text-red-500 hover:underline">Löschen</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td colspan="6"
                                            class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                            Keine Clubs gefunden.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-4">
                        {{ $clubs->appends(request()->query())->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
