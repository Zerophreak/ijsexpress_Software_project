<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\AuthToken;
use Illuminate\Http\Request;

class HasValidAuthToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $token = AuthToken::query()
            ->where('token', $request->get('key'))
            ->where('revoked', false)
            ->first();

        if (!$token) {
            return response(['message' => 'Unauthorized.'], 401);
        }

        \Sentry\configureScope(function (\Sentry\State\Scope $scope) use ($token) :void {
            $scope->setTag('auth_token', $token->slug);
        });

        return $next($request);
    }
}
