<?php

use App\Http\Controllers\Steamchecker\SteamcheckerPageController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');
Route::get('apps/steamchecker', SteamcheckerPageController::class)->name('steamchecker');
Route::inertia('apps/steamchecker/privacy-policy', 'apps/SteamcheckerPrivacy')->name('steamchecker.privacy');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/oauth.php';
