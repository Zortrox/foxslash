<?php

use App\Filament\Resources\SteamGames\Pages\ListSteamGames;
use App\Models\SteamGame;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::create(['name' => 'Super Admin']);
    $this->actingAs(User::factory()->create()->assignRole('Super Admin'));
});

test('the aliases column shows the first two aliases and an ellipsis when there are more', function () {
    $twoAliases = SteamGame::factory()->create();
    $twoAliases->aliases()->createMany([['alias' => 'tesv'], ['alias' => 'skyrim']]);

    $manyAliases = SteamGame::factory()->create();
    $manyAliases->aliases()->createMany([['alias' => 'kotor'], ['alias' => 'swkotor'], ['alias' => 'starwarskotor']]);

    Livewire::test(ListSteamGames::class)
        ->assertTableColumnStateSet('aliases', 'tesv, skyrim', $twoAliases)
        ->assertTableColumnStateSet('aliases', 'kotor, swkotor, …', $manyAliases);
});
