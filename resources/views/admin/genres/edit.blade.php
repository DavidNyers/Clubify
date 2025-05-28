<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Genre bearbeiten:') }} {{ $genre->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form action="{{ route('admin.genres.update', $genre) }}" method="POST">
                        @csrf
                        @method('PUT') {{-- Wichtig für Update --}}

                        <!-- Name -->
                        <div>
                            <label for="name"
                                class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Name') }}</label>
                            <input id="name"
                                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                type="text" name="name" value="{{ old('name', $genre->name) }}" required autofocus
                                autocomplete="name" />
                            @error('name')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Slug (nur zur Info anzeigen, da wir ihn nicht ändern lassen) -->
                        <div class="mt-4">
                            <label for="slug"
                                class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Slug (automatisch generiert)') }}</label>
                            <input id="slug"
                                class="block mt-1 w-full bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-gray-500 dark:text-gray-400"
                                type="text" value="{{ $genre->slug }}" disabled readonly />
                        </div>


                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.genres.index') }}"
                                class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 mr-4">
                                {{ __('Abbrechen') }}
                            </a>

                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 dark:hover:bg-blue-600 active:bg-blue-700 dark:active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                {{ __('Aktualisieren') }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
