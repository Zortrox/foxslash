<?php

namespace App\Services;

use App\Models\SteamGame;
use Illuminate\Support\Facades\Storage;

/**
 * A pre-built JSON file of every synced Steam app, so the large list is never rebuilt per request.
 */
class SteamAppListFile
{
    public const PATH = 'steamchecker/app-list.json';

    /**
     * Absolute path to the file, building it first when missing.
     */
    public function path(): string
    {
        if (! Storage::disk('local')->exists(self::PATH)) {
            $this->write();
        }

        return Storage::disk('local')->path(self::PATH);
    }

    /**
     * Rebuild the file from the steam_games table as {"apps":[{"appid":1,"name":"..."}]}.
     */
    public function write(): void
    {
        $disk = Storage::disk('local');
        $disk->makeDirectory(dirname(self::PATH));

        $temporaryPath = $disk->path(self::PATH.'.tmp');
        $handle = fopen($temporaryPath, 'w');
        fwrite($handle, '{"apps":[');

        $first = true;
        SteamGame::query()
            ->select(['appid', 'name'])
            ->orderBy('appid')
            ->lazyById(5000, 'appid')
            ->each(function (SteamGame $game) use ($handle, &$first): void {
                fwrite($handle, ($first ? '' : ',').json_encode(
                    ['appid' => (int) $game->appid, 'name' => $game->name],
                    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE,
                ));
                $first = false;
            });

        fwrite($handle, ']}');
        fclose($handle);

        rename($temporaryPath, $disk->path(self::PATH));
    }
}
