<?php

namespace App\Filament\Resources\SteamGames\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AliasesRelationManager extends RelationManager
{
    protected static ?string $label = 'Game Alias';

    protected static string $relationship = 'aliases';

    public function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('alias')->required()]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([CreateAction::make()])
            ->columns([TextColumn::make('alias')->label('Alias')])
            ->recordActions([
                DeleteAction::make('Delete')->requiresConfirmation(),
            ])
            ->defaultSort('alias');
    }
}
