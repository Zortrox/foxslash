<?php

namespace App\Http\Controllers\Steamchecker;

use App\Actions\Auth\ResolveUserFromSteam;
use App\Http\Controllers\Controller;
use App\Models\ExtensionConnectCode;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

/**
 * Exchanges a sign-in code for a Sanctum token with only the "steamchecker" ability,
 * and revokes that token on sign out.
 */
class ExtensionTokenController extends Controller
{
    public const ABILITY = 'steamchecker';

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => ['required', 'string', 'max:255'],
            'code_verifier' => ['required', 'string', 'regex:/^[A-Za-z0-9\-._~]{43,128}$/'],
            'redirect_uri' => ['required', 'string', 'max:255'],
            'device_name' => ['required', 'string', 'max:100'],
        ]);

        if ($validator->fails()) {
            return $this->failed('The sign-in request was incomplete.');
        }

        $input = $validator->validated();
        $connectCode = ExtensionConnectCode::consume($input['code']);

        if ($connectCode === null || $connectCode->isExpired()) {
            return $this->failed('This sign-in code has expired or was already used. Please sign in again.');
        }

        if (! hash_equals($connectCode->redirect_uri, $input['redirect_uri']) || ! $connectCode->verifies($input['code_verifier'])) {
            return $this->failed('This sign-in code does not match this extension. Please sign in again.');
        }

        $user = $connectCode->user;
        $token = $user->createToken($input['device_name'], [self::ABILITY]);

        return response()->json([
            'token' => $token->plainTextToken,
            'account' => static::account($user),
        ]);
    }

    /**
     * Sign out: revoke the token that made this request.
     */
    public function destroy(Request $request): Response
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }

    /**
     * @return array{name: string, steam_id: string|null, steam_name: string|null, avatar_url: string|null}
     */
    public static function account(User $user): array
    {
        $steam = $user->connectedAccounts()->where('provider', ResolveUserFromSteam::PROVIDER)->first();

        return [
            'name' => $user->name,
            'steam_id' => $steam?->provider_id,
            'steam_name' => $steam?->nickname ?? $steam?->name,
            'avatar_url' => $steam?->avatar_path,
        ];
    }

    private function failed(string $message): JsonResponse
    {
        return response()->json(['message' => $message], 400);
    }
}
