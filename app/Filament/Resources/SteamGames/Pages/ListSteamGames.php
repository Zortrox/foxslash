<?php

namespace App\Filament\Resources\SteamGames\Pages;

use App\Filament\Resources\SteamGames\SteamGameResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSteamGames extends ListRecords
{
    protected static string $resource = SteamGameResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
