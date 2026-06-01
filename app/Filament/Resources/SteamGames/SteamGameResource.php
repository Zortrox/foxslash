<?php

namespace App\Filament\Resources\SteamGames;

use App\Filament\Resources\SteamGames\Pages\CreateSteamGame;
use App\Filament\Resources\SteamGames\Pages\EditSteamGame;
use App\Filament\Resources\SteamGames\Pages\ListSteamGames;
use App\Filament\Resources\SteamGames\Schemas\SteamGameForm;
use App\Filament\Resources\SteamGames\Tables\SteamGamesTable;
use App\Models\SteamGame;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SteamGameResource extends Resource
{
    protected static ?string $model = SteamGame::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return SteamGameForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SteamGamesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSteamGames::route('/'),
            'create' => CreateSteamGame::route('/create'),
            'edit' => EditSteamGame::route('/{record}/edit'),
        ];
    }
}
