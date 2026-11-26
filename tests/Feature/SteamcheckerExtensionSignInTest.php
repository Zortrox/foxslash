<?php

use App\Models\ExtensionConnectCode;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\PersonalAccessToken;

const EXT_REDIRECT = 'https://abcdefghijklmnopabcdefghijklmnop.chromiumapp.org/';
const EXT_STEAM_ID = '76561197960287930';

beforeEach(function () {
    config(['services.steamchecker.redirect_uris' => [EXT_REDIRECT]]);
    Http::preventStrayRequests();
});

function pkcePair(): array
{
    $verifier = rtrim(strtr(base64_encode(random_bytes(48)), '+/', '-_'), '=');
    $challenge = rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');

    return [$verifier, $challenge];
}

function connectParams(string $challenge, array $overrides = []): array
{
    return [
        'redirect_uri' => EXT_REDIRECT,
        'state' => 'state-123',
        'code_challenge' => $challenge,
        'code_challenge_method' => 'S256',
        ...$overrides,
    ];
}

function steamUser(): User
{
    return User::factory()->withConnectedAccount('steam', fn ($factory) => $factory->state([
        'provider_id' => EXT_STEAM_ID,
        'nickname' => 'gaben',
        'avatar_path' => 'https://avatars.steamstatic.com/a_full.jpg',
    ]))->create(['name' => 'Zortrox']);
}

/**
 * Run the consent step and return the code from the redirect.
 */
function allowAndGetCode(User $user, string $challenge): string
{
    $response = test()->actingAs($user)
        ->post(route('extension.connect.store'), [...connectParams($challenge), 'decision' => 'allow']);

    $location = $response->headers->get('Location');
    expect($location)->toStartWith(EXT_REDIRECT.'?');
    parse_str(parse_url($location, PHP_URL_QUERY), $query);
    expect($query['state'])->toBe('state-123');

    return $query['code'];
}

/**
 * Compare URLs ignoring query parameter order (fullUrl() sorts them).
 */
function expectSameUrl(?string $actual, string $expected): void
{
    expect($actual)->not->toBeNull();
    parse_str((string) parse_url($actual, PHP_URL_QUERY), $actualQuery);
    parse_str((string) parse_url($expected, PHP_URL_QUERY), $expectedQuery);
    ksort($actualQuery);
    ksort($expectedQuery);

    expect(strtok($actual, '?'))->toBe(strtok($expected, '?'))
        ->and($actualQuery)->toBe($expectedQuery);
}

function exchange(string $code, string $verifier, array $overrides = [])
{
    return test()->postJson('/api/steamchecker/token', [
        'code' => $code,
        'code_verifier' => $verifier,
        'redirect_uri' => EXT_REDIRECT,
        'device_name' => 'SteamChecker (Chrome)',
        ...$overrides,
    ]);
}

describe('connect page', function () {
    test('an unlisted redirect uri is rejected before the login page', function () {
        [, $challenge] = pkcePair();

        $this->get(route('extension.connect', connectParams($challenge, ['redirect_uri' => 'https://evil.example/'])))
            ->assertStatus(400);

        $this->actingAs(steamUser())
            ->post(route('extension.connect.store'), [...connectParams($challenge, ['redirect_uri' => 'https://evil.example/']), 'decision' => 'allow'])
            ->assertStatus(400);

        expect(ExtensionConnectCode::count())->toBe(0);
    });

    test('a missing or non-S256 challenge is rejected', function () {
        [, $challenge] = pkcePair();

        $this->get(route('extension.connect', connectParams($challenge, ['code_challenge_method' => 'plain'])))->assertStatus(400);
        $this->get(route('extension.connect', connectParams('short')))->assertStatus(400);
    });

    test('guests are sent to log in and come back to the connect page', function () {
        [, $challenge] = pkcePair();
        $url = route('extension.connect', connectParams($challenge));

        $this->get($url)->assertRedirect(route('login'));

        expectSameUrl(session('url.intended'), $url);
    });

    test('users without steam are asked to link it and return here afterwards', function () {
        [, $challenge] = pkcePair();
        $url = route('extension.connect', connectParams($challenge));

        $this->actingAs(User::factory()->create())
            ->get($url)
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('auth/ExtensionConnect')->where('steamLinked', false));

        expectSameUrl(session('url.intended'), $url);
    });

    test('the consent page shows the linked steam account', function () {
        [, $challenge] = pkcePair();

        $this->actingAs(steamUser())
            ->get(route('extension.connect', connectParams($challenge)))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('auth/ExtensionConnect')
                ->where('steamLinked', true)
                ->where('steamName', 'gaben')
                ->where('state', 'state-123'));
    });

    test('cancel redirects back with access_denied', function () {
        [, $challenge] = pkcePair();

        $this->actingAs(steamUser())
            ->post(route('extension.connect.store'), [...connectParams($challenge), 'decision' => 'cancel'])
            ->assertRedirect(EXT_REDIRECT.'?error=access_denied&state=state-123');

        expect(ExtensionConnectCode::count())->toBe(0);
    });

    test('allow stores only a hash of the code, bound to the user', function () {
        [, $challenge] = pkcePair();
        $user = steamUser();

        $code = allowAndGetCode($user, $challenge);

        $stored = ExtensionConnectCode::sole();
        expect($stored->code_hash)->toBe(hash('sha256', $code))
            ->and($stored->code_hash)->not->toBe($code)
            ->and($stored->user_id)->toBe($user->id)
            ->and(strlen($code))->toBeGreaterThanOrEqual(43);
    });
});

describe('token exchange', function () {
    test('a valid code becomes a steamchecker-only token', function () {
        [$verifier, $challenge] = pkcePair();
        $user = steamUser();
        $code = allowAndGetCode($user, $challenge);

        $response = exchange($code, $verifier)
            ->assertOk()
            ->assertJsonPath('account', [
                'name' => 'Zortrox',
                'steam_id' => EXT_STEAM_ID,
                'steam_name' => 'gaben',
                'avatar_url' => 'https://avatars.steamstatic.com/a_full.jpg',
            ]);

        $token = PersonalAccessToken::findToken($response->json('token'));
        expect($token->tokenable_id)->toBe($user->id)
            ->and($token->name)->toBe('SteamChecker (Chrome)')
            ->and($token->abilities)->toBe(['steamchecker']);
    });

    test('a code works only once', function () {
        [$verifier, $challenge] = pkcePair();
        $code = allowAndGetCode(steamUser(), $challenge);

        exchange($code, $verifier)->assertOk();
        exchange($code, $verifier)->assertStatus(400)->assertJsonStructure(['message']);
    });

    test('the wrong verifier is rejected and burns the code', function () {
        [$verifier, $challenge] = pkcePair();
        [$otherVerifier] = pkcePair();
        $code = allowAndGetCode(steamUser(), $challenge);

        exchange($code, $otherVerifier)->assertStatus(400);
        exchange($code, $verifier)->assertStatus(400);
    });

    test('a different redirect uri is rejected', function () {
        [$verifier, $challenge] = pkcePair();
        $code = allowAndGetCode(steamUser(), $challenge);

        exchange($code, $verifier, ['redirect_uri' => 'https://other.chromiumapp.org/'])->assertStatus(400);
    });

    test('an expired code is rejected', function () {
        [$verifier, $challenge] = pkcePair();
        $code = allowAndGetCode(steamUser(), $challenge);

        $this->travel(61)->seconds();

        exchange($code, $verifier)->assertStatus(400);
    });

    test('malformed requests are a 400 with a message', function () {
        $this->postJson('/api/steamchecker/token', ['code' => 'x'])
            ->assertStatus(400)
            ->assertJsonStructure(['message']);
    });
});

describe('signed-in api', function () {
    function steamcheckerToken(User $user, array $abilities = ['steamchecker']): string
    {
        return $user->createToken('SteamChecker (Chrome)', $abilities)->plainTextToken;
    }

    test('me returns the account', function () {
        $user = steamUser();

        $this->withToken(steamcheckerToken($user))
            ->getJson('/api/steamchecker/me')
            ->assertOk()
            ->assertJsonPath('steam_id', EXT_STEAM_ID);
    });

    test('me owned and wishlist use the linked steam account', function () {
        Http::fake([
            'api.steampowered.com/IPlayerService/GetOwnedGames/*' => Http::response(['response' => [
                'game_count' => 1,
                'games' => [['appid' => 10, 'name' => 'Counter-Strike']],
            ]]),
            'api.steampowered.com/IWishlistService/GetWishlist/*' => Http::response(['response' => ['items' => [
                ['appid' => 620, 'priority' => 1],
            ]]]),
        ]);
        $token = steamcheckerToken(steamUser());

        $this->withToken($token)->getJson('/api/steamchecker/me/owned')
            ->assertOk()
            ->assertJsonPath('steam_id', EXT_STEAM_ID)
            ->assertJsonPath('games.0.appid', 10);

        $this->withToken($token)->getJson('/api/steamchecker/me/wishlist')
            ->assertOk()
            ->assertExactJson(['steam_id' => EXT_STEAM_ID, 'appids' => [620]]);
    });

    test('a private library keeps the profile endpoint error', function () {
        Http::fake(['api.steampowered.com/IPlayerService/GetOwnedGames/*' => Http::response(['response' => []])]);

        $this->withToken(steamcheckerToken(steamUser()))
            ->getJson('/api/steamchecker/me/owned')
            ->assertNotFound()
            ->assertJsonStructure(['message']);
    });

    test('no linked steam account is a 409', function () {
        $this->withToken(steamcheckerToken(User::factory()->create()))
            ->getJson('/api/steamchecker/me/owned')
            ->assertStatus(409)
            ->assertJsonStructure(['message']);
    });

    test('requests without a token are a 401', function () {
        $this->getJson('/api/steamchecker/me')->assertUnauthorized();
    });

    test('tokens without the steamchecker ability are refused', function () {
        $this->withToken(steamcheckerToken(steamUser(), ['other']))
            ->getJson('/api/steamchecker/me')
            ->assertForbidden();
    });

    test('signing out revokes the token', function () {
        $user = steamUser();
        $token = steamcheckerToken($user);

        $this->withToken($token)->deleteJson('/api/steamchecker/token')->assertNoContent();

        expect($user->tokens()->count())->toBe(0);
        $this->app['auth']->forgetGuards();
        $this->withToken($token)->getJson('/api/steamchecker/me')->assertUnauthorized();
    });

    test('users can see and revoke extension tokens in security settings', function () {
        $user = steamUser();
        steamcheckerToken($user);
        $tokenId = $user->tokens()->value('id');

        $this->actingAs($user)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->get(route('security.edit'))
            ->assertInertia(fn ($page) => $page->where('extensionTokens.0.name', 'SteamChecker (Chrome)'));

        $this->actingAs($user)
            ->delete(route('extension-tokens.destroy', $tokenId))
            ->assertRedirect();

        expect($user->tokens()->count())->toBe(0);
    });

    test('users cannot revoke someone else\'s token', function () {
        $owner = steamUser();
        steamcheckerToken($owner);

        $this->actingAs(User::factory()->create())
            ->delete(route('extension-tokens.destroy', $owner->tokens()->value('id')));

        expect($owner->tokens()->count())->toBe(1);
    });
});
