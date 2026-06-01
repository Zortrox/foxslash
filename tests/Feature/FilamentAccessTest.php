<?php

use App\Models\User;
use Filament\PanelRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('allows super admin to access filament panel', function () {
    Role::create(['name' => 'Super Admin']);
    $user = User::factory()->create();
    $user->assignRole('Super Admin');

    $panel = app(PanelRegistry::class)->getDefault();

    expect($user->canAccessPanel($panel))->toBeTrue();
});

it('denies users without super admin role from filament panel', function () {
    $user = User::factory()->create();

    $panel = app(PanelRegistry::class)->getDefault();

    expect($user->canAccessPanel($panel))->toBeFalse();
});

it('denies users with a different role from filament panel', function () {
    Role::create(['name' => 'Super Admin']);
    Role::create(['name' => 'Editor']);
    $user = User::factory()->create();
    $user->assignRole('Editor');

    $panel = app(PanelRegistry::class)->getDefault();

    expect($user->canAccessPanel($panel))->toBeFalse();
});
