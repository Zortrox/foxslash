<?php

use App\Jobs\SteamFetchGames;
use App\Models\SteamGame;
use App\Models\SteamGameAlias;
use App\Services\SteamAppListFile;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Sleep;

const STEAM_ID = '76561197960287930';

beforeEach(function () {
    Http::preventStrayRequests();
    Storage::fake('local');
    config(['services.steam.api_key' => 'test-steam-key']);
});

function fakeSteamApi(array $overrides = []): void
{
    Http::fake([
        'api.steampowered.com/ISteamUser/ResolveVanityURL/*' => Http::response(['response' => ['success' => 1, 'steamid' => STEAM_ID]]),
        'api.steampowered.com/IPlayerService/GetOwnedGames/*' => Http::response(['response' => [
            'game_count' => 2,
            'games' => [
                ['appid' => 10, 'name' => 'Counter-Strike', 'playtime_forever' => 5],
                ['appid' => 220, 'name' => 'Half-Life 2', 'playtime_forever' => 0],
            ],
        ]]),
        'api.steampowered.com/IWishlistService/GetWishlist/*' => Http::response(['response' => ['items' => [
            ['appid' => 400, 'priority' => 2, 'date_added' => 1],
            ['appid' => 620, 'priority' => 1, 'date_added' => 2],
        ]]]),
        ...$overrides,
    ]);
}

describe('api/steamchecker', function () {
    test('owned games by steamid64 skip vanity resolution', function () {
        fakeSteamApi();

        $this->getJson('/api/steamchecker/profiles/'.STEAM_ID.'/owned')
            ->assertOk()
            ->assertJsonPath('steam_id', STEAM_ID)
            ->assertJsonPath('game_count', 2)
            ->assertJsonPath('games.1.name', 'Half-Life 2');

        Http::assertNotSent(fn (Request $request) => str_contains($request->url(), 'ResolveVanityURL'));
        Http::assertSent(fn (Request $request) => str_contains($request->url(), 'GetOwnedGames')
            && $request['steamid'] === STEAM_ID
            && (int) $request['include_appinfo'] === 1
            && str_contains($request->url(), 'key=test-steam-key'));
    });

    test('owned games resolve a custom profile url name', function () {
        fakeSteamApi();

        $this->getJson('/api/steamchecker/profiles/gabelogannewell/owned')
            ->assertOk()
            ->assertJsonPath('steam_id', STEAM_ID);

        Http::assertSent(fn (Request $request) => str_contains($request->url(), 'ResolveVanityURL')
            && $request['vanityurl'] === 'gabelogannewell');
    });

    test('owned games are cached between requests', function () {
        fakeSteamApi();

        $this->getJson('/api/steamchecker/profiles/'.STEAM_ID.'/owned')->assertOk();
        $this->getJson('/api/steamchecker/profiles/'.STEAM_ID.'/owned')->assertOk();

        Http::assertSentCount(1);
    });

    test('an unknown profile name is a 404 with a message', function () {
        fakeSteamApi([
            'api.steampowered.com/ISteamUser/ResolveVanityURL/*' => Http::response(['response' => ['success' => 42, 'message' => 'No match']]),
        ]);

        $this->getJson('/api/steamchecker/profiles/nobody-here/owned')
            ->assertNotFound()
            ->assertJsonPath('message', 'No Steam profile found for "nobody-here".');
    });

    test('a private library is a 404 with a message', function () {
        fakeSteamApi([
            'api.steampowered.com/IPlayerService/GetOwnedGames/*' => Http::response(['response' => []]),
        ]);

        $this->getJson('/api/steamchecker/profiles/'.STEAM_ID.'/owned')
            ->assertNotFound()
            ->assertJsonStructure(['message']);
    });

    test('a steam outage is a 502', function () {
        fakeSteamApi([
            'api.steampowered.com/IPlayerService/GetOwnedGames/*' => Http::response('', 503),
        ]);

        $this->getJson('/api/steamchecker/profiles/'.STEAM_ID.'/owned')->assertStatus(502);
    });

    test('wishlist returns appids in priority order', function () {
        fakeSteamApi();

        $this->getJson('/api/steamchecker/profiles/'.STEAM_ID.'/wishlist')
            ->assertOk()
            ->assertExactJson(['steam_id' => STEAM_ID, 'appids' => [620, 400]]);
    });

    test('aliases are grouped by appid and can be filtered by name', function () {
        SteamGame::factory()->create(['appid' => 72850, 'name' => 'The Elder Scrolls V: Skyrim']);
        SteamGame::factory()->create(['appid' => 32370, 'name' => 'Star Wars: KOTOR']);
        SteamGameAlias::create(['appid' => 72850, 'alias' => 'tesv']);
        SteamGameAlias::create(['appid' => 32370, 'alias' => 'kotor']);
        SteamGameAlias::create(['appid' => 32370, 'alias' => 'starwarskotor']);

        $this->getJson('/api/steamchecker/aliases')
            ->assertOk()
            ->assertExactJson(['32370' => ['kotor', 'starwarskotor'], '72850' => ['tesv']]);

        $this->getJson('/api/steamchecker/aliases?names[]=tesv')
            ->assertOk()
            ->assertExactJson(['72850' => ['tesv']]);
    });

    test('the app list is served from the synced games', function () {
        SteamGame::factory()->create(['appid' => 10, 'name' => 'Counter-Strike™']);
        SteamGame::factory()->create(['appid' => 220, 'name' => 'Half-Life 2']);

        $this->get('/api/steamchecker/apps')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/json');

        expect(json_decode(file_get_contents(app(SteamAppListFile::class)->path()), true))
            ->toBe(['apps' => [['appid' => 10, 'name' => 'Counter-Strike™'], ['appid' => 220, 'name' => 'Half-Life 2']]]);
    });

    test('responses allow any origin for the extension content script', function () {
        fakeSteamApi();

        $this->getJson('/api/steamchecker/profiles/'.STEAM_ID.'/wishlist', ['Origin' => 'https://www.humblebundle.com'])
            ->assertHeader('Access-Control-Allow-Origin', '*');
    });
});

describe('legacy apps/steamchecker', function () {
    test('owned keeps the original response shape', function () {
        fakeSteamApi();

        $this->getJson('/apps/steamchecker/owned/?steamid=gabelogannewell')
            ->assertOk()
            ->assertJsonPath('response.game_count', 2)
            ->assertJsonPath('response.games.0.appid', 10)
            ->assertJsonPath('aliases', []);
    });

    test('owned treats the steamid as an id64 when told to', function () {
        fakeSteamApi();

        $this->getJson('/apps/steamchecker/owned/?steamid='.STEAM_ID.'&id64=1')->assertOk();

        Http::assertNotSent(fn (Request $request) => str_contains($request->url(), 'ResolveVanityURL'));
    });

    test('owned errors come back as a message', function () {
        $this->getJson('/apps/steamchecker/owned/')->assertJsonPath('message', 'No steam profile argument');
    });

    test('wishlist keeps the original string appid shape', function () {
        fakeSteamApi();

        $this->getJson('/apps/steamchecker/wishlist/?id='.STEAM_ID.'&type=id64')
            ->assertOk()
            ->assertExactJson(['wishlist' => ['620', '400']]);
    });

    test('an empty wishlist returns the original message', function () {
        fakeSteamApi([
            'api.steampowered.com/IWishlistService/GetWishlist/*' => Http::response(['response' => []]),
        ]);

        $this->getJson('/apps/steamchecker/wishlist/?id='.STEAM_ID.'&type=id64')
            ->assertJsonPath('message', 'No wishlist items');
    });

    test('aliases accept the raw json post the extension sends', function () {
        SteamGame::factory()->create(['appid' => 72850]);
        SteamGameAlias::create(['appid' => 72850, 'alias' => 'tesv']);

        $this->call('POST', '/apps/steamchecker/aliases/', content: json_encode(['type' => 'all']))
            ->assertOk()
            ->assertExactJson(['72850' => ['tesv']]);

        $this->call('POST', '/apps/steamchecker/aliases/', content: json_encode(['type' => 'some', 'names' => ['nope']]))
            ->assertOk()
            ->assertExactJson([]);
    });

    test('config v2 serves the page config with cors and a short cache', function (string $url) {
        $this->getJson($url, ['Origin' => 'https://www.humblebundle.com'])
            ->assertOk()
            ->assertJsonPath('schema_version', 2)
            ->assertJsonStructure(['revision', 'pages' => [['id', 'path', 'targets']]])
            ->assertHeader('Access-Control-Allow-Origin', '*')
            ->assertHeader('Cache-Control', 'max-age=300, public');
    })->with(['/api/steamchecker/config/v2', '/apps/steamchecker/config/v2/']);

    test('appList.json wraps the app list in applist', function () {
        SteamGame::factory()->create(['appid' => 10, 'name' => 'Counter-Strike']);

        $response = $this->get('/apps/steamchecker/appList.json')->assertOk();

        expect(json_decode($response->streamedContent(), true))
            ->toBe(['applist' => ['apps' => [['appid' => 10, 'name' => 'Counter-Strike']]]]);
    });
});

test('the app sync job pages through steam, upserts games, and rebuilds the app list', function () {
    Http::fake([
        'api.steampowered.com/IStoreService/GetAppList/*' => Http::sequence()
            ->push(['response' => ['apps' => [['appid' => 10, 'name' => 'Counter-Strike']], 'have_more_results' => true, 'last_appid' => 10]])
            ->push(['response' => ['apps' => [['appid' => 220, 'name' => 'Half-Life 2']]]]),
    ]);
    SteamGame::factory()->create(['appid' => 10, 'name' => 'Old Name']);

    (new SteamFetchGames)->handle();

    expect(SteamGame::orderBy('appid')->pluck('name', 'appid')->all())
        ->toBe([10 => 'Counter-Strike', 220 => 'Half-Life 2']);
    Http::assertSent(fn (Request $request) => str_contains($request->url(), 'GetAppList') && (int) $request['last_appid'] === 10);
    Storage::disk('local')->assertExists(SteamAppListFile::PATH);
});

test('the app sync job stops at once and names the status when steam rejects the key', function () {
    Sleep::fake();
    Http::fake(['api.steampowered.com/IStoreService/GetAppList/*' => Http::response('Forbidden', 403)]);

    expect(fn () => (new SteamFetchGames)->handle())
        ->toThrow(RuntimeException::class, 'Failed to fetch the Steam app list: HTTP 403 (check STEAM_API_KEY)');

    Http::assertSentCount(1);
    Sleep::assertNeverSlept();
});

test('the app sync job can outlast a default worker without being handed out twice', function () {
    $job = new SteamFetchGames;

    expect($job->timeout)->toBeGreaterThan(60)
        ->and(config('queue.connections.database.retry_after'))->toBeGreaterThan($job->timeout);
});
