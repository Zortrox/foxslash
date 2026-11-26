<?php

namespace App\Http\Controllers\Steamchecker;

use App\Actions\Auth\ResolveUserFromSteam;
use App\Exceptions\SteamApiException;
use App\Http\Controllers\Controller;
use App\Services\SteamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * The signed-in extension user's own Steam data, from the Steam account linked to their foxslash account.
 */
class MeController extends Controller
{
    public function __construct(private SteamService $steam) {}

    public function show(Request $request): JsonResponse
    {
        return response()->json(ExtensionTokenController::account($request->user()));
    }

    public function owned(Request $request): JsonResponse
    {
        return $this->forSteamId($request, fn (string $steamId): array => $this->steam->getOwnedGames($steamId));
    }

    public function wishlist(Request $request): JsonResponse
    {
        return $this->forSteamId($request, fn (string $steamId): array => ['appids' => $this->steam->getWishlist($steamId)]);
    }

    /**
     * @param  callable(string): array<string, mixed>  $load
     */
    private function forSteamId(Request $request, callable $load): JsonResponse
    {
        $steamId = $request->user()->connectedAccounts()
            ->where('provider', ResolveUserFromSteam::PROVIDER)
            ->value('provider_id');

        if ($steamId === null) {
            return response()->json(['message' => 'No Steam account is linked to this foxslash account.'], 409);
        }

        try {
            return response()->json(['steam_id' => $steamId, ...$load($steamId)]);
        } catch (SteamApiException $exception) {
            return response()->json(['message' => $exception->getMessage()], $exception->status);
        }
    }
}
