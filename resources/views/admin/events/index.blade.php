<x-admin-layout>
    <x-slot name="header" title="Events verwalten">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Events verwalten') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if (session('success'))
                        <div
                            class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-200 rounded text-sm">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div
                            class="mb-4 p-4 bg-red-100 dark:bg-red-900 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-200 rounded text-sm">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Filter/Such Formular und Create Button --}}
                    <div class="mb-4 flex flex-col md:flex-row justify-between items-center gap-4">
                        <form action="{{ route('admin.events.index') }}" method="GET"
                            class="flex flex-wrap items-center gap-2 grow">
                            <div class="flex-1 min-w-[150px]">
                                <label for="search_name" class="sr-only">Eventname</label>
                                <input type="text" name="search_name" id="search_name" placeholder="Eventname..."
                                    value="{{ request('search_name') }}" class="form-input-field text-sm w-full">
                            </div>
                            <div class="flex-1 min-w-[150px]">
                                <label for="search_club" class="sr-only">Clubname</label>
                                <input type="text" name="search_club" id="search_club" placeholder="Clubname..."
                                    value="{{ request('search_club') }}" class="form-input-field text-sm w-full">
                            </div>
                            <div class="flex-1 min-w-[150px]">
                                <label for="filter_status" class="sr-only">Status</label>
                                <select name="filter_status" id="filter_status"
                                    class="form-select-field text-sm w-full">
                                    <option value="">-- Alle Status --</option>
                                    <option value="active" {{ request('filter_status') == 'active' ? 'selected' : '' }}>
                                        Aktiv</option>
                                    <option value="inactive"
                                        {{ request('filter_status') == 'inactive' ? 'selected' : '' }}>Inaktiv</option>
                                    <option value="needs_approval"
                                        {{ request('filter_status') == 'needs_approval' ? 'selected' : '' }}>Wartet auf
                                        Freigabe</option>
                                    <option value="cancelled"
                                        {{ request('filter_status') == 'cancelled' ? 'selected' : '' }}>Abgesagt
                                    </option>
                                </select>
                            </div>
                            <div class="flex-shrink-0">
                                <button type="submit" class="btn-primary py-1.5 px-3 text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4 inline-block mr-1">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                    </svg>
                                    Suchen
                                </button>
                                @if (request('search_name') || request('search_club') || request('filter_status'))
                                    <a href="{{ route('admin.events.index') }}"
                                        class="ml-2 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200"
                                        title="Filter zurücksetzen">× Reset</a>
                                @endif
                            </div>
                        </form>
                        <div class="flex-shrink-0 w-full md:w-auto">
                            <a href="{{ route('admin.events.create') }}"
                                class="btn-primary block md:inline-block w-full text-center">
                                Neues Event erstellen
                            </a>
                        </div>
                    </div>


                    {{-- Event Tabelle --}}
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-6">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Event</th>
                                    <th scope="col" class="px-6 py-3">Club</th>
                                    <th scope="col" class="px-6 py-3">Startzeit</th>
                                    <th scope="col" class="px-6 py-3 text-center">Status</th>
                                    <th scope="col" class="px-6 py-3 text-right">Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($events as $event)
                                    <tr
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                            {{ $event->name }}
                                        </th>
                                        <td class="px-6 py-4">
                                            {{ $event->club->name ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $event->start_time->format('d.m.Y H:i') }} Uhr
                                        </td>
                                        <td class="px-6 py-4 text-center whitespace-nowrap">
                                            @if ($event->isCancelled())
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">Abgesagt</span>
                                            @elseif(!$event->is_active && $event->requires_approval)
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">Wartet
                                                    auf Freigabe</span>
                                            @elseif($event->is_active)
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Aktiv</span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300">Inaktiv</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right flex justify-end space-x-2 whitespace-nowrap">
                                            <a href="{{ route('admin.events.edit', $event) }}"
                                                class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Bearbeiten</a>
                                            <form action="{{ route('admin.events.destroy', $event) }}" method="POST"
                                                onsubmit="return confirm('Sind Sie sicher, dass Sie dieses Event löschen möchten?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="font-medium text-red-600 dark:text-red-500 hover:underline">Löschen</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td colspan="5"
                                            class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                            Keine Events gefunden, die den Filterkriterien entsprechen.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination Links --}}
                    <div class="mt-4">
                        {{ $events->appends(request()->query())->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
    {{-- Stelle sicher, dass die Formular-Styles aus dem Layout oder einem Push geladen werden --}}
    @push('styles')
        <style>
            /* Übernehme die Klassen aus dem Club-Formular, falls nicht global definiert */
            .form-label {
                @apply block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200 mb-1;
            }

            .form-input-field,
            .form-textarea-field,
            .form-select-field {
                @apply block w-full rounded-md border-0 py-1.5 px-3 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:focus:ring-indigo-500 sm:text-sm sm:leading-6 bg-white dark:bg-gray-50 text-gray-900 dark:text-gray-900;
            }

            .form-select-field {
                @apply pr-8;
            }

            /* Platz für Pfeil */
            .btn-primary {
                @apply inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-900;
            }
        </style>
    @endpush
</x-admin-layout>
