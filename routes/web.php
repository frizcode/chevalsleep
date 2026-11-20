<?php

use App\Http\Controllers\Admin\CharacterController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\TierBuilderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\TierListController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [TierListController::class, 'index'])->name('home');

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
