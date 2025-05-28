<x-admin-layout>
    <x-slot name="header" title="DJ-Profil bearbeiten: {{ $dj->displayName }}">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('DJ-Profil bearbeiten:') }} <span
                class="text-indigo-600 dark:text-indigo-400">{{ $dj->displayName }}</span>
        </h2>
    </x-slot>

    <form action="{{ route('admin.djs.update', $dj) }}" method="POST" enctype="multipart/form-data">
        @method('PUT')
        @include('admin.djs._form', [
            'dj' => $dj, // Das zu bearbeitende Profil
            // 'availableDjs' wird hier nicht benötigt
            'socialLinksJson' => $socialLinksJson, // Vorformatierter JSON String
            'musicLinksJson' => $musicLinksJson, // Vorformatierter JSON String
        ])

        <div class="mt-6 pt-5 border-t border-gray-200 dark:border-white/10 flex justify-end space-x-3">
            <a href="{{ route('admin.djs.index') }}" class="btn-secondary"> {{ __('Abbrechen') }} </a>
            <button type="submit" class="btn-primary"> {{ __('Änderungen speichern') }} </button>
        </div>
    </form>

</x-admin-layout>
