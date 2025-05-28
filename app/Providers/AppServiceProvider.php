<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    protected $listen = [
        // ... andere Events ...
        \Illuminate\Auth\Events\Login::class => [ // Importiere die Klasse oben
            \App\Listeners\UpdateUserLastLoginTimestamp::class, // Importiere die Klasse oben
        ],
    ];
}
