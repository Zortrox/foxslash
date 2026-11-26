<?php

namespace App\Http\Controllers\Steamchecker;

use App\Exceptions\SteamApiException;
use App\Http\Controllers\Controller;
use App\Services\SteamService;
use Illuminate\Http\JsonResponse;

/**
 * Owned games and wishlists for a Steam profile, given as a SteamID64 or custom URL name.
 */
class ProfileGamesController extends Controller
{
    public function __construct(private SteamService $steam) {}

    public function owned(string $profile): JsonResponse
    {
        try {
            $steamId = $this->steam->resolveSteamId($profile);

            return response()->json(['steam_id' => $steamId, ...$this->steam->getOwnedGames($steamId)]);
        } catch (SteamApiException $exception) {
            return response()->json(['message' => $exception->getMessage()], $exception->status);
        }
    }

    public function wishlist(string $profile): JsonResponse
    {
        try {
            $steamId = $this->steam->resolveSteamId($profile);

            return response()->json(['steam_id' => $steamId, 'appids' => $this->steam->getWishlist($steamId)]);
        } catch (SteamApiException $exception) {
            return response()->json(['message' => $exception->getMessage()], $exception->status);
        }
    }
}
