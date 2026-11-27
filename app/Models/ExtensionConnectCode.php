<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * A single-use code from the steamchecker extension's sign-in handoff (PKCE).
 * Only a SHA-256 hash of the code is stored.
 *
 * @property int $id
 * @property int $user_id
 * @property string $code_hash
 * @property string $redirect_uri
 * @property string $code_challenge
 * @property Carbon $expires_at
 */
class ExtensionConnectCode extends Model
{
    public const LIFETIME_SECONDS = 60;

    protected $fillable = ['user_id', 'code_hash', 'redirect_uri', 'code_challenge', 'expires_at'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Issue a code for the user and return the plain value (shown once, in the redirect).
     */
    public static function issue(User $user, string $redirectUri, string $codeChallenge): string
    {
        static::query()->where('expires_at', '<', now())->delete();

        $code = Str::random(64);

        static::query()->create([
            'user_id' => $user->id,
            'code_hash' => static::hash($code),
            'redirect_uri' => $redirectUri,
            'code_challenge' => $codeChallenge,
            'expires_at' => now()->addSeconds(self::LIFETIME_SECONDS),
        ]);

        return $code;
    }

    /**
     * Atomically consume a code: it is deleted whether or not the checks that follow pass,
     * and only one caller can ever receive it.
     */
    public static function consume(string $code): ?self
    {
        $record = static::query()->where('code_hash', static::hash($code))->first();

        if ($record === null || static::query()->whereKey($record->id)->delete() !== 1) {
            return null;
        }

        return $record;
    }

    public static function hash(string $code): string
    {
        return hash('sha256', $code);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * PKCE S256: base64url(sha256(verifier)) must equal the stored challenge.
     */
    public function verifies(string $codeVerifier): bool
    {
        $challenge = rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');

        return hash_equals($this->code_challenge, $challenge);
    }
}
