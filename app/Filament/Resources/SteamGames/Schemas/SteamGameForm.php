<?php

namespace App\Filament\Resources\SteamGames\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;

class SteamGameForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Group::make([
                TextInput::make('appid')
                    ->numeric()
                    ->readOnly()
                    ->label('App ID'),
                TextInput::make('name')->required(),
            ])->columns(1),
        ]);
    }
}
