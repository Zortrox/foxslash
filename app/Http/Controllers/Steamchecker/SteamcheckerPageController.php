<?php

namespace App\Http\Controllers\Steamchecker;

use App\Http\Controllers\Controller;
use App\Services\SteamcheckerChangelog;
use Inertia\Inertia;
use Inertia\Response;

class SteamcheckerPageController extends Controller
{
    /**
     * The extension's home page: what it does, where to get it, and its changelog.
     */
    public function __invoke(SteamcheckerChangelog $changelog): Response
    {
        return Inertia::render('apps/Steamchecker', [
            'repositoryUrl' => $changelog->repositoryUrl(),
            'releases' => Inertia::defer(fn (): ?array => $changelog->releases()),
        ]);
    }
}
