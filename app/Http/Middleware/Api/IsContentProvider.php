<?php

namespace App\Http\Middleware\Api;

use Closure;
use App\Models\PropertyCP;

class IsContentProvider
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $checkUseMasterKey = 'false')
    {
        $checkUseMasterKey = ('true' === $checkUseMasterKey);
        if ($checkUseMasterKey) {
            //From IsUseMasterKey middleware
            $useMasterKey = $request->get('use_master_key');
        } else {
            $useMasterKey = false;
        }

        if (!$useMasterKey) {
            $key = $request->query('key');
            if (!isset($key)) {
                $key = $request->header('x-IVX-API-KEY');
            }

            $token = $request->query('token');
            if (!isset($token)) {
                $token = $request->header('x-IVX-API-TOKEN');
            }

            if (empty($key) || empty($token)) {
                abort(401);
            }

            $cp = PropertyCP::where('api_key', '=', $key)
                ->where('api_token', '=', $token)
                ->first();

            if (empty($cp)) {
                abort(403);
            }

            $request->attributes->add(['cp' => $cp]);
        }

        return $next($request);
    }
}
