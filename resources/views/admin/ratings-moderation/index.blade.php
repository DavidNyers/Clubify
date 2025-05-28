<x-admin-layout>
    <x-slot name="header" title="Bewertungsmoderation">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Bewertungsmoderation - Offene Anträge') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
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
                    @if (session('info'))
                        <div
                            class="mb-4 p-3 bg-blue-100 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 text-blue-700 dark:text-blue-200 rounded text-sm">
                            {{ session('info') }}
                        </div>
                    @endif

                    {{-- Filter und Sortierung Formular --}}
                    <form action="{{ route('admin.ratings.moderation.index') }}" method="GET" class="mb-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 items-end">
                            {{-- Clubname Filter --}}
                            <div>
                                <label for="club_name" class="form-label text-xs">Clubname</label>
                                <input type="text" name="club_name" id="club_name"
                                    value="{{ $filterClubName ?? '' }}" placeholder="Club suchen..."
                                    class="form-input-field text-sm w-full mt-1">
                            </div>
                            {{-- Username Filter --}}
                            <div>
                                <label for="user_name" class="form-label text-xs">Benutzername / E-Mail</label>
                                <input type="text" name="user_name" id="user_name"
                                    value="{{ $filterUserName ?? '' }}" placeholder="Benutzer suchen..."
                                    class="form-input-field text-sm w-full mt-1">
                            </div>
                            {{-- Sterne Filter --}}
                            <div>
                                <label for="stars" class="form-label text-xs">Sterne</label>
                                <select name="stars" id="stars" class="form-select-field text-sm w-full mt-1">
                                    <option value="">Alle Sterne</option>
                                    <option value="5" {{ ($filterStars ?? '') == '5' ? 'selected' : '' }}>5 Sterne
                                    </option>
                                    <option value="4" {{ ($filterStars ?? '') == '4' ? 'selected' : '' }}>4 Sterne
                                    </option>
                                    <option value="3" {{ ($filterStars ?? '') == '3' ? 'selected' : '' }}>3 Sterne
                                    </option>
                                    <option value="2" {{ ($filterStars ?? '') == '2' ? 'selected' : '' }}>2 Sterne
                                    </option>
                                    <option value="1" {{ ($filterStars ?? '') == '1' ? 'selected' : '' }}>1 Stern
                                    </option>
                                    <option value="4-5" {{ ($filterStars ?? '') == '4-5' ? 'selected' : '' }}>4-5
                                        Sterne</option>
                                    <option value="1-2" {{ ($filterStars ?? '') == '1-2' ? 'selected' : '' }}>1-2
                                        Sterne</option>
                                    <option value="1-3" {{ ($filterStars ?? '') == '1-3' ? 'selected' : '' }}>1-3
                                        Sterne</option>
                                </select>
                            </div>
                            {{-- Sortierung --}}
                            <div>
                                <label for="sort" class="form-label text-xs">Sortieren nach</label>
                                <select name="sort" id="sort" class="form-select-field text-sm w-full mt-1">
                                    <option value="date-asc"
                                        {{ ($sortValue ?? 'date-asc') == 'date-asc' ? 'selected' : '' }}>Datum (Älteste
                                        zuerst)</option>
                                    <option value="date-desc"
                                        {{ ($sortValue ?? 'date-asc') == 'date-desc' ? 'selected' : '' }}>Datum
                                        (Neueste zuerst)</option>
                                    <option value="club-asc"
                                        {{ ($sortValue ?? 'date-asc') == 'club-asc' ? 'selected' : '' }}>Club (A-Z)
                                    </option>
                                    <option value="club-desc"
                                        {{ ($sortValue ?? 'date-asc') == 'club-desc' ? 'selected' : '' }}>Club (Z-A)
                                    </option>
                                    <option value="user-asc"
                                        {{ ($sortValue ?? 'date-asc') == 'user-asc' ? 'selected' : '' }}>Benutzer (A-Z)
                                    </option>
                                    <option value="user-desc"
                                        {{ ($sortValue ?? 'date-asc') == 'user-desc' ? 'selected' : '' }}>Benutzer
                                        (Z-A)</option>
                                    <option value="rating-asc"
                                        {{ ($sortValue ?? 'date-asc') == 'rating-asc' ? 'selected' : '' }}>Sterne
                                        (Niedrigste)</option>
                                    <option value="rating-desc"
                                        {{ ($sortValue ?? 'date-asc') == 'rating-desc' ? 'selected' : '' }}>Sterne
                                        (Höchste)</option>
                                </select>
                            </div>
                            {{-- Buttons --}}
                            <div class="flex space-x-2">
                                <button type="submit" class="btn-primary py-1.5 px-4 text-sm w-full">Anwenden</button>
                                <a href="{{ route('admin.ratings.moderation.index') }}"
                                    class="btn-secondary py-1.5 px-4 text-sm text-center w-full"
                                    title="Filter zurücksetzen">Reset</a>
                            </div>
                        </div>
                    </form>

                    {{-- Tabelle der offenen Anträge --}}
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-6">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-4 py-3">Club</th>
                                    <th scope="col" class="px-4 py-3">Benutzer</th>
                                    <th scope="col" class="px-3 py-3 text-center">Sterne</th>
                                    <th scope="col" class="px-6 py-3">Kommentar (Auszug)</th>
                                    <th scope="col" class="px-4 py-3">Eingegangen am</th>
                                    <th scope="col" class="px-6 py-3 text-right">Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pendingRatings as $rating)
                                    <tr
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td
                                            class="px-4 py-2 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                            @if ($rating->club)
                                                <a href="{{ route('clubs.show', $rating->club) }}" target="_blank"
                                                    class="hover:underline" title="Club im Frontend ansehen">
                                                    {{ $rating->club->name }}
                                                </a>
                                            @else
                                                <span class="text-gray-400 italic">Club gelöscht</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2">
                                            @if ($rating->user)
                                                <a href="{{ route('admin.users.show', $rating->user) }}"
                                                    class="hover:underline" title="Benutzerdetails ansehen">
                                                    {{ $rating->user->name }}
                                                </a>
                                                <span
                                                    class="block text-xs text-gray-500 dark:text-gray-400">{{ $rating->user->email }}</span>
                                            @else
                                                <span class="text-gray-400 italic">Benutzer gelöscht</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            <div class="flex justify-center text-yellow-400">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 fill-current {{ $i <= $rating->rating ? '' : 'text-gray-300 dark:text-gray-600' }}"
                                                        viewBox="0 0 20 20">
                                                        <path
                                                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                                    </svg>
                                                @endfor
                                            </div>
                                        </td>
                                        <td class="px-6 py-2 text-xs max-w-sm">
                                            @if ($rating->comment)
                                                <div x-data="{ showFullComment_{{ $rating->id }}: false }">
                                                    <p x-show="!showFullComment_{{ $rating->id }}" class="truncate">
                                                        {{ Str::limit($rating->comment, 80) }}
                                                        @if (strlen($rating->comment ?? '') > 80)
                                                            <button @click="showFullComment_{{ $rating->id }} = true"
                                                                type="button"
                                                                class="text-indigo-600 dark:text-indigo-400 hover:underline text-xs ml-1 focus:outline-none">(mehr)</button>
                                                        @endif
                                                    </p>
                                                    {{-- Modal für den vollständigen Kommentar --}}
                                                    <div x-show="showFullComment_{{ $rating->id }}" x-cloak
                                                        x-transition:enter="ease-out duration-300"
                                                        x-transition:enter-start="opacity-0"
                                                        x-transition:enter-end="opacity-100"
                                                        x-transition:leave="ease-in duration-200"
                                                        x-transition:leave-start="opacity-100"
                                                        x-transition:leave-end="opacity-0"
                                                        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-40 flex items-center justify-center p-4"
                                                        @keydown.escape.window="showFullComment_{{ $rating->id }} = false">
                                                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl max-w-xl w-full space-y-4 transform transition-all sm:my-8"
                                                            @click.outside="showFullComment_{{ $rating->id }} = false">
                                                            <div
                                                                class="flex justify-between items-center border-b pb-2 border-gray-200 dark:border-gray-700">
                                                                <h4
                                                                    class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                                                    Vollständiger Kommentar</h4>
                                                                <button
                                                                    @click="showFullComment_{{ $rating->id }} = false"
                                                                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1 rounded-full -mr-2">
                                                                    <svg class="w-5 h-5" fill="currentColor"
                                                                        viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd"
                                                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                                            clip-rule="evenodd"></path>
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                            <p
                                                                class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap max-h-96 overflow-y-auto">
                                                                {{ $rating->comment }}</p>
                                                            <div class="text-right mt-4">
                                                                <button
                                                                    @click="showFullComment_{{ $rating->id }} = false"
                                                                    type="button"
                                                                    class="btn-secondary">Schließen</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-gray-400 italic">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap">
                                            {{ $rating->created_at->format('d.m.Y H:i') }}
                                        </td>
                                        <td
                                            class="px-6 py-2 text-right flex justify-end items-center space-x-2 whitespace-nowrap">
                                            <form action="{{ route('admin.ratings.moderation.approve', $rating) }}"
                                                method="POST" onsubmit="return confirm('Bewertung freigeben?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="btn-primary bg-green-600 hover:bg-green-700 focus:ring-green-500 text-xs px-2.5 py-1">Freigeben</button>
                                            </form>
                                            <form action="{{ route('admin.ratings.moderation.reject', $rating) }}"
                                                method="POST"
                                                onsubmit="return confirm('Bewertung ablehnen und löschen?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="btn-primary bg-red-600 hover:bg-red-700 focus:ring-red-500 text-xs px-2.5 py-1">Ablehnen</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td colspan="6"
                                            class="px-6 py-4 text-center text-gray-500 dark:text-gray-400 italic">
                                            Keine Bewertungen zur Moderation vorhanden.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $pendingRatings->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Stelle sicher, dass die globalen Formular- und Button-Styles aus app.css oder dem Layout geladen werden --}}
    {{-- Diese werden für die Filter-Inputs und die Modal-Buttons benötigt --}}
</x-admin-layout>
