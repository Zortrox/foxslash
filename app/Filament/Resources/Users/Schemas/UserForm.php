<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Password;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Account')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),
                    TextInput::make('password')
                        ->password()
                        ->revealable()
                        ->rule(Password::default())
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->saved(fn (?string $state): bool => filled($state))
                        ->helperText(fn (string $operation): ?string => $operation === 'edit' ? 'Leave blank to keep the current password.' : null),
                ]),
            Section::make('Access')
                ->schema([
                    Select::make('roles')
                        ->relationship(
                            name: 'roles',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn (Builder $query): Builder => auth()->user()?->hasRole('Super Admin')
                                ? $query
                                : $query->where('name', '!=', 'Super Admin'),
                        )
                        ->multiple()
                        ->preload()
                        ->searchable(),
                    Select::make('permissions')
                        ->relationship(name: 'permissions', titleAttribute: 'name')
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->helperText('Granted directly, on top of what the roles give.'),
                ]),
        ]);
    }
}
