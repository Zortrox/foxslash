<?php

use App\Http\Controllers\Steamchecker\LegacySteamcheckerController;
use App\Http\Controllers\Steamchecker\PageConfigController;
use Illuminate\Support\Facades\Route;

/*
 * URLs used by released steamchecker extension versions. These sit outside the "web"
 * group so they carry no session or CSRF; the alias POST sends raw JSON with no token.
 */
Route::prefix('apps/steamchecker')->middleware('throttle:steamchecker')->group(function () {
    Route::get('owned', [LegacySteamcheckerController::class, 'owned']);
    Route::get('wishlist', [LegacySteamcheckerController::class, 'wishlist']);
    Route::match(['get', 'post'], 'aliases', [LegacySteamcheckerController::class, 'aliases']);
    Route::get('config/v2', PageConfigController::class);
    Route::get('appList.json', [LegacySteamcheckerController::class, 'appList']);
});
