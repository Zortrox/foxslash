<?php

namespace App\Filament\Resources\SteamGames\Pages;

use App\Filament\Resources\SteamGames\SteamGameResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSteamGame extends EditRecord
{
    protected static string $resource = SteamGameResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
