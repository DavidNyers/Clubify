<x-admin-layout> {{-- Verwendet das neue Admin-Layout --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __('Willkommen im Admin-Bereich!') }}
                    <p class="mt-4">Dies ist der Startpunkt für die Verwaltung von Clubify.</p>
                    <p>Nächste Schritte: CRUD für Genres, Abo-Pläne, etc. implementieren.</p>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
