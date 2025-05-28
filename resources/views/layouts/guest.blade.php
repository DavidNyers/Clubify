<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" :class="{ 'dark': dark }" x-data="{ dark: localStorage.getItem('dark') === 'true' }"
    x-init="$watch('dark', val => localStorage.setItem('dark', val))">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Clubify') }} - Authentifizierung</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- Leaflet CSS (falls Karte auf Auth-Seite gebraucht wird, sonst entfernen) --}}
    {{-- <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/> --}}

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="font-sans text-gray-900 dark:text-gray-100 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
        {{-- Logo --}}
        <div>
            <a href="/"
                class="text-4xl font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-500 transition-colors">
                Clubify
            </a>
        </div>

        {{-- Die Formular-Karte --}}
        <div
            class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white dark:bg-gray-800 shadow-xl overflow-hidden sm:rounded-lg">
            {{ $slot }} {{-- Hier wird der Inhalt der Auth-Seiten (login.blade.php etc.) geladen --}}
        </div>

        {{-- Theme Switcher (optional auch hier platzieren) --}}
        <div class="absolute top-4 right-4">
            <button @click="dark = !dark" aria-label="Toggle Dark Mode"
                class="p-2 rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <svg x-show="!dark" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
                <svg x-show="dark" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 3v1m0 16v1m8.485-8.485l-.707.707M5.222 5.222l-.707.707m16.97 0l-.707-.707M5.222 18.778l-.707-.707M21 12h-1M4 12H3m16.97-5.222l-.707-.707M7.722 18.778l-.707.707" />
                </svg>
            </button>
        </div>
    </div>
    {{-- Leaflet JS (falls Karte auf Auth-Seite gebraucht wird) --}}
    {{-- <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script> --}}
</body>

</html>
