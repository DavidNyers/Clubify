<?php

use Illuminate\Support\Facades\Route;

// Standard Controller
use App\Http\Controllers\ProfileController;

// Admin Controller
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController; // Optionaler Alias
use App\Http\Controllers\Admin\GenreController as AdminGenreController;
use App\Http\Controllers\Admin\SubscriptionPlanController as AdminSubscriptionPlanController;
use App\Http\Controllers\Admin\ClubController as AdminClubController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\DjController as AdminDjController;
use App\Http\Controllers\Admin\PartnerApplicationController;
use App\Http\Controllers\Admin\RatingModerationController;

// Frontend Controller
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\FrontendClubController;
use App\Http\Controllers\Frontend\FrontendEventController;
use App\Http\Controllers\Frontend\FrontendDjController;
use App\Http\Controllers\Frontend\MapController;
use App\Http\Controllers\Frontend\SearchController;
use App\Http\Controllers\Frontend\EventBookmarkController;
use App\Http\Controllers\Frontend\RatingController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // --- Profil Routes ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // --- Event Bookmark Routes ---
    Route::post('/events/{event}/bookmark-toggle', [EventBookmarkController::class, 'toggle'])->name('events.bookmark.toggle');
    Route::get('/meine-gemerkten-events', [EventBookmarkController::class, 'index'])->name('events.bookmarked.index');
});


// --- Club Frontend Routes ---
// Die Home-Route ist bereits oben definiert, daher hier entfernt.
Route::get('/clubs', [FrontendClubController::class, 'index'])->name('clubs.index');
Route::get('/clubs/{club:slug}', [FrontendClubController::class, 'show'])->name('clubs.show');
Route::post('/clubs/{club}/ratings', [RatingController::class, 'store'])
     ->middleware('verified') // Nur verifizierte User können bewerten
     ->name('ratings.store');
// ------------------------

// --- Event Frontend Routes ---
Route::get('/events', [FrontendEventController::class, 'index'])->name('events.index');
Route::get('/events/{event:slug}', [FrontendEventController::class, 'show'])->name('events.show');
// ------------------------

// --- DJ Frontend Routes ---
Route::get('/djs', [FrontendDjController::class, 'index'])->name('djs.index');
Route::get('/djs/{dj:slug}', [FrontendDjController::class, 'show'])->name('djs.show'); // Nutzt Slug vom DjProfile
// ------------------------

// --- MAP Routes ---
Route::get('/karte', [MapController::class, 'index'])->name('map.index');
// ------------------------

// --- Such-Route ---
Route::get('/suche', [SearchController::class, 'index'])->name('search.index');
// ------------------------

// Admin Dashboard Routes
Route::prefix('admin')
    ->middleware(['auth', 'verified', 'role:Administrator'])
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard'); // admin.dashboard

        // CRUD Routen mit korrekten Controller-Aliasen
        Route::resource('genres', AdminGenreController::class);
        Route::resource('subscription-plans', AdminSubscriptionPlanController::class);
        Route::resource('clubs', AdminClubController::class);
        Route::resource('events', AdminEventController::class);
        Route::resource('users', AdminUserController::class)->except(['create']);
        Route::resource('djs', AdminDjController::class);

        // Partner Application Routes
        Route::get('/partner-applications', [PartnerApplicationController::class, 'index'])->name('partner-applications.index');
        Route::get('/partner-applications/{user}', [PartnerApplicationController::class, 'show'])->name('partner-applications.show');
        Route::patch('/partner-applications/{user}/approve', [PartnerApplicationController::class, 'approve'])->name('partner-applications.approve');
        Route::patch('/partner-applications/{user}/reject', [PartnerApplicationController::class, 'reject'])->name('partner-applications.reject');

         // --- Rating Moderation Routes ---
        Route::get('/ratings-moderation', [RatingModerationController::class, 'index'])->name('ratings.moderation.index');
        Route::patch('/ratings-moderation/{rating}/approve', [RatingModerationController::class, 'approve'])->name('ratings.moderation.approve');
        Route::patch('/ratings-moderation/{rating}/reject', [RatingModerationController::class, 'reject'])->name('ratings.moderation.reject'); // PATCH für Statusänderung oder DELETE zum Löschen
        // Optional: Route::delete('/ratings-moderation/{rating}', [RatingModerationController::class, 'destroy'])->name('ratings.moderation.destroy'); // Für hartes Löschen
});
// ------------------------

Route::get('/map-placeholder', function() { return view('frontend.placeholders.map'); })->name('map.placeholder');
Route::get('/partybuses-placeholder', function() { return view('frontend.placeholders.partybuses'); })->name('partybuses.placeholder');
Route::get('/imprint-placeholder', function() { return view('frontend.placeholders.imprint'); })->name('imprint.placeholder');

require __DIR__.'/auth.php';