<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Property;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;

class CheckOrganization
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
        if (null != $request->route('property')) {
            $property = $request->route('property');
            if (!is_object($property)) {
                $property = Property::with('organization')->findOrFail($request->route('property'));
            }
            if (!$request->session()->has('organization')
                || $request->session()->get('organization') != $property->organization->id) {
                $request->session()->put('organization', $property->organization->id);
            }
            Organization::setCurrentOrganization($property->organization);
        } else {
            if (!$request->session()->has('organization')) {
                $user = Auth::user();
                if ($user->isSuperAdmin() || $user->isOperator()) {
                    $organization = Organization::all()->except(Organization::ID_FOR_SUPER_ADMIN)->first();
                } else {
                    $organization = $user->organizations()->first();
                }
                if (is_null($organization)) {
                    Auth::logout();

                    return redirect('login')->with('no_user_right', trans('signup.no_user_right'));
                } else {
                    $request->session()->put('organization', $organization->id);
                    Organization::setCurrentOrganization($organization);
                }
            } else {
                Organization::setCurrentOrganization($request->session()->get('organization'));
            }
        }

        return $next($request);
    }
}
