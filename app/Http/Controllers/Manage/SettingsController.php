<?php

namespace App\Http\Controllers\Manage;

use Validator;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:organization-admin');
    }

    public function index()
    {
        $orgId = Organization::Organization()->id;
        session(['orgId' => $orgId]);

        $organization = Organization::Organization();
        $orgName = Organization::Organization()->name;
        $orgCountry = !empty(Organization::Organization()->country) ? trans('country.'.Organization::Organization()->country) : 'Unspecified';
        $orgAddress = Organization::Organization()->address;

        return view('manage.settings.index', compact('organization', 'orgName', 'orgCountry', 'orgAddress'));
    }

    public function updateSetting(Request $request)
    {
        $orgId = session('orgId');
        $rules = ['address' => 'required|string|max:255|min:5'];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        $org = Organization::find($orgId);
        $org->address = $request->address;
        $org->save();

        session()->flash('success', __('manage/organization/settings.update_success'));

        return back();
    }
}
