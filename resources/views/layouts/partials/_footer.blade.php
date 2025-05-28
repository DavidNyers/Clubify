<footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-auto">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            {{-- Über Uns / Logo --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Clubify</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Deine zentrale Plattform für das Nachtleben. Finde die besten Clubs, Events und DJs in deiner Nähe.
                </p>
                {{-- Social Media Icons (Beispiele) --}}
                <div class="mt-4 flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                        <span class="sr-only">Facebook</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">...</svg> {{-- Facebook Icon --}}
                    </a>
                    <a href="#" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                        <span class="sr-only">Instagram</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">...</svg> {{-- Instagram Icon --}}
                    </a>
                    {{-- Weitere Icons --}}
                </div>
            </div>

            {{-- Quick Links --}}
            <div>
                <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Quick
                    Links</h4>
                <ul class="space-y-2">
                    <li><a href="#"
                            class="text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">Events
                            suchen</a></li>
                    <li><a href="#"
                            class="text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">Clubs
                            entdecken</a></li>
                    <li><a href="#"
                            class="text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">Top
                            DJs</a></li>
                </ul>
            </div>

            {{-- Rechtliches --}}
            <div>
                <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                    Rechtliches</h4>
                <ul class="space-y-2">
                    <li><a href="#"
                            class="text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">Impressum</a>
                    </li>
                    <li><a href="#"
                            class="text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">Datenschutz</a>
                    </li>
                    <li><a href="#"
                            class="text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">AGB</a>
                    </li>
                    <li><a href="#"
                            class="text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">Kontakt</a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Copyright --}}
        <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">© {{ date('Y') }} Clubify. Alle Rechte vorbehalten.
            </p>
        </div>
    </div>
</footer>
