<?php

namespace App\Providers\SocialiteProviders;

use League\OAuth1\Client\Credentials\ClientCredentials;
use InvalidArgumentException;
use League\OAuth1\Client\Credentials\TokenCredentials;
use SocialiteProviders\Manager\OAuth1\Server;

class SteamForSocialstreamServer extends Server
{
    public function urlTemporaryCredentials() {}

    public function urlAuthorization() {}

    public function urlTokenCredentials() {}

    public function urlUserDetails() {}

    // FIXME: look at Twitter OAuth 1 server
    public function userDetails($data, TokenCredentials $tokenCredentials) {}

    public function userUid($data, TokenCredentials $tokenCredentials) {}

    public function userEmail($data, TokenCredentials $tokenCredentials) {}

    public function userScreenName($data, TokenCredentials $tokenCredentials) {}

    protected function createClientCredentials(array $clientCredentials)
    {
        $_clientCredentials = new ClientCredentials();

        $_clientCredentials->setSecret($clientCredentials['secret']);

        if (isset($clientCredentials['callback_uri'])) {
            $_clientCredentials->setCallbackUri(
                $clientCredentials['callback_uri'],
            );
        }

        return $_clientCredentials;
    }
}
