<?php

namespace App\Filament\Resources\SteamGames\Pages;

use App\Filament\Resources\SteamGames\SteamGameResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSteamGame extends CreateRecord
{
    protected static string $resource = SteamGameResource::class;
}
