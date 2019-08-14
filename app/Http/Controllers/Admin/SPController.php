<?php

namespace App\Http\Controllers\Admin;

use App\Models\Playlist;
use App\Models\PropertySP;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class SPController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $properties = PropertySP::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', '%'.$search.'%');
        })
            ->paginate(10);

        return view('admin.pages.sp.index', compact('properties'));
    }

    /**
     * Display api list of property.
     *
     * @param int $id property id
     *
     * @return \Illuminate\Http\Response
     *
     * @author stone
     * @datetime 2018-12-12 15:00:00
     */
    public function api(PropertySP $property)
    {
        $playlist = Playlist::select('id')->whitelistedForSP($property)->first();
        $entry = null;
        if ($playlist) {
            $entry = $playlist->entries()->first();
        }

        return view('admin.pages.sp.api', [
            'property' => $property,
            'playlist' => $playlist,
            'entry' => $entry,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $propertySP
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sp = PropertySP::findOrFail($id);
        if ($sp->delete()) {
            Session::flash('success', __('admin/service_provider.property_deleted_successfully'));
        } else {
            Session::flash('error', __('admin/service_provider.property_delete_failed'));
        }

        return back();
    }
}
