<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\RequirePassword;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Password confirmation that lets through users who have no password yet
 * (accounts created through Steam), since there is nothing for them to confirm.
 */
class RequirePasswordWhenSet extends RequirePassword
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle($request, Closure $next, $redirectToRoute = null, $passwordTimeoutSeconds = null): Response
    {
        if ($request->user()?->password === null) {
            return $next($request);
        }

        return parent::handle($request, $next, $redirectToRoute, $passwordTimeoutSeconds);
    }
}
