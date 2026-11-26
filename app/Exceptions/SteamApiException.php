<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * A Steam lookup failed in a way the API caller should hear about.
 */
class SteamApiException extends RuntimeException
{
    public function __construct(string $message, public readonly int $status = 502)
    {
        parent::__construct($message);
    }

    public static function profileNotFound(string $profile): self
    {
        return new self("No Steam profile found for \"{$profile}\".", 404);
    }

    public static function profileNotPublic(string $section): self
    {
        return new self("The Steam profile's {$section} is private or empty.", 404);
    }

    public static function unavailable(): self
    {
        return new self('Steam did not respond. Please try again later.', 502);
    }
}
