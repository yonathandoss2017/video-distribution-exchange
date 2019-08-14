<?php

namespace App\Http\Controllers\Admin;

use App\Models\Organization;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class OrganizationController extends Controller
{
    public function admin() 
    {
        
        return redirect()->route('admin.organization.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $organizations = Organization::where('id', '>', 0)->withCount('properties')
        ->when($search, function ($query) use ($search) {
            return $query->where('name', 'like', '%'.$search.'%');
        })
        ->paginate(10);

        return view('admin.pages.organization.index', compact('organizations', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.pages.organization.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'country' => 'required',
        ]);

        //create organization
        $organization = new Organization($request->all());
        $organization->save();

        //add organization feature
        $organization->feature()->create([
            'ai_content_review' => 'on' == $request->ai_content_review ? 1 : 0,
        ]);

        Session::flash('success', __('admin/organization.saved_organization'));

        return redirect()->route('admin.organization.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Organization $organization
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Organization $organization)
    {
        $organization->load('feature');

        return view('admin.pages.organization.edit', ['organization' => $organization]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Organization             $organization
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Organization $organization)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'country' => 'required',
        ]);

        $organization->name = $request->name;
        $organization->address = $request->address;
        $organization->country = $request->country;
        $organization->save();

        $organization->feature()->updateOrCreate([
            'organization_id' => $organization->id,
        ], [
            'ai_content_review' => 'on' == $request->ai_content_review ? 1 : 0,
        ]);

        Session::flash('success', __('admin/organization.updated_organization'));

        return redirect()->route('admin.organization.index', [$organization]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Organization $organization)
    {
        $result = $organization->delete();
        $status = $organization->status;
        if ($result) {
            if (Organization::STATUS_DELETE_PROCESSING == $status) {
                Session::flash('success', __('admin/organization.deletion_is_in_processing'));
            } else {
                Session::flash('success', __('admin/organization.deleted_organization'));
            }
        } else {
            Session::flash('error', __('admin/organization.failed_deleted_organization'));
        }

        return back();
    }
}
