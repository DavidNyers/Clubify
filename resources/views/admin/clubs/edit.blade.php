<x-admin-layout>
    <x-slot name="header" title="Club bearbeiten: {{ $club->name }}">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Club bearbeiten:') }} <span class="text-indigo-600 dark:text-indigo-400">{{ $club->name }}</span>
        </h2>
    </x-slot>

    {{-- Form direkt im Slot --}}
    <form action="{{ route('admin.clubs.update', $club) }}" method="POST" enctype="multipart/form-data">
        @method('PUT')
        @include('admin.clubs._form', [
            'club' => $club,
            'genres' => $genres,
            'clubOwners' => $clubOwners,
            'countries' => $countries,
            'openingHoursStructured' => $openingHoursStructured,
            'accessibilityStructured' => $accessibilityStructured,
        ])

        {{-- Action Buttons mit Abstand oben --}}
        <div class="pt-8 mt-8 border-t border-gray-200 dark:border-white/10 flex justify-end space-x-3">
            <a href="{{ route('admin.clubs.index') }}" class="btn-secondary"> {{ __('Abbrechen') }} </a>
            <button type="submit" class="btn-primary"> {{ __('Aktualisieren') }} </button>
        </div>
    </form>

</x-admin-layout>
