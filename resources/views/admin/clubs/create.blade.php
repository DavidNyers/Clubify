<x-admin-layout>
    <x-slot name="header" title="Neuen Club erstellen">
        <div class="flex justify-between items-center"> {{-- Flex für Titel und ggf. Buttons --}}
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Neuen Club erstellen') }}
            </h2>
            {{-- Ggf. Aktionen hier im Header? --}}
        </div>
    </x-slot>

    {{-- Formular beginnt direkt, kein extra Padding/Margin in der View --}}
    <form action="{{ route('admin.clubs.store') }}" method="POST">
        {{-- Include form partial --}}
        @include('admin.clubs._form', [
            'club' => null,
            'genres' => $genres,
            'clubOwners' => $clubOwners,
            'countries' => $countries,
        ])

        {{-- Action Buttons am Ende des Formulars, direkt nach dem Partial --}}
        <div class="mt-6 pt-5 border-t border-gray-200 dark:border-white/10 flex justify-end space-x-3">
            <a href="{{ route('admin.clubs.index') }}" class="btn-secondary"> {{ __('Abbrechen') }} </a>
            <button type="submit" class="btn-primary"> {{ __('Speichern') }} </button>
        </div>
    </form>

</x-admin-layout>
