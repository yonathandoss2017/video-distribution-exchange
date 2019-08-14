<?php

namespace App\Http\Controllers\Manage\SP\IVST;

use App\Models\PropertySP;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SyndicationController extends Controller
{
    public function __construct(Request $request)
    {
        $property_id = $request->property;
        $this->property = $property = PropertySP::find($property_id);

        $this->middleware('can:manage-property,property,id');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($property_id)
    {
        $sp = PropertySP::find($property_id);

        return view('manage.sp.ivst.syndication.create', compact('property_id', 'sp'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store($property_id, Request $request)
    {
        if (1 == $request->get('is_remove')) {
            $request->merge(['sp_ivst_notification_url' => '']);
        } else {
            $this->validate($request, [
                'sp_ivst_notification_url' => 'required|url',
            ], [
                'required' => __('manage/sp/ivst/syndication.validate_required_fail'),
                'url' => __('manage/sp/ivst/syndication.validate_url_fail'),
            ]);
        }

        if (PropertySP::where('id', $property_id)->update(['sp_ivst_notification_url' => $request->get('sp_ivst_notification_url')])) {
            session()->flash('success', __('manage/sp/ivst/syndication.updated_successfully'));
        } else {
            session()->flash('error', __('manage/sp/ivst/syndication.updated_fail'));
        }

        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
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
    }
}
