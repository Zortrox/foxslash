<?php

namespace App\Providers;

use App\Providers\SocialiteProviders\SteamForSocialstream;
use App\Providers\SocialiteProviders\SteamForSocialstreamServer;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Manager\OAuth1\Server;

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
        Event::listen(function (
            \SocialiteProviders\Manager\SocialiteWasCalled $event,
        ) {
            $event->extendSocialite('steam', SteamForSocialstream::class);
        });
    }
}
