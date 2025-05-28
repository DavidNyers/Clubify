{{-- resources/views/components/frontend-layout.blade.php --}}
@props([
    'title' => null,
    'description' => null,
])

{{-- Dieses ist die Wrapper-Komponente, sie inkludiert das eigentliche Layout --}}
@include('layouts.frontend', [
    'title' => $title,
    'description' => $description,
    'slot' => $slot, // Wichtig: Den Slot weitergeben!
])
