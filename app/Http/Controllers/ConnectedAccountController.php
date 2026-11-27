<?php

namespace App\Http\Controllers;

use App\Models\ConnectedAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class ConnectedAccountController extends Controller
{
    /**
     * Disconnect one of the user's connected accounts after confirming their password.
     */
    public function destroy(Request $request, ConnectedAccount $connectedAccount): RedirectResponse
    {
        $user = $request->user();

        abort_unless($connectedAccount->user_id === $user->id, 404);

        if ($user->password === null || ! Hash::check((string) $request->input('password'), $user->password)) {
            throw ValidationException::withMessages([
                'password' => __('The password is incorrect.'),
            ]);
        }

        $connectedAccount->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Steam account disconnected.')]);

        return back(303);
    }
}
