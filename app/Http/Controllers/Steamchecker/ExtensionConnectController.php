<?php

namespace App\Http\Controllers\Steamchecker;

use App\Actions\Auth\ResolveUserFromSteam;
use App\Http\Controllers\Controller;
use App\Models\ConnectedAccount;
use App\Models\ExtensionConnectCode;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * The browser side of the steamchecker extension sign-in: consent, then a
 * single-use code handed to the extension's redirect URI (PKCE S256).
 * Contract: browser-extensions/steamchecker/docs/sign-in.md.
 */
class ExtensionConnectController extends Controller
{
    /**
     * Show the consent page. The redirect URI is checked before anything else,
     * so an untrusted URI never reaches the login page or receives a redirect.
     */
    public function show(Request $request): Response|RedirectResponse
    {
        $params = $this->validatedParams($request);

        if (! $request->user()) {
            return redirect()->guest(route('login'));
        }

        $steamAccount = $this->steamAccount($request->user());

        if ($steamAccount === null) {
            // Linking Steam sends the user back here (AuthenticatedSessionSteamController uses intended()).
            $request->session()->put('url.intended', $request->fullUrl());
        }

        return Inertia::render('auth/ExtensionConnect', [
            ...$params,
            'csrfToken' => csrf_token(),
            'steamLinked' => $steamAccount !== null,
            'steamName' => $steamAccount?->nickname ?? $steamAccount?->name,
            'steamAvatar' => $steamAccount?->avatar_path,
            'accountName' => $request->user()->name,
        ]);
    }

    /**
     * Allow or cancel. Always answers with a 302 to the extension's redirect URI.
     */
    public function store(Request $request): RedirectResponse
    {
        $params = $this->validatedParams($request);
        $decision = $request->validate(['decision' => ['required', 'in:allow,cancel']])['decision'];

        if ($decision === 'cancel') {
            return redirect()->away($this->callbackUrl($params['redirect_uri'], [
                'error' => 'access_denied',
                'state' => $params['state'],
            ]));
        }

        if ($this->steamAccount($request->user()) === null) {
            return redirect()->route('extension.connect', $params);
        }

        $code = ExtensionConnectCode::issue($request->user(), $params['redirect_uri'], $params['code_challenge']);

        return redirect()->away($this->callbackUrl($params['redirect_uri'], [
            'code' => $code,
            'state' => $params['state'],
        ]));
    }

    /**
     * @return array{redirect_uri: string, state: string, code_challenge: string, code_challenge_method: string}
     */
    private function validatedParams(Request $request): array
    {
        $redirectUri = $request->input('redirect_uri');

        if (! is_string($redirectUri) || ! in_array($redirectUri, config('services.steamchecker.redirect_uris'), true)) {
            throw new HttpException(400, 'This sign-in link is not from a recognised SteamChecker extension.');
        }

        $state = $request->input('state');
        $challenge = $request->input('code_challenge');

        if (! is_string($state) || $state === '' || strlen($state) > 255
            || ! is_string($challenge) || ! preg_match('/^[A-Za-z0-9_-]{43}$/', $challenge)
            || $request->input('code_challenge_method') !== 'S256') {
            throw new HttpException(400, 'This sign-in link is incomplete. Please try signing in from SteamChecker again.');
        }

        return [
            'redirect_uri' => $redirectUri,
            'state' => $state,
            'code_challenge' => $challenge,
            'code_challenge_method' => 'S256',
        ];
    }

    private function steamAccount(User $user): ?ConnectedAccount
    {
        return $user->connectedAccounts()->where('provider', ResolveUserFromSteam::PROVIDER)->first();
    }

    /**
     * @param  array<string, string>  $query
     */
    private function callbackUrl(string $redirectUri, array $query): string
    {
        return $redirectUri.(str_contains($redirectUri, '?') ? '&' : '?').http_build_query($query);
    }
}
