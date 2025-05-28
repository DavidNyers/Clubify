<x-guest-layout>
    {{-- Session Status (z.B. nach Passwort-Reset-Anforderung) --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        {{-- Email Address --}}
        <div>
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input id="email" class="form-input-field mt-1 w-full" type="email" name="email" :value="old('email')"
                required autofocus autocomplete="username" />
            {{-- Fehleranzeige mit unserer Klasse --}}
            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <div class="flex items-center justify-between">
                <label for="password" class="form-label">{{ __('Password') }}</label>
                @if (Route::has('password.request'))
                    <div class="text-sm">
                        <a class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300"
                            href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    </div>
                @endif
            </div>
            <div class="mt-1">
                <input id="password" class="form-input-field w-full" type="password" name="password" required
                    autocomplete="current-password" />
            </div>
            @error('password')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Remember Me --}}
        <div class="flex items-center justify-between">
            <label for="remember_me" class="flex items-center">
                <input id="remember_me" type="checkbox" class="form-checkbox-field h-4 w-4" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-300">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div>
            <button type="submit" class="btn-primary w-full flex justify-center">
                {{ __('Log in') }}
            </button>
        </div>

        {{-- Optional: Link zur Registrierung --}}
        @if (Route::has('register'))
            <p class="text-sm text-center text-gray-600 dark:text-gray-400">
                Noch kein Account?
                <a href="{{ route('register') }}"
                    class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                    Jetzt registrieren
                </a>
            </p>
        @endif
    </form>
</x-guest-layout>
