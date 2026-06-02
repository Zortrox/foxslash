<?php

namespace App\Actions\Socialstream;

use JoelButcher\Socialstream\Contracts\ResolvesSocialiteUsers;
use JoelButcher\Socialstream\Socialstream;
use Laravel\Socialite\Contracts\User;
use Laravel\Socialite\Facades\Socialite;

class ResolveSocialiteUser implements ResolvesSocialiteUsers
{
    /**
     * Cache of resolved users per provider within the current request.
     *
     * @var array<string, User>
     */
    private array $resolved = [];

    /**
     * Resolve the user for a given provider.
     */
    public function resolve(string $provider): User
    {
        if (array_key_exists($provider, $this->resolved)) {
            return $this->resolved[$provider];
        }

        $user = Socialite::driver($provider)->user();

        if (Socialstream::generatesMissingEmails()) {
            $user->email =
                $user->getEmail() ??
                "{$user->id}@{$provider}." . config('app.domain');
        }

        return $this->resolved[$provider] = $user;
    }
}
