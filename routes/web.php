<?php

use App\Http\Controllers\Admin\AssetImportController;
use App\Http\Controllers\Admin\CharacterController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\TierBuilderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\CmController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\TierListController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

// Champions Meeting Meta Guides
Route::get('/cm', [CmController::class, 'latest'])->name('cm.latest');
Route::get('/cm/{event:slug}', [CmController::class, 'show'])->name('cm.show');

// Legacy tier list route redirect / fallback
Route::get('/tier-list', [TierListController::class, 'index'])->name('tier-list');

// Encyclopedia & Wiki Placeholders (Cheval Grand Coming Soon Views)
Route::prefix('guides')->name('guides.')->group(function () {
    Route::get('/umas', function () {
        return view('public.guides.placeholder', [
            'title' => 'Uma Musume Encyclopedia',
            'subtitle' => 'Comprehensive database of all Global & JP Uma Musume racers, growth rates, aptitudes, and hidden mechanics.',
            'category' => 'Uma Database',
        ]);
    })->name('umas');

    Route::get('/supoka', function () {
        return view('public.guides.placeholder', [
            'title' => 'Support Card (Supoka) Compendium',
            'subtitle' => 'Detailed tier ranking, hint skill pools, training event options, and specialization multipliers for all Support Cards.',
            'category' => 'Support Cards',
        ]);
    })->name('supoka');

    Route::get('/skills', function () {
        return view('public.guides.placeholder', [
            'title' => 'Skills & Race Mechanics Guide',
            'subtitle' => 'In-depth breakdown of acceleration phases, target velocity calculation, stamina drain mechanics, and track factor conversions.',
            'category' => 'Mechanics & Skills',
        ]);
    })->name('skills');
});

/*
|--------------------------------------------------------------------------
| Laravel Breeze Dashboard Redirect
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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
| Admin CMS Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // GameTora Asset Importer
    Route::post('assets/import-url', [AssetImportController::class, 'store'])->name('assets.import-url');

    // Events Management
    Route::post('events/{event}/activate', [EventController::class, 'activate'])->name('events.activate');
    Route::resource('events', EventController::class);

    // Characters Management
    Route::resource('characters', CharacterController::class);

    // Tier Builder
    Route::get('events/{event}/tiers', [TierBuilderController::class, 'index'])->name('events.tiers.index');
    Route::post('events/{event}/tiers', [TierBuilderController::class, 'store'])->name('events.tiers.store');
    Route::put('events/{event}/tiers/{tierEntry}', [TierBuilderController::class, 'update'])->name('events.tiers.update');
    Route::delete('events/{event}/tiers/{tierEntry}', [TierBuilderController::class, 'destroy'])->name('events.tiers.destroy');
});

require __DIR__.'/auth.php';
