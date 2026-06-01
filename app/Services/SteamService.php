<?php

namespace App\Services;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use RuntimeException;

class SteamService
{
    private static $baseUrl = 'https://api.steampowered.com';

    // https://store.steampowered.com/api/appdetails?appids=49520

    // https://store.steampowered.com/api/packagedetails?packageids=32848

    // https://store.steampowered.com/actions/ajaxresolvebundles?cc=UA&l=english&bundleids=33369

    /**
     *
     * @return Collection<array{appid: number, name: string}>
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     */
    public function getAppList(): Collection
    {
        $response = Http::get(static::$baseUrl . '/ISteamApps/GetAppList/v2', [
            'key' => config('services.steam.api_key'),
            'format' => 'json',
        ]);

        if (!$response->ok()) {
            throw new RuntimeException('Failed to fetch Steam app list');
        }

        return collect($response->json('applist.apps'));
    }

    public function getUserOwnedGames()
    {
        // https://api.steampowered.com/IPlayerService/GetOwnedGames/v1/?key=&steamid=&format=json
    }

    public function getWishlist()
    {
        // https://api.steampowered.com/IWishlistService/GetWishlist/v1/?key=&steamid=&format=json
    }
}
