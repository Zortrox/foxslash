<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * The steamchecker extension's release notes, read from its GitHub releases.
 */
class SteamcheckerChangelog
{
    public const CACHE_KEY = 'steamchecker.changelog';

    /** How long a fetched changelog is kept, in seconds. */
    public const CACHE_SECONDS = 3600;

    /** How long a failed fetch is remembered before GitHub is asked again, in seconds. */
    public const FAILURE_CACHE_SECONDS = 300;

    /**
     * Published, non-prerelease releases, newest first, or null when GitHub can't be reached.
     *
     * @return list<array{version: string, published_at: string, url: string, notes: string}>|null
     */
    public function releases(): ?array
    {
        $cached = Cache::get(self::CACHE_KEY);

        if (is_array($cached) && array_key_exists('releases', $cached)) {
            return $cached['releases'];
        }

        $releases = $this->fetch();

        Cache::put(
            self::CACHE_KEY,
            ['releases' => $releases],
            $releases === null ? self::FAILURE_CACHE_SECONDS : self::CACHE_SECONDS,
        );

        return $releases;
    }

    public function repositoryUrl(): string
    {
        return 'https://github.com/'.config('services.steamchecker.repository');
    }

    /**
     * @return list<array{version: string, published_at: string, url: string, notes: string}>|null
     */
    private function fetch(): ?array
    {
        try {
            $response = Http::acceptJson()
                ->withHeaders(['X-GitHub-Api-Version' => '2022-11-28'])
                ->timeout(10)
                ->get('https://api.github.com/repos/'.config('services.steamchecker.repository').'/releases', ['per_page' => 50]);
        } catch (ConnectionException) {
            return null;
        }

        if (! $response->successful() || ! is_array($response->json())) {
            return null;
        }

        return collect($response->json())
            ->reject(fn (array $release): bool => ($release['draft'] ?? false) || ($release['prerelease'] ?? false))
            ->map(fn (array $release): array => [
                'version' => ltrim((string) ($release['name'] ?: $release['tag_name']), 'v'),
                'published_at' => Carbon::parse($release['published_at'])->toDateString(),
                'url' => (string) $release['html_url'],
                'notes' => $this->renderNotes((string) ($release['body'] ?? '')),
            ])
            ->values()
            ->all();
    }

    /**
     * Render release notes to HTML. semantic-release opens each body with a
     * "## [version](compare link) (date)" heading the page already shows, so it is dropped.
     */
    private function renderNotes(string $body): string
    {
        $body = preg_replace('/\A\s*##\s[^\n]*\n/', '', $body) ?? $body;

        return Str::markdown(trim($body), [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }
}
