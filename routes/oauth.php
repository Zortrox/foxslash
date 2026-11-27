<?php

use App\Http\Controllers\Auth\AuthenticatedSessionSteamController;
use App\Http\Controllers\Auth\SetPasswordController;
use App\Http\Controllers\ConnectedAccountController;
use App\Http\Controllers\Steamchecker\ExtensionConnectController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::get('/oauth/{provider}', [AuthenticatedSessionSteamController::class, 'redirect'])
        ->whereIn('provider', ['steam'])
        ->name('oauth.redirect');

    // Steam's OpenID flow returns with GET; POST is kept for providers that post back.
    Route::match(['get', 'post'], '/oauth/{provider}/callback', [AuthenticatedSessionSteamController::class, 'callback'])
        ->whereIn('provider', ['steam'])
        ->name('oauth.callback');

    // No auth middleware on the GET: the controller rejects unknown redirect URIs before sending guests to log in.
    Route::get('/extension/connect', [ExtensionConnectController::class, 'show'])->name('extension.connect');
    Route::post('/extension/connect', [ExtensionConnectController::class, 'store'])->middleware('auth')->name('extension.connect.store');

    Route::middleware('auth')->group(function () {
        Route::delete('/settings/connected-accounts/{connectedAccount}', [ConnectedAccountController::class, 'destroy'])
            ->name('connected-accounts.destroy');

        Route::post('/settings/password', [SetPasswordController::class, 'store'])
            ->name('user-password.set');
    });
});
