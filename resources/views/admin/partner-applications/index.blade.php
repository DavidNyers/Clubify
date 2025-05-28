<x-admin-layout>
    <x-slot name="header" title="Offene Partner-Anträge">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Offene Partner-Anträge') }}
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
                    @if ($errors->any())
                        <div
                            class="mb-4 p-3 bg-red-100 dark:bg-red-900 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-200 rounded text-sm">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif


                    {{-- Tabelle der offenen Anträge --}}
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Antragsteller</th>
                                    <th scope="col" class="px-6 py-3">E-Mail</th>
                                    <th scope="col" class="px-6 py-3">Antrag gestellt am</th>
                                    <th scope="col" class="px-6 py-3 text-right">Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pendingApplications as $user)
                                    <tr
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td
                                            class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                            {{ $user->name }}
                                        </td>
                                        <td class="px-6 py-4">{{ $user->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $user->created_at->format('d.m.Y H:i') }}</td> {{-- Wann hat sich User registriert? Ggf. anderes Feld für Antragdatum? --}}
                                        <td class="px-6 py-4 text-right">
                                            {{-- Link zur Detailseite --}}
                                            <a href="{{ route('admin.partner-applications.show', $user) }}"
                                                class="btn-secondary text-xs px-3 py-1">
                                                Details / Bearbeiten
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td colspan="4"
                                            class="px-6 py-4 text-center text-gray-500 dark:text-gray-400 italic">
                                            Aktuell keine offenen Partner-Anträge.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination Links --}}
                    <div class="mt-4">
                        {{ $pendingApplications->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Stelle sicher, dass die globalen Formular- und Button-Styles geladen werden --}}
</x-admin-layout>
