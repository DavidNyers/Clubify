{{-- resources/views/layouts/guest.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" :class="{ 'dark': dark }" x-data="{
    dark: localStorage.getItem('dark') === 'true',
    isMobileMenuOpen: false {{-- Alpine-Variable für mobilen Header --}}
}"
    x-init="$watch('dark', val => localStorage.setItem('dark', val))" class="h-full scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Clubify') }} - {{ $title ?? 'Authentifizierung' }}</title>
    <meta name="description" content="{{ $description ?? 'Login oder Registrierung für Clubify.' }}">


    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    {{-- Globale Form Styles und Button Styles sollten hier geladen werden (oder in app.css) --}}
    {{-- Stelle sicher, dass .nav-link, .mobile-nav-link, .btn-primary etc. verfügbar sind --}}
</head>

<body
    class="font-sans antialiased bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 flex flex-col min-h-screen">

    {{-- === Bestehenden Header Inkludieren === --}}
    @include('layouts.partials._header')

    {{-- Hauptinhalt mit Authentifizierungsformular --}}
    {{-- Wir brauchen hier wieder einen Wrapper, um das Formular zu zentrieren und zu gestalten --}}
    <main class="flex-grow flex flex-col sm:justify-center items-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full sm:max-w-md bg-white dark:bg-gray-800 shadow-xl overflow-hidden sm:rounded-lg">
            <div class="px-6 py-8"> {{-- Inneres Padding für das Formular --}}
                {{ $slot }}
            </div>
        </div>
    </main>

    {{-- === Bestehenden Footer Inkludieren === --}}
    @include('layouts.partials._footer')

    {{-- Kein Leaflet hier, es sei denn, es wird wirklich auf Auth-Seiten gebraucht --}}
    @stack('scripts') {{-- Für eventuelle Skripte der Auth-Seiten --}}
</body>

</html>
