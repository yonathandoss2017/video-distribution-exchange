<?php

namespace App\Http\Controllers\Manage;

use Validator;
use App\Models\Property;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:organization-admin')->except(['selectProperty', 'selectOrganization']);
    }

    public function selectProperty()
    {
        if (Auth::user()->isSuperAdmin() || Auth::user()->isAdmin()) {
            $properties = Organization::Organization()->properties;
        } elseif (Auth::user()->isOperator()) {
            $properties = Organization::Organization()->properties()->where('type', Property::TYPE_CP)->get();
        } else {
            $organization_id = Organization::Organization()->id;
            $properties = Property::join('role_user', 'role_user.property_id', 'properties.id')
                ->where('role_user.user_id', Auth::user()->id)
                ->where('role_user.organization_id', $organization_id)
                ->select('properties.*')->get();
        }

        $routes = [
            Property::TYPE_SP_PLUS => 'manage.cp.settings',
            Property::TYPE_CP => 'manage.cp.playlists.index',
            Property::TYPE_SP => 'manage.sp.playlists.index',
        ];
        $types = [
            'sp' => 'SP',
            'cp' => 'CP',
        ];

        if ($properties->count() > 0) {
            return view('manage.property.select', compact('properties', 'routes', 'types'));
        } else {
            return view('manage.property.empty');
        }
    }

    public function addProperty()
    {
        $org = Organization::Organization();

        return view('manage.property.add', compact('org'));
    }

    public function storeProperty(Request $request)
    {
        $rules = $this->rules();
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        $org = Organization::Organization();
        $property = new Property();
        $property->organization_id = $org->id;
        $property->name = $request->property_name;
        $property->type = $request->property_type;
        $property->save();

        session()->flash('success', __('manage/organization/property.property_create_success'));

        return redirect()->route('manage.property.select');
    }

    public function rules()
    {
        $rules['property_name'] = 'required|string|max:150|min:1';
        $rules['property_type'] = 'required|in:cp,sp';

        return $rules;
    }

    public function users()
    {
        return view('manage.test');
    }

    /**
     * Select specific organization to manage.
     *
     * @param Request $request
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function selectOrganization($id, Request $request)
    {
        if (Organization::ID_FOR_SUPER_ADMIN == $id) {
            return redirect()->route('manage.property.select');
        }

        $user = Auth::user();
        if ($user->isSuperAdmin() || $user->isOperator()) {
            $organization = Organization::findOrFail($id);
        } else {
            $organization = $user->organizations()->where('id', $id)->first();
        }

        if (is_null($organization)) {
            return redirect()->route('manage.property.select');
        }

        $request->session()->put('organization', $id);

        /* When user changes to a diff org, the SP (for hotspot editing) also must change */

        //Current policy: Just take whatever first SP we can assign.
        if ($user->isSuperAdmin() || $user->isAdmin()) {
            $sps = $organization->service_providers();
            if ($sps->first()) {
                $request->session()->put('sp', $sps->first()->id);
            } else {
                $request->session()->put('sp', \App\Models\Property::ID_INVALID_SP);
            }
        } else {
            $sps = $organization->service_providers();
            foreach ($sps as $sp) {
                if ($user->isManager($sp)) {
                    $request->session()->put('sp', $sp->id);

                    return redirect('/admin/');
                }
            }
            $request->session()->put('sp', \App\Models\Property::ID_INVALID_SP);
        }

        return redirect()->route('manage.property.select');
    }
}
