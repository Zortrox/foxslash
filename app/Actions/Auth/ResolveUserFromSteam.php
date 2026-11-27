<?php

namespace App\Actions\Auth;

use App\Models\ConnectedAccount;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Contracts\User as SteamUser;

/**
 * Finds, links or creates the local user for a Steam sign in.
 */
class ResolveUserFromSteam
{
    public const PROVIDER = 'steam';

    /**
     * Link the Steam account to the given user.
     *
     * @throws ValidationException when the Steam account already belongs to another user
     */
    public function link(User $user, SteamUser $steamUser): ConnectedAccount
    {
        $account = $this->findAccount($steamUser);

        if ($account && $account->user_id !== $user->id) {
            throw ValidationException::withMessages([
                'oauth' => __('This Steam account is already linked to another user.'),
            ]);
        }

        return $this->saveAccount($user, $steamUser, $account);
    }

    /**
     * Return the user that owns the Steam account, creating one on first sign in.
     */
    public function findOrCreate(SteamUser $steamUser): User
    {
        $account = $this->findAccount($steamUser);

        if ($account) {
            $this->saveAccount($account->user, $steamUser, $account);

            return $account->user;
        }

        return DB::transaction(function () use ($steamUser): User {
            $user = User::create([
                'name' => $steamUser->getName() ?: $steamUser->getNickname(),
                'email' => $steamUser->getEmail() ?: $this->placeholderEmail($steamUser),
            ]);

            $user->markEmailAsVerified();

            $this->saveAccount($user, $steamUser);

            return $user;
        });
    }

    /**
     * Steam OpenID never shares an email address, so new users get a unique placeholder
     * on the site's own domain, e.g. 76561197960287930@steam.foxslash.com.
     */
    public function placeholderEmail(SteamUser $steamUser): string
    {
        $domain = parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'localhost';

        return $steamUser->getId().'@steam.'.$domain;
    }

    private function findAccount(SteamUser $steamUser): ?ConnectedAccount
    {
        return ConnectedAccount::query()
            ->with('user')
            ->where('provider', self::PROVIDER)
            ->where('provider_id', (string) $steamUser->getId())
            ->first();
    }

    private function saveAccount(User $user, SteamUser $steamUser, ?ConnectedAccount $account = null): ConnectedAccount
    {
        $account ??= new ConnectedAccount([
            'provider' => self::PROVIDER,
            'provider_id' => (string) $steamUser->getId(),
        ]);

        $account->forceFill([
            'user_id' => $user->id,
            'name' => $steamUser->getName(),
            'nickname' => $steamUser->getNickname(),
            'email' => $steamUser->getEmail(),
            'avatar_path' => $steamUser->getAvatar(),
            'token' => $steamUser->token ?? null,
        ])->save();

        return $account;
    }
}
