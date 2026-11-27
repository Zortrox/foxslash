<?php

namespace App\Http\Controllers\Auth;

use App\Concerns\PasswordValidationRules;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

/**
 * Lets users who signed up through Steam (and so have no password) set one.
 */
class SetPasswordController extends Controller
{
    use PasswordValidationRules;

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_if($user->password !== null, 403);

        $validated = Validator::make($request->only(['password', 'password_confirmation']), [
            'password' => $this->passwordRules(),
        ])->validateWithBag('setPassword');

        $user->forceFill(['password' => $validated['password']])->save();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Your password has been set.')]);

        return back(303);
    }
}
