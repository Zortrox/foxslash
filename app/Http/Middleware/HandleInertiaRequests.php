<?php

namespace App\Http\Middleware;

use App\Models\ConnectedAccount;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $user
                    ? [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'avatar' => $user->avatar,
                        'email_verified_at' => $user->email_verified_at,
                        'two_factor_enabled' => $user->hasEnabledTwoFactorAuthentication(),
                        'roles' => $user->getRoleNames(),
                        'permissions' => $user->getAllPermissions()->pluck('name'),
                        'created_at' => $user->created_at,
                        'updated_at' => $user->updated_at,
                    ]
                    : null,
            ],
            'oauth' => fn (): array => [
                'providers' => [['id' => 'steam', 'name' => 'Steam']],
                'hasPassword' => $user !== null && $user->password !== null,
                'connectedAccounts' => $user
                    ? $user->connectedAccounts->map(fn (ConnectedAccount $account): array => $account->toSharedArray())->values()
                    : [],
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
