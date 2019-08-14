<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\PropertySP;
use App\Models\Organization;

class CheckServiceProvider
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
        $property = $request->route('property');
        if (is_null($property)) {
            abort(403);
        }
        if (!is_object($property)) {
            $property = PropertySP::findOrFail($property);
        }
        if ($property->organization_id != optional(Organization::Organization())->id) {
            abort(403);
        }

        return $next($request);
    }
}
