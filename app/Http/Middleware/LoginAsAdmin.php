<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class LoginAsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param string|null              $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::guard()->check()) {
            Auth::guard()->attempt([
                'email' => 'admin@cnc.com',
                'password' => 'ABCabc01',
            ], true);
        }

        return $next($request);
    }
}
