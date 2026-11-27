<?php

namespace App\Http\Controllers\Steamchecker;

use App\Http\Controllers\Controller;
use App\Models\SteamGameAlias;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Alternate names that bundle sites use for Steam apps, keyed by appid.
 */
class AliasController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'names' => ['sometimes', 'array', 'max:1000'],
            'names.*' => ['string', 'max:255'],
        ]);

        return response()->json($this->aliasesByAppId($validated['names'] ?? null));
    }

    /**
     * @param  list<string>|null  $names  Only aliases with these names; null for all.
     * @return array<string, list<string>>
     */
    public function aliasesByAppId(?array $names = null): array
    {
        return SteamGameAlias::query()
            ->select(['appid', 'alias'])
            ->when($names !== null, fn ($query) => $query->whereIn('alias', $names))
            ->orderBy('appid')
            ->orderBy('alias')
            ->get()
            ->groupBy('appid')
            ->map(fn ($aliases): array => $aliases->pluck('alias')->all())
            ->all();
    }
}
