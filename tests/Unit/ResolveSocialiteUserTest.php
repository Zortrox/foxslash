<?php

use App\Actions\Socialstream\ResolveSocialiteUser;
use Laravel\Socialite\Contracts\User;
use Laravel\Socialite\Facades\Socialite;
use Tests\TestCase;

uses(TestCase::class);

test('resolves provider user from socialite', function () {
    $user = Mockery::mock(User::class);
    $user->shouldReceive('getEmail')->andReturn('user@example.com');

    $driver = Mockery::mock();
    $driver->shouldReceive('user')->once()->andReturn($user);

    Socialite::shouldReceive('driver')->once()->with('github')->andReturn($driver);

    $resolver = new ResolveSocialiteUser;
    $resolved = $resolver->resolve('github');

    expect($resolved)->toBe($user);
});

test('caches resolved user so provider is only called once per request', function () {
    $user = Mockery::mock(User::class);
    $user->shouldReceive('getEmail')->andReturn('user@example.com');

    $driver = Mockery::mock();
    $driver->shouldReceive('user')->once()->andReturn($user);

    Socialite::shouldReceive('driver')->once()->with('steam')->andReturn($driver);

    $resolver = new ResolveSocialiteUser;

    $first = $resolver->resolve('steam');
    $second = $resolver->resolve('steam');

    expect($first)->toBe($second);
});

test('resolves different users for different providers', function () {
    $githubUser = Mockery::mock(User::class);
    $githubUser->shouldReceive('getEmail')->andReturn('github@example.com');

    $steamUser = Mockery::mock(User::class);
    $steamUser->shouldReceive('getEmail')->andReturn('steam@example.com');

    $githubDriver = Mockery::mock();
    $githubDriver->shouldReceive('user')->once()->andReturn($githubUser);

    $steamDriver = Mockery::mock();
    $steamDriver->shouldReceive('user')->once()->andReturn($steamUser);

    Socialite::shouldReceive('driver')->once()->with('github')->andReturn($githubDriver);
    Socialite::shouldReceive('driver')->once()->with('steam')->andReturn($steamDriver);

    $resolver = new ResolveSocialiteUser;

    expect($resolver->resolve('github'))->toBe($githubUser);
    expect($resolver->resolve('steam'))->toBe($steamUser);
});
