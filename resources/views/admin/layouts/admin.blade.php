<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" :class="{ 'dark': dark }" x-data="{
    dark: localStorage.getItem('dark') === 'true',
    isSidebarOpen: window.innerWidth >= 768 // Default open auf md und größer
}"
    x-init="$watch('dark', val => localStorage.setItem('dark', val));
    window.addEventListener('resize', () => { isSidebarOpen = window.innerWidth >= 768 });" class="h-full overflow-hidden">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles specific to Admin? (optional) -->
    {{-- <link rel="stylesheet" href="{{ asset('css/admin.css') }}"> --}}

    <style>
        /* Kleine Anpassung für sanftere Übergänge */
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900 h-full">
    <div x-data="{}" class="flex h-full"> {{-- Alpine Scope für $refs --}}

        <!-- Overlay für Mobile Sidebar -->
        <div x-show="isSidebarOpen && window.innerWidth < 768" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/50 z-30 md:hidden"
            @click="isSidebarOpen = false" x-cloak>
        </div>

        <!-- Sidebar -->
        <aside x-show="isSidebarOpen" x-transition:enter="transition ease-in-out duration-300"
            x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in-out duration-300" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            @click.outside="if(window.innerWidth < 768) isSidebarOpen = false" {{-- Schließt bei Klick außerhalb auf Mobile --}}
            class="fixed inset-y-0 left-0 z-40 w-64 bg-gray-800 dark:bg-gray-900 text-gray-100 p-4 flex-shrink-0 overflow-y-auto md:relative md:translate-x-0 md:z-auto md:inset-0 md:flex md:flex-col"
            x-cloak>

            <!-- Sidebar Header -->
            <div class="mb-6 text-center flex-shrink-0">
                <a href="{{ route('admin.dashboard') }}" class="text-xl font-semibold">
                    Clubify Admin
                </a>
            </div>

            <!-- Navigation -->
            <nav class="flex-grow">
                <ul>
                    <li class="mb-2 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700' : '' }}">
                        <a href="{{ route('admin.dashboard') }}"
                            class="block py-2 px-4 rounded hover:bg-gray-700">Dashboard</a>
                    </li>
                    <div class="pt-2">
                        <h3 class="px-4 mb-1 text-xs uppercase text-gray-500 font-semibold tracking-wider">Inhalte</h3>
                        <a href="{{ route('admin.clubs.index') }}"
                            class="flex items-center px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.clubs.*') ? 'bg-gray-700' : '' }}">Clubs</a>
                        <a href="{{ route('admin.events.index') }}"
                            class="flex items-center px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.events.*') ? 'bg-gray-700' : '' }}">Events</a>
                        <a href="{{ route('admin.djs.index') }}"
                            class="flex items-center px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.djs.*') ? 'bg-gray-700' : '' }}">DJs</a>
                        <a href="#"
                            class="flex items-center px-4 py-2 rounded hover:bg-gray-700 text-gray-500">Veranstalter
                            (TBD)</a>
                        <a href="{{ route('admin.genres.index') }}"
                            class="flex items-center px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.genres.*') ? 'bg-gray-700' : '' }}">Genres</a>

                        <a href="#"
                            class="flex items-center px-4 py-2 rounded hover:bg-gray-700 text-gray-500">Partybusse
                            (TBD)</a>
                    </div>
                    <div class="pt-2">
                        <h3 class="px-4 mb-1 text-xs uppercase text-gray-500 font-semibold tracking-wider">Benutzer &
                            Partner</h3>
                        <a href="{{ route('admin.users.index') }}"
                            class="flex items-center px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.users.*') ? 'bg-gray-700' : '' }}">Benutzerverwaltung</a>
                        <a href="{{ route('admin.partner-applications.index') }}"
                            class="flex items-center px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.partner-applications.*') ? 'bg-gray-700' : '' }}">Partner-Anträge</a>
                    </div>
                    <li class="mt-4 mb-1 px-4 text-xs uppercase text-gray-500 font-semibold">System & Konfiguration</li>
                    <li class="mb-2 {{ request()->routeIs('admin.subscription-plans.*') ? 'bg-gray-700' : '' }}">
                        <a href="{{ route('admin.subscription-plans.index') }}"
                            class="block py-2 px-4 rounded hover:bg-gray-700 {{ request()->routeIs('admin.subscription-plans.*') ? '' : 'text-gray-400' }}">
                            Abo-Pläne
                        </a>
                    </li>
                    <li class="mb-2 {{ request()->routeIs('admin.gamification.*') ? 'bg-gray-700' : '' }}">
                        <a href="#" class="block py-2 px-4 rounded hover:bg-gray-700 text-gray-400">Gamification
                            (TBD)</a>
                    </li>
                    <li class="mb-2 {{ request()->routeIs('admin.settings.*') ? 'bg-gray-700' : '' }}">
                        <a href="#" class="block py-2 px-4 rounded hover:bg-gray-700 text-gray-400">Einstellungen
                            (TBD)</a>
                    </li>

                    <li class="mt-4 mb-1 px-4 text-xs uppercase text-gray-500 font-semibold">Moderation</li>
                    <li class="mb-2 {{ request()->routeIs('admin.reviews.moderation*') ? 'bg-gray-700' : '' }}">
                        <a href="{{ route('admin.ratings.moderation.index') }}"
                            class="flex items-center px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.ratings.moderation.*') ? 'bg-gray-700' : '' }}">
                            Bewertungen
                        </a>
                    </li>
                    <li class="mb-2 {{ request()->routeIs('admin.reports.*') ? 'bg-gray-700' : '' }}">
                        <a href="#" class="block py-2 px-4 rounded hover:bg-gray-700 text-gray-400">Meldungen
                            (TBD)</a>
                    </li>
                </ul>
            </nav>

            <!-- Sidebar Footer -->
            <div class="mt-auto flex-shrink-0 border-t border-gray-700 pt-4">
                <ul>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                                class="block py-2 px-4 rounded hover:bg-red-700">
                                Logout
                            </a>
                        </form>
                    </li>
                    <li class="mt-2">
                        <a href="{{ route('dashboard') }}" class="block py-2 px-4 rounded hover:bg-gray-600 text-sm"
                            target="_blank">Zur User-Ansicht</a>
                    </li>
                    <!-- Theme Switcher -->
                    {{-- <li class="mt-6 text-center">
                        <button @click="dark = !dark" aria-label="Toggle Dark Mode"
                            class="p-2 rounded-md text-gray-400 hover:text-gray-100 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white">
                            <template x-if="!dark">
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                </svg>
                            </template>
                            <template x-if="dark">
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 3v1m0 16v1m8.485-8.485l-.707.707M5.222 5.222l-.707.707m16.97 0l-.707-.707M5.222 18.778l-.707-.707M21 12h-1M4 12H3m16.97-5.222l-.707-.707M7.722 18.778l-.707.707" />
                                </svg>
                            </template>
                        </button>
                    </li> --}}
                </ul>
            </div>

        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">

            <!-- Top Bar (für Mobile Toggle etc.) -->
            <header
                class="bg-white dark:bg-gray-800 shadow-md md:hidden flex justify-between items-center p-4 flex-shrink-0">
                <!-- Hamburger Toggle -->
                <button @click="isSidebarOpen = !isSidebarOpen"
                    class="text-gray-500 dark:text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none focus:ring">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                </button>
                <!-- Ggf. Platz für User-Menü auf Mobile hier -->
                <div></div> {{-- Platzhalter rechts --}}
            </header>

            <!-- Scrollbarer Inhaltsbereich -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Page Heading (optional, kann auch direkt in die View) -->
                @if (isset($header))
                    <header class="bg-white dark:bg-gray-800 shadow mb-6 hidden md:block"> {{-- Versteckt auf Mobile, da Topbar existiert --}}
                        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                <!-- Page Content -->
                {{ $slot }}
            </main>
        </div>
    </div>

</body>

</html>
