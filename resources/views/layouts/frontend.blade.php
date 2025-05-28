<!DOCTYPE html>
{{-- Stelle sicher, dass x-data etc. für den Theme Switcher vorhanden ist --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" :class="{ 'dark': dark }" x-data="{
    dark: localStorage.getItem('dark') === 'true',
    isMobileMenuOpen: false
}"
    x-init="$watch('dark', val => localStorage.setItem('dark', val))" class="h-full scroll-smooth"> {{-- scroll-smooth für Ankerlinks --}}

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Dynamischer Titel und Meta-Beschreibung (Wichtig für SEO!) --}}
    <title>{{ $title ?? config('app.name', 'Clubify') }}</title>
    <meta name="description" content="{{ $description ?? 'Deine zentrale Plattform für Clubs, Events und DJs.' }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Styles & Scripts -->
    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    {{-- Vite CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    {{-- Falls spezifische Frontend-Styles benötigt werden --}}
    {{-- @stack('styles') --}}
</head>

<body
    class="font-sans antialiased bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 flex flex-col min-h-screen">

    {{-- Header --}}
    @include('layouts.partials._header')

    {{-- Hauptinhalt --}}
    <main class="flex-grow">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    @include('layouts.partials._footer')

    {{-- Falls spezifische Frontend-Scripts benötigt werden --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @stack('scripts')
</body>

</html>
