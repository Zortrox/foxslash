<?php

use App\Http\Controllers\Steamchecker\AliasController;
use App\Http\Controllers\Steamchecker\AppListController;
use App\Http\Controllers\Steamchecker\ExtensionTokenController;
use App\Http\Controllers\Steamchecker\MeController;
use App\Http\Controllers\Steamchecker\PageConfigController;
use App\Http\Controllers\Steamchecker\ProfileGamesController;
use App\Http\Controllers\SteamGameSearchController;
use App\Models\SteamGame;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('games/search', function (Request $request) {
    return SteamGame::search($request->input('query'))
        ->options([
            'query_by' => 'formatted_name',
            'sort_by' => '_text_match:desc,formatted_name_length:asc', // formatted_name:asc
            'prioritize_token_position' => true,
            'enable_synonyms' => false,
            'enable_typos_for_numerical_tokens' => false,
        ])
        ->get();
});

Route::get('games', SteamGameSearchController::class.'@index');

Route::prefix('steamchecker')->name('api.steamchecker.')->middleware('throttle:steamchecker')->group(function () {
    Route::get('profiles/{profile}/owned', [ProfileGamesController::class, 'owned'])->name('owned');
    Route::get('profiles/{profile}/wishlist', [ProfileGamesController::class, 'wishlist'])->name('wishlist');
    Route::get('aliases', [AliasController::class, 'index'])->name('aliases');
    Route::get('apps', AppListController::class)->name('apps');
    Route::get('config/v2', PageConfigController::class)->name('config');

    Route::post('token', [ExtensionTokenController::class, 'store'])->name('token.store');

    Route::middleware(['auth:sanctum', 'abilities:'.ExtensionTokenController::ABILITY])->group(function () {
        Route::delete('token', [ExtensionTokenController::class, 'destroy'])->name('token.destroy');
        Route::get('me', [MeController::class, 'show'])->name('me');
        Route::get('me/owned', [MeController::class, 'owned'])->name('me.owned');
        Route::get('me/wishlist', [MeController::class, 'wishlist'])->name('me.wishlist');
    });
});
