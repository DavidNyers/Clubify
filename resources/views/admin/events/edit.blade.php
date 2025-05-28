<x-admin-layout>
    <x-slot name="header" title="Event bearbeiten: {{ $event->name }}">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Event bearbeiten:') }} <span class="text-indigo-600 dark:text-indigo-400">{{ $event->name }}</span>
        </h2>
    </x-slot>

    <form action="{{ route('admin.events.update', $event) }}" method="POST" enctype="multipart/form-data">
        @method('PUT')
        {{-- Include form partial, passing event and related data --}}
        @include('admin.events._form', [
            'event' => $event, // Das zu bearbeitende Event-Objekt
            'clubs' => $clubs,
            'genres' => $genres,
            'organizers' => $organizers,
            'djs' => $djs,
        ])

        {{-- Action Buttons --}}
        <div class="pt-6 mt-6 border-t border-gray-200 dark:border-white/10">
            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.events.index') }}" class="btn-secondary"> {{ __('Abbrechen') }} </a>
                <button type="submit" class="btn-primary"> {{ __('Änderungen speichern') }} </button>
            </div>
        </div>
    </form>

    {{-- Lade Button Styles (oder global definieren) --}}
    @push('styles')
        <style>
            .btn-primary {
                @apply inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-900;
            }

            .btn-secondary {
                @apply inline-flex justify-center py-2 px-4 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-900;
            }
        </style>
    @endpush

</x-admin-layout>
