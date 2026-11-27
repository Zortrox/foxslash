<?php

use App\Models\User;
use App\Services\SteamcheckerChangelog;
use Illuminate\Support\Facades\Http;

function fakeGithubReleases(): void
{
    Http::fake([
        'api.github.com/repos/Zortrox/steamchecker/releases*' => Http::response([
            [
                'tag_name' => 'v1.1.0',
                'name' => 'v1.1.0',
                'draft' => false,
                'prerelease' => false,
                'published_at' => '2026-11-27T03:39:32Z',
                'html_url' => 'https://github.com/Zortrox/steamchecker/releases/tag/v1.1.0',
                'body' => "## [1.1.0](https://github.com/Zortrox/steamchecker/compare/v1.0.1...v1.1.0) (2026-11-27)\n\n### Features\n\n* support Humble's new site <script>alert(1)</script> [link](javascript:alert(1))\n",
            ],
            [
                'tag_name' => 'v1.0.0-beta.1',
                'name' => 'v1.0.0-beta.1',
                'draft' => false,
                'prerelease' => true,
                'published_at' => '2024-06-18T03:10:23Z',
                'html_url' => 'https://github.com/Zortrox/steamchecker/releases/tag/v1.0.0-beta.1',
                'body' => 'beta',
            ],
        ]),
    ]);
}

test('the steamchecker page lists stable releases from github', function () {
    fakeGithubReleases();

    $this->get('/apps/steamchecker/')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('apps/Steamchecker')
            ->where('repositoryUrl', 'https://github.com/Zortrox/steamchecker')
            ->missing('releases')
            ->loadDeferredProps(fn ($reload) => $reload
                ->has('releases', 1)
                ->where('releases.0.version', '1.1.0')
                ->where('releases.0.published_at', '2026-11-27')
                ->where('releases.0.notes', fn (string $notes): bool => str_contains($notes, '<h3>Features</h3>')
                    && ! str_contains($notes, 'compare/v1.0.1')
                    && ! str_contains($notes, '<script>')
                    && ! str_contains($notes, 'javascript:'))));
});

test('the changelog is cached between requests', function () {
    fakeGithubReleases();
    $changelog = app(SteamcheckerChangelog::class);

    $changelog->releases();
    $changelog->releases();

    Http::assertSentCount(1);
});

test('the changelog is null when github is unavailable and the failure is cached', function () {
    Http::fake(['api.github.com/*' => Http::response(status: 503)]);
    $changelog = app(SteamcheckerChangelog::class);

    expect($changelog->releases())->toBeNull()
        ->and($changelog->releases())->toBeNull();

    Http::assertSentCount(1);
});

test('the steamchecker privacy policy is public', function () {
    $this->get('/apps/steamchecker/privacy-policy/')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('apps/SteamcheckerPrivacy'));
});

test('deleting an account revokes its extension tokens', function () {
    $user = User::factory()->create();
    $user->createToken('steamchecker', ['steamchecker']);

    $this->actingAs($user)
        ->delete(route('profile.destroy'), ['password' => 'password'])
        ->assertRedirect('/');

    $this->assertDatabaseCount('personal_access_tokens', 0);
});
