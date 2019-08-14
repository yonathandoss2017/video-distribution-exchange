<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cookie;

/**
 * We will update the last_active record for user
 * every 30 minutes once.
 */
class UpdateUserLastActive
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $auth = $request->user();
        if ($request->ajax() || !$auth) {
            return $next($request);
        }

        $cookie = $request->cookie('last_active');
        if (!$cookie) {
            $this->createCookie();
            $this->updateData($auth);
        }

        return $next($request);
    }

    private function updateData($user, $time = null)
    {
        $user->timestamps = false;
        $user->last_active = $time ?: now();

        return $user->save();
    }

    private function createCookie()
    {
        Cookie::queue('last_active', now(), 30);
    }
}
