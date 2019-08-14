<?php

namespace App\Http\Controllers\Manage\CP;

use App\Models\Property;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AnalyticsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $property_id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, $property_id)
    {
        return view('manage.cp.analytics.analytics', compact('property_id'));
    }
}
