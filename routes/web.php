<?php

use App\Http\Controllers\Admin\AssetImportController;
use App\Http\Controllers\Admin\CharacterController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\SkillController;
use App\Http\Controllers\Admin\SupportCardController;
use App\Http\Controllers\Admin\TierBuilderController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\BookmarkController;
use App\Http\Controllers\Public\CmController;
use App\Http\Controllers\Public\DatabaseController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\TierListController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Language Switcher (EN / ID)
|--------------------------------------------------------------------------
*/
Route::get('/lang/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

// Champions Meeting Meta Guides
Route::get('/cm', [CmController::class, 'latest'])->name('cm.latest');
Route::get('/cm-archive', [CmController::class, 'archive'])->name('cm.archive');
Route::get('/cm/{event:slug}', [CmController::class, 'show'])->name('cm.show');
Route::post('/cm/{event:slug}/bookmark', [BookmarkController::class, 'toggle'])->name('cm.bookmark');

// Legacy tier list route redirect / fallback
Route::get('/tier-list', [TierListController::class, 'index'])->name('tier-list');

// Public Master Database & Encyclopedias (Replacing Placeholders)
Route::prefix('guides')->name('guides.')->group(function () {
    Route::get('/umas', [DatabaseController::class, 'umas'])->name('umas');
    Route::get('/supoka', [DatabaseController::class, 'supoka'])->name('supoka');
    Route::get('/skills', [DatabaseController::class, 'skills'])->name('skills');
});

// User Bookmarked CM Guides
Route::middleware('auth')->get('/my-bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');

/*
|--------------------------------------------------------------------------
| Laravel Breeze Dashboard Redirect
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    if (auth()->check() && auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('home');
})->middleware(['auth'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Profile Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin CMS Routes (Protected by Auth & Admin Role Middleware)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // GameTora Asset Importer
    Route::post('assets/import-url', [AssetImportController::class, 'store'])->name('assets.import-url');

    // Events Management
    Route::post('events/{event}/activate', [EventController::class, 'activate'])->name('events.activate');
    Route::post('events/{event}/deactivate', [EventController::class, 'deactivate'])->name('events.deactivate');
    Route::resource('events', EventController::class);

    // Master Database: Characters (Uma)
    Route::resource('characters', CharacterController::class);

    // Master Database: Skills
    Route::resource('skills', SkillController::class);

    // Master Database: Support Cards
    Route::resource('support-cards', SupportCardController::class);

    // Tier Builder
    Route::get('events/{event}/tiers', [TierBuilderController::class, 'index'])->name('events.tiers.index');
    Route::post('events/{event}/tiers', [TierBuilderController::class, 'store'])->name('events.tiers.store');
    Route::put('events/{event}/tiers/{tierEntry}', [TierBuilderController::class, 'update'])->name('events.tiers.update');
    Route::delete('events/{event}/tiers/{tierEntry}', [TierBuilderController::class, 'destroy'])->name('events.tiers.destroy');
});

require __DIR__.'/auth.php';
