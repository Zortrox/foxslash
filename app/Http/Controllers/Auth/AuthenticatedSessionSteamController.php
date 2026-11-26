<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\ResolveUserFromSteam;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;
use Throwable;

class AuthenticatedSessionSteamController extends Controller
{
    /**
     * Send the user to Steam's OpenID sign in page.
     */
    public function redirect(): SymfonyRedirectResponse
    {
        return Socialite::driver(ResolveUserFromSteam::PROVIDER)->redirect();
    }

    /**
     * Handle the return from Steam: link the account when signed in, otherwise sign in or register.
     */
    public function callback(Request $request, ResolveUserFromSteam $resolver): RedirectResponse
    {
        try {
            $steamUser = Socialite::driver(ResolveUserFromSteam::PROVIDER)->user();
        } catch (Throwable $exception) {
            report($exception);

            return $this->failed($request, __('Unable to sign in with Steam. Please try again.'));
        }

        if ($currentUser = $request->user()) {
            try {
                $resolver->link($currentUser, $steamUser);
            } catch (ValidationException $exception) {
                return redirect()->route('profile.edit')->withErrors($exception->errors());
            }

            Inertia::flash('toast', ['type' => 'success', 'message' => __('Your Steam account has been connected.')]);

            return redirect()->intended(route('profile.edit'));
        }

        $user = $resolver->findOrCreate($steamUser);

        if (Features::enabled(Features::twoFactorAuthentication()) && $user->hasEnabledTwoFactorAuthentication()) {
            $request->session()->put([
                'login.id' => $user->getKey(),
                'login.remember' => true,
            ]);

            return redirect()->route('two-factor.login');
        }

        Auth::login($user, remember: true);

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    private function failed(Request $request, string $message): RedirectResponse
    {
        $route = $request->user() ? 'profile.edit' : 'login';

        return redirect()->route($route)->withErrors(['oauth' => $message]);
    }
}
