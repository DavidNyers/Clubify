<x-admin-layout>
    <x-slot name="header" title="Neues DJ-Profil erstellen">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Neues DJ-Profil erstellen') }}
        </h2>
    </x-slot>

    <form action="{{ route('admin.djs.store') }}" method="POST" enctype="multipart/form-data"> {{-- enctype für Bilder --}}
        @include('admin.djs._form', [
            'dj' => null, // Kein DjProfile Objekt hier
            'availableDjs' => $availableDjs,
            'socialLinksJson' => '', // Leerer String für Textarea
            'musicLinksJson' => '', // Leerer String für Textarea
        ])

        <div class="mt-6 pt-5 border-t border-gray-200 dark:border-white/10 flex justify-end space-x-3">
            <a href="{{ route('admin.djs.index') }}" class="btn-secondary"> {{ __('Abbrechen') }} </a>
            <button type="submit" class="btn-primary"> {{ __('Profil erstellen') }} </button>
        </div>
    </form>

</x-admin-layout>
