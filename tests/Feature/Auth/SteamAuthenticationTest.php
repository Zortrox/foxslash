<?php

use App\Models\User;
use App\Providers\SocialiteProviders\SteamForSocialstream;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;
use SocialiteProviders\Manager\OAuth2\User as SteamUser;

$example_steam_id = '76560000000000000';

test('users are redirected to steam for authentication', function () {
    $mockProvider = Mockery::mock(SteamForSocialstream::class);
    $mockProvider->shouldReceive('redirect')
        ->once()
        ->andReturn(new RedirectResponse('https://steamcommunity.com/openid/login?openid.ns=http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0'));

    Socialite::shouldReceive('driver')
        ->once()
        ->with('steam')
        ->andReturn($mockProvider);

    $this->get('/oauth/steam')->assertRedirectContains('steamcommunity.com');
});

test('new users can register using a steam account', function () use ($example_steam_id) {
    $steamUser = (new SteamUser)->map([
        'id' => $example_steam_id,
        'nickname' => 'steamuser',
        'name' => 'Steam User',
        'email' => null,
        'avatar' => null,
        'avatar_original' => null,
    ]);

    $mockProvider = Mockery::mock(SteamForSocialstream::class);
    $mockProvider->shouldReceive('user')->once()->andReturn($steamUser);

    Socialite::shouldReceive('driver')
        ->once()
        ->with('steam')
        ->andReturn($mockProvider);

    Session::put('socialstream.previous_url', route('register'));

    $response = $this->get('/oauth/steam/callback');

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));

    $this->assertDatabaseHas('users', ['name' => 'Steam User']);
    $this->assertDatabaseHas('connected_accounts', [
        'provider' => 'steam',
        'provider_id' => $example_steam_id,
    ]);
});

test('existing users can sign in using a steam account', function () use ($example_steam_id) {
    $user = User::factory()->create();
    $user->connectedAccounts()->create([
        'provider' => 'steam',
        'provider_id' => $example_steam_id,
        'token' => 'fake-token',
    ]);

    $steamUser = (new SteamUser)->map([
        'id' => $example_steam_id,
        'nickname' => 'steamuser',
        'name' => 'Steam User',
        'email' => null,
        'avatar' => null,
        'avatar_original' => null,
    ]);

    // The sign-in pipeline (RedirectIfTwoFactorAuthenticatable + AttemptToAuthenticate)
    // calls resolver->resolve() twice, so no call-count constraint is set here.
    $mockProvider = Mockery::mock(SteamForSocialstream::class);
    $mockProvider->shouldReceive('user')->andReturn($steamUser);

    Socialite::shouldReceive('driver')
        ->with('steam')
        ->andReturn($mockProvider);

    Session::put('socialstream.previous_url', route('login'));

    $response = $this->get('/oauth/steam/callback');

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertDatabaseCount('users', 1);
});
