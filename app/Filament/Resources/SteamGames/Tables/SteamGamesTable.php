<?php

namespace App\Filament\Resources\SteamGames\Tables;

use App\Models\SteamGame;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SteamGamesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('aliases'))
            ->columns([
                TextColumn::make('appid')->numeric()->sortable(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('aliases')
                    ->state(fn (SteamGame $record): string => self::aliasSummary($record)),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    /**
     * The first two aliases, followed by an ellipsis when there are more.
     */
    private static function aliasSummary(SteamGame $record): string
    {
        $aliases = $record->aliases->pluck('alias');
        $summary = $aliases->take(2)->implode(', ');

        return $aliases->count() > 2 ? $summary.', …' : $summary;
    }
}
