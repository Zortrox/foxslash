<?php

namespace App\Services;

use App\Exceptions\SteamApiException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;
use RuntimeException;

class SteamService
{
    private const API_URL = 'https://api.steampowered.com';

    /** Steam's 64-bit IDs for individual accounts all start with this prefix. */
    private const STEAM_ID64_PATTERN = '/^7656119\d{10}$/';

    /** How long owned games and wishlists are cached, in seconds. */
    public const PROFILE_CACHE_SECONDS = 600;

    // https://store.steampowered.com/api/appdetails?appids=49520
    // https://store.steampowered.com/api/packagedetails?packageids=32848
    // https://store.steampowered.com/actions/ajaxresolvebundles?cc=UA&l=english&bundleids=33369

    /**
     * Fetch every app (games and DLC) from Steam, paging through IStoreService.
     *
     * @return Collection<int, array{appid: int, name: string}>
     */
    public function getAppList(): Collection
    {
        $lastAppId = 0;
        $apps = [];

        do {
            $data = $this->fetchAppListPage($lastAppId);
            $lastAppId = $data['last_appid'] ?? null;
            $apps = [...$apps, ...$data['apps'] ?? []];
        } while (($data['have_more_results'] ?? false) && $lastAppId !== null);

        return collect($apps)->map(fn (array $app): array => [
            'appid' => $app['appid'],
            'name' => $app['name'],
        ]);
    }

    /**
     * Turn a profile reference into a 64-bit SteamID.
     *
     * @param  string  $profile  A SteamID64 or a custom profile URL name (steamcommunity.com/id/<name>).
     * @param  bool|null  $isSteamId64  Force the interpretation; null detects it from the format.
     *
     * @throws SteamApiException
     */
    public function resolveSteamId(string $profile, ?bool $isSteamId64 = null): string
    {
        $profile = trim($profile);

        if ($profile === '') {
            throw SteamApiException::profileNotFound($profile);
        }

        $isSteamId64 ??= (bool) preg_match(self::STEAM_ID64_PATTERN, $profile);

        if ($isSteamId64) {
            if (! ctype_digit($profile)) {
                throw SteamApiException::profileNotFound($profile);
            }

            return $profile;
        }

        return Cache::remember(
            'steam.vanity.'.strtolower($profile),
            now()->addDay(),
            function () use ($profile): string {
                $response = $this->get('/ISteamUser/ResolveVanityURL/v1/', ['vanityurl' => $profile]);

                if ((int) $response->json('response.success') !== 1 || ! $response->json('response.steamid')) {
                    throw SteamApiException::profileNotFound($profile);
                }

                return (string) $response->json('response.steamid');
            },
        );
    }

    /**
     * The games a profile owns, as Steam's GetOwnedGames returns them (with app names).
     *
     * @return array{game_count: int, games: list<array<string, mixed>>}
     *
     * @throws SteamApiException
     */
    public function getOwnedGames(string $steamId): array
    {
        return Cache::remember(
            "steam.owned.{$steamId}",
            self::PROFILE_CACHE_SECONDS,
            function () use ($steamId): array {
                $data = $this->get('/IPlayerService/GetOwnedGames/v1/', [
                    'steamid' => $steamId,
                    'include_appinfo' => 1,
                    'include_played_free_games' => 1,
                ])->json('response') ?? [];

                if (! isset($data['games'])) {
                    throw SteamApiException::profileNotPublic('game library');
                }

                return [
                    'game_count' => (int) ($data['game_count'] ?? count($data['games'])),
                    'games' => array_values($data['games']),
                ];
            },
        );
    }

    /**
     * The app IDs on a profile's wishlist, highest priority first.
     *
     * @return list<int>
     *
     * @throws SteamApiException
     */
    public function getWishlist(string $steamId): array
    {
        return Cache::remember(
            "steam.wishlist.{$steamId}",
            self::PROFILE_CACHE_SECONDS,
            function () use ($steamId): array {
                $data = $this->get('/IWishlistService/GetWishlist/v1/', ['steamid' => $steamId])
                    ->json('response') ?? [];

                // Steam returns an empty response for both private and empty wishlists.
                return collect($data['items'] ?? [])
                    ->sortBy('priority')
                    ->pluck('appid')
                    ->map(fn (mixed $appId): int => (int) $appId)
                    ->values()
                    ->all();
            },
        );
    }

    /**
     * @param  array<string, mixed>  $query
     *
     * @throws SteamApiException
     */
    private function get(string $path, array $query): Response
    {
        try {
            $response = $this->client()->get($path, $query);
        } catch (ConnectionException) {
            throw SteamApiException::unavailable();
        }

        if (! $response->successful()) {
            throw SteamApiException::unavailable();
        }

        return $response;
    }

    private function client(): PendingRequest
    {
        return Http::baseUrl(self::API_URL)
            ->timeout(15)
            ->retry(2, 500, throw: false)
            ->withQueryParameters(['key' => config('services.steam.api_key'), 'format' => 'json']);
    }

    /**
     * @return array{apps?: list<array{appid: int, name: string}>, have_more_results?: bool, last_appid?: int}
     */
    private function fetchAppListPage(int $lastAppId, int $retries = 2): array
    {
        $response = Http::get(self::API_URL.'/IStoreService/GetAppList/v1/', [
            'key' => config('services.steam.api_key'),
            'include_games' => true,
            'include_dlc' => true,
            'max_results' => 50_000,
            'last_appid' => $lastAppId,
            'format' => 'json',
        ]);

        if (! $response->ok()) {
            if ($response->clientError() || $retries === 0) {
                throw new RuntimeException("Failed to fetch the Steam app list: HTTP {$response->status()}".($response->clientError() ? ' (check STEAM_API_KEY)' : ''));
            }

            Sleep::for(5)->seconds();

            return $this->fetchAppListPage($lastAppId, $retries - 1);
        }

        return $response->json('response') ?? [];
    }
}
