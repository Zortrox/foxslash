<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Steam\Provider as SteamProvider;

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
        // Register the Steam provider for Socialite
        Event::listen(function (SocialiteWasCalled $event): void {
            $event->extendSocialite('steam', SteamProvider::class);
        });

        // Public endpoints the steamchecker extension calls on every bundle page load
        RateLimiter::for('steamchecker', fn (Request $request): Limit => Limit::perMinute(120)->by($request->ip()));

        // Set up a global gate that grants all permissions to users with the "Super Admin" role
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });
    }
}
