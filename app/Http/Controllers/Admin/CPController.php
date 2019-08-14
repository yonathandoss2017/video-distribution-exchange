<?php

namespace App\Http\Controllers\Admin;

use App\Models\Property;
use App\Models\PropertyCP;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class CPController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $properties = PropertyCP::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', '%'.$search.'%');
        })
            ->paginate(10);

        return view('admin.pages.cp.index', compact('properties'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $property = PropertyCP::with('organization')->findOrFail($id);
        $orgs = Organization::select('id', 'name')->get();
        $organizations = [];
        foreach ($orgs as $organization) {
            if (Property::ID_FOR_ADMIN == $organization->id) {
                continue;
            }
            $organizations[$organization->id] = $organization->name;
        }

        return view('admin.pages.cp.edit', compact('property', 'organizations'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'organization_id' => 'required',
        ]);

        $property = PropertyCP::with('users')->select('id', 'organization_id')->findOrFail($id);
        if ($request->organization_id != $property->organization_id) {
            $property->users()->detach();
            $property->organization_id = $request->organization_id;
            $property->save();
        }

        Session::flash('success', __('admin/content_provider.updated_property'));

        return redirect()->route('admin.cp.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cp = PropertyCP::findOrFail($id);
        $result = $cp->delete();
        if ($result) {
            if (Property::STATUS_DELETE_PROCESSING == $cp->status) {
                Session::flash('success', __('admin/content_provider.property_delete_in_processing'));
            } else {
                Session::flash('success', __('admin/content_provider.property_deleted_successfully'));
            }
        } else {
            Session::flash('error', __('admin/content_provider.property_delete_failed'));
        }

        return back();
    }

    public function api(Request $request, $id)
    {
        $property = PropertyCP::findOrFail($id);

        return view('admin.pages.cp.api', compact('property'));
    }
}
