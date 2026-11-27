<?php

namespace App\Http\Controllers\Steamchecker;

use App\Exceptions\SteamApiException;
use App\Http\Controllers\Controller;
use App\Services\SteamAppListFile;
use App\Services\SteamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * The /apps/steamchecker/* URLs and JSON shapes that released extension versions call,
 * mapped onto the same services as /api/steamchecker.
 */
class LegacySteamcheckerController extends Controller
{
    public function __construct(private SteamService $steam) {}

    /**
     * GET ?steamid=<id or vanity>&id64=1 → {"response":{"game_count":..,"games":[..]},"aliases":{}}
     */
    public function owned(Request $request): JsonResponse
    {
        $profile = (string) $request->query('steamid', '');

        if ($profile === '') {
            return $this->message('No steam profile argument');
        }

        try {
            $steamId = $this->steam->resolveSteamId($profile, $request->boolean('id64'));

            return response()->json([
                'response' => $this->steam->getOwnedGames($steamId),
                'aliases' => (object) [],
            ]);
        } catch (SteamApiException $exception) {
            return $this->message($exception->getMessage(), $exception->status);
        }
    }

    /**
     * GET ?id=<id or vanity>&type=id64|id → {"wishlist":["appid",..]}
     */
    public function wishlist(Request $request): JsonResponse
    {
        $profile = (string) $request->query('id', '');

        if ($profile === '') {
            return $this->message('No steam profile argument');
        }

        try {
            $steamId = $this->steam->resolveSteamId($profile, $request->query('type') === 'id64');
            $appIds = $this->steam->getWishlist($steamId);
        } catch (SteamApiException $exception) {
            return $this->message($exception->getMessage(), $exception->status);
        }

        if ($appIds === []) {
            return $this->message('No wishlist items', 404);
        }

        return response()->json(['wishlist' => array_map('strval', $appIds)]);
    }

    /**
     * POST {"type":"all"} or {"type":"some","names":[..]}; the body is raw JSON without a content type.
     */
    public function aliases(Request $request, AliasController $aliases): JsonResponse
    {
        $body = json_decode($request->getContent(), true);
        $names = is_array($body) && ($body['type'] ?? 'all') !== 'all'
            ? array_values(array_filter((array) ($body['names'] ?? []), 'is_string'))
            : null;

        return response()->json((object) $aliases->aliasesByAppId($names));
    }

    /**
     * {"applist":{"apps":[..]}}, streamed around the pre-built {"apps":[..]} file.
     */
    public function appList(SteamAppListFile $appList): StreamedResponse
    {
        $path = $appList->path();

        return response()->stream(function () use ($path): void {
            echo '{"applist":';
            readfile($path);
            echo '}';
        }, 200, [
            'Content-Type' => 'application/json',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    private function message(string $message, int $status = 422): JsonResponse
    {
        return response()->json(['message' => $message], $status);
    }
}
