<?php

use App\Models\ConnectedAccount;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use SocialiteProviders\Manager\OAuth2\User as SteamUser;
use SocialiteProviders\Steam\Provider as SteamProvider;

const EXAMPLE_STEAM_ID = '76561197960287930';

function fakeSteamUser(array $overrides = []): SteamUser
{
    return (new SteamUser)->map([
        'id' => EXAMPLE_STEAM_ID,
        'nickname' => 'steamuser',
        'name' => 'Steam User',
        'email' => null,
        'avatar' => null,
        ...$overrides,
    ]);
}

function mockSteamCallback(SteamUser $steamUser): void
{
    $provider = Mockery::mock(SteamProvider::class);
    $provider->shouldReceive('user')->andReturn($steamUser);

    Socialite::shouldReceive('driver')->with('steam')->andReturn($provider);
}

test('users are redirected to steam for authentication', function () {
    $provider = Mockery::mock(SteamProvider::class);
    $provider->shouldReceive('redirect')
        ->once()
        ->andReturn(new RedirectResponse('https://steamcommunity.com/openid/login'));

    Socialite::shouldReceive('driver')->once()->with('steam')->andReturn($provider);

    $this->get('/oauth/steam')->assertRedirectContains('steamcommunity.com');
});

test('the real steam driver builds an openid redirect', function () {
    $this->get('/oauth/steam')->assertRedirectContains('steamcommunity.com/openid/login');
});

test('unknown providers are not routed', function () {
    $this->get('/oauth/github')->assertNotFound();
});

test('new users are registered on first steam sign in', function () {
    mockSteamCallback(fakeSteamUser());

    $response = $this->get('/oauth/steam/callback');

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));

    $user = User::sole();
    expect($user->name)->toBe('Steam User')
        ->and($user->email)->toBe(EXAMPLE_STEAM_ID.'@steam.'.config('app.domain'))
        ->and($user->email_verified_at)->not->toBeNull()
        ->and($user->password)->toBeNull();

    $this->assertDatabaseHas('connected_accounts', [
        'user_id' => $user->id,
        'provider' => 'steam',
        'provider_id' => EXAMPLE_STEAM_ID,
        'nickname' => 'steamuser',
    ]);
});

test('the steam avatar is shared as the user avatar', function () {
    mockSteamCallback(fakeSteamUser(['avatar' => 'https://avatars.steamstatic.com/abc_full.jpg']));

    $this->get('/oauth/steam/callback');

    $this->get(route('dashboard'))
        ->assertInertia(fn ($page) => $page->where('auth.user.avatar', 'https://avatars.steamstatic.com/abc_full.jpg'));
});

test('existing users sign in with their linked steam account', function () {
    $user = User::factory()->create();
    $user->connectedAccounts()->create(['provider' => 'steam', 'provider_id' => EXAMPLE_STEAM_ID]);

    mockSteamCallback(fakeSteamUser(['nickname' => 'renamed']));

    $response = $this->get('/oauth/steam/callback');

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertDatabaseCount('users', 1);
    expect($user->connectedAccounts()->sole()->nickname)->toBe('renamed');
});

test('users with two factor enabled are sent to the challenge', function () {
    $user = User::factory()->create([
        'two_factor_secret' => encrypt('secret'),
        'two_factor_confirmed_at' => now(),
    ]);
    $user->connectedAccounts()->create(['provider' => 'steam', 'provider_id' => EXAMPLE_STEAM_ID]);

    mockSteamCallback(fakeSteamUser());

    $this->get('/oauth/steam/callback')->assertRedirect(route('two-factor.login'));

    $this->assertGuest();
    expect(session('login.id'))->toBe($user->id);
});

test('signed in users link their steam account', function () {
    $user = User::factory()->create();

    mockSteamCallback(fakeSteamUser());

    $this->actingAs($user)
        ->get('/oauth/steam/callback')
        ->assertRedirect(route('profile.edit'));

    $this->assertDatabaseHas('connected_accounts', ['user_id' => $user->id, 'provider_id' => EXAMPLE_STEAM_ID]);
});

test('a steam account linked to someone else cannot be linked again', function () {
    $owner = User::factory()->create();
    $owner->connectedAccounts()->create(['provider' => 'steam', 'provider_id' => EXAMPLE_STEAM_ID]);
    $other = User::factory()->create();

    mockSteamCallback(fakeSteamUser());

    $this->actingAs($other)
        ->get('/oauth/steam/callback')
        ->assertRedirect(route('profile.edit'))
        ->assertSessionHasErrors('oauth');

    expect(ConnectedAccount::sole()->user_id)->toBe($owner->id);
});

test('a failed steam response sends the user back to login', function () {
    $provider = Mockery::mock(SteamProvider::class);
    $provider->shouldReceive('user')->andThrow(new RuntimeException('Invalid OpenID response'));
    Socialite::shouldReceive('driver')->with('steam')->andReturn($provider);

    $this->get('/oauth/steam/callback')
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('oauth');

    $this->assertGuest();
});

test('users can disconnect a steam account after confirming their password', function () {
    $user = User::factory()->create();
    $account = $user->connectedAccounts()->create(['provider' => 'steam', 'provider_id' => EXAMPLE_STEAM_ID]);

    $this->actingAs($user)
        ->delete(route('connected-accounts.destroy', $account), ['password' => 'wrong'])
        ->assertSessionHasErrors('password');

    $this->actingAs($user)
        ->delete(route('connected-accounts.destroy', $account), ['password' => 'password'])
        ->assertSessionHasNoErrors();

    $this->assertModelMissing($account);
});

test('users cannot disconnect accounts they do not own', function () {
    $owner = User::factory()->create();
    $account = $owner->connectedAccounts()->create(['provider' => 'steam', 'provider_id' => EXAMPLE_STEAM_ID]);

    $this->actingAs(User::factory()->create())
        ->delete(route('connected-accounts.destroy', $account), ['password' => 'password'])
        ->assertNotFound();

    $this->assertModelExists($account);
});

test('steam users without a password can set one', function () {
    $user = User::factory()->create(['password' => null]);

    $this->actingAs($user)
        ->post(route('user-password.set'), [
            'password' => 'new-Password-123',
            'password_confirmation' => 'new-Password-123',
        ])
        ->assertSessionHasNoErrors();

    expect(Hash::check('new-Password-123', $user->fresh()->password))->toBeTrue();
});

test('the login page shares the steam provider', function () {
    $this->get(route('login'))
        ->assertInertia(fn ($page) => $page->where('oauth.providers.0.id', 'steam'));
});

test('steam users without a password open security settings without confirming a password', function () {
    $user = User::factory()->withoutPassword()->create();

    $this->actingAs($user)
        ->get(route('security.edit'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('settings/Security')->where('oauth.hasPassword', false));
});

test('users with a password still confirm it before security settings', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('security.edit'))
        ->assertRedirect(route('password.confirm'));
});

test('steam users without a password cannot delete their account until they set one', function () {
    $user = User::factory()->withoutPassword()->create();

    $this->actingAs($user)
        ->delete(route('profile.destroy'), ['password' => ''])
        ->assertSessionHasErrors('password');

    expect($user->fresh())->not->toBeNull();
});

test('deleting an account removes its connected steam account', function () {
    $user = User::factory()->withConnectedAccount()->create();

    $this->actingAs($user)
        ->delete(route('profile.destroy'), ['password' => 'password'])
        ->assertRedirect('/');

    expect($user->fresh())->toBeNull();
    $this->assertDatabaseMissing('connected_accounts', ['user_id' => $user->id]);
});
