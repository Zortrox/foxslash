<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthenticatedSessionSteamController extends Controller
{
    public function show(): RedirectResponse
    {
        return Socialite::driver('steam')->redirect();
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $githubUser = Socialite::driver('github')->user();

        $user = User::updateOrCreate(
            [
                'github_id' => $githubUser->id,
            ],
            [
                'name' => $githubUser->name,
                'email' => $githubUser->email,
                'github_token' => $githubUser->token,
                'github_refresh_token' => $githubUser->refreshToken,
            ],
        );

        Auth::login($user);

        return redirect('/dashboard');

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
