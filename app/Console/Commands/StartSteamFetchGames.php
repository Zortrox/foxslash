<?php

namespace App\Console\Commands;

use App\Jobs\SteamFetchGames;
use Illuminate\Console\Command;

class StartSteamFetchGames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:start-steam-fetch-games';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Starts fetching and syncing Steam app list';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        SteamFetchGames::dispatch();

        $this->line('Started fetching Steam apps');
    }
}
