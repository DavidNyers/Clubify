<header class="bg-white dark:bg-gray-800 shadow-md sticky top-0 z-40">
    <nav class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            {{-- Logo & Hauptmenü (Desktop) --}}
            <div class="flex items-center">
                <a href="{{ route('home') }}"
                    class="flex-shrink-0 text-xl font-bold text-indigo-600 dark:text-indigo-400">
                    Clubify
                </a>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="{{ route('events.index') }}"
                            class="nav-link {{ request()->routeIs('events.index') || request()->routeIs('events.show') ? 'active-nav-link' : '' }}">Events</a>
                        <a href="{{ route('clubs.index') }}"
                            class="nav-link {{ request()->routeIs('clubs.index') || request()->routeIs('clubs.show') ? 'active-nav-link' : '' }}">Clubs</a>
                        <a href="{{ route('djs.index') }}"
                            class="nav-link {{ request()->routeIs('djs.index') || request()->routeIs('djs.show') ? 'active-nav-link' : '' }}">DJs</a>
                        <a href="{{ route('map.index') }}"
                            class="nav-link {{ request()->routeIs('map.index') ? 'active-nav-link' : '' }}">Karte</a>
                        <a href="{{-- route('partybuses.placeholder') --}}"
                            class="nav-link text-gray-400 dark:text-gray-500 cursor-not-allowed">Partybusse (TBD)</a>
                    </div>
                </div>
            </div>

            {{-- Suche, Aktionen & Mobile Menu Button --}}
            <div class="flex items-center">
                {{-- Globale Suche (Placeholder) --}}
                <div class="hidden md:block mr-4">
                    <form action="{{ route('search.index') }}" method="GET" class="relative">
                        <input type="search" name="q" placeholder="Suche Events, Clubs, DJs..." required
                            value="{{ request('q') }}"
                            class="form-input-field !py-1.5 !text-sm !bg-gray-50 dark:!bg-gray-700 !ring-gray-200 dark:!ring-gray-600 !pl-8">
                        {{-- Mehr Padding links für Icon --}}
                        <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </div>
                        {{-- Kein expliziter Button, Enter reicht --}}
                    </form>
                </div>

                {{-- Theme Switcher --}}
                <button @click="dark = !dark" aria-label="Toggle Dark Mode"
                    class="p-1 rounded-md text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-800 focus:ring-indigo-500 mr-2">
                    <svg x-show="!dark" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <svg x-show="dark" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 3v1m0 16v1m8.485-8.485l-.707.707M5.222 5.222l-.707.707m16.97 0l-.707-.707M5.222 18.778l-.707-.707M21 12h-1M4 12H3m16.97-5.222l-.707-.707M7.722 18.778l-.707.707" />
                    </svg>
                </button>

                {{-- Auth Links / User Dropdown --}}
                <div class="hidden md:block">
                    @guest
                        <a href="{{ route('login') }}" class="nav-link">Login</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="ml-4 btn-secondary !py-1 !text-xs">Registrieren</a>
                        @endif
                    @else
                        {{-- TODO: User Dropdown für eingeloggte Benutzer --}}
                        <span class="text-sm text-gray-500">Hallo, {{ Auth::user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}" class="inline-block ml-2">@csrf<button
                                type="submit" class="text-xs text-gray-500 hover:text-gray-700">Logout</button></form>
                    @endguest
                </div>

                {{-- Mobile Menu Button --}}
                <div class="-mr-2 flex md:hidden">
                    <button @click="isMobileMenuOpen = !isMobileMenuOpen" type="button"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-800 focus:ring-indigo-500"
                        aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Menü öffnen</span>
                        <svg x-show="!isMobileMenuOpen" class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-show="isMobileMenuOpen" class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="isMobileMenuOpen" x-cloak x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="md:hidden absolute top-16 inset-x-0 p-2 transition transform origin-top-right" id="mobile-menu">
            <div
                class="rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 bg-white dark:bg-gray-800 divide-y divide-gray-50 dark:divide-gray-700">
                <div class="px-5 pt-5 pb-6 space-y-3">
                    {{-- Mobile Suche --}}
                    <form action="{{ route('search.index') }}" method="GET" class="relative">
                        <input type="search" name="q" placeholder="Suche..." required
                            value="{{ request('q') }}" class="form-input-field !py-1.5 !text-sm w-full !pl-8">
                        <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </div>
                    </form>
                    {{-- Mobile Nav Links --}}
                    <a href="{{ route('events.index') }}"
                        class="mobile-nav-link {{ request()->routeIs('events.index') || request()->routeIs('events.show') ? 'active-mobile-nav-link' : '' }}">Events</a>
                    <a href="{{ route('clubs.index') }}"
                        class="mobile-nav-link {{ request()->routeIs('clubs.index') || request()->routeIs('clubs.show') ? 'active-mobile-nav-link' : '' }}">Clubs</a>
                    <a href="{{ route('djs.index') }}"
                        class="mobile-nav-link {{ request()->routeIs('djs.index') || request()->routeIs('djs.show') ? 'active-mobile-nav-link' : '' }}">DJs</a>
                    <a href="{{ route('map.index') }}"
                        class="nav-link {{ request()->routeIs('map.index') ? 'active-nav-link' : '' }}">Karte</a>
                    <a href="#"
                        class="mobile-nav-link text-gray-400 dark:text-gray-500 cursor-not-allowed">Partybusse
                        (TBD)</a>
                </div>
                {{-- Mobile Auth Links --}}
                <div class="px-5 py-4">
                    @guest
                        <a href="{{ route('register') }}"
                            class="block w-full text-center btn-primary mb-2">Registrieren</a>
                        <p class="text-center text-xs font-medium text-gray-500 dark:text-gray-400">
                            Bereits Mitglied? <a href="{{ route('login') }}"
                                class="text-indigo-600 dark:text-indigo-400 hover:underline">Login</a>
                        </p>
                    @else
                        {{-- TODO: Mobile User Menu --}}
                        <form method="POST" action="{{ route('logout') }}"><button type="submit"
                                class="block w-full text-left text-sm text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 p-2 rounded">Logout</button>
                        </form>
                    @endguest
                </div>
            </div>
        </div>
    </nav>
    {{-- Füge globale CSS Klassen hier hinzu oder in app.css --}}
    <style>
        .nav-link {
            @apply text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium;
        }

        .mobile-nav-link {
            @apply block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700;
        }

        <style>.active-nav-link {
            @apply bg-indigo-100 dark:bg-gray-700 text-indigo-700 dark:text-white;
        }

        .active-mobile-nav-link {
            @apply bg-indigo-50 dark:bg-gray-700 text-indigo-700 dark:text-white;
        }
    </style>
    </style>
</header>
