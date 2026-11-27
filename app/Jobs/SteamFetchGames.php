<?php

namespace App\Jobs;

use App\Models\SteamGame;
use App\Services\SteamAppListFile;
use App\Services\SteamService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SteamFetchGames implements ShouldQueue
{
    use Queueable;

    /**
     * Paging through every Steam app and upserting them takes longer than a
     * worker's default 60 seconds.
     */
    public int $timeout = 600;

    /**
     * The schedule runs it again tomorrow, so a failed run is not retried.
     */
    public int $tries = 1;

    public bool $failOnTimeout = true;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::debug('Starting app fetching');

        $app_list = app(SteamService::class)->getAppList();

        $chunk_size = 1000;
        $total_chunks = ceil($app_list->count() / 1000);

        $app_list
            ->chunk($chunk_size)
            ->each(function ($chunk, $index) use ($total_chunks) {
                Log::debug(
                    'Upserting app chunk '.
                        $index +
                        1 .
                        ' of '.
                        $total_chunks,
                );
                SteamGame::query()->upsert($chunk->all(), ['appid'], ['name']);
            });

        app(SteamAppListFile::class)->write();

        Log::debug('Finished app fetching');
    }
}
