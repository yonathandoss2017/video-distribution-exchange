<?php

namespace App\Http\Controllers\Manage\SP\Exchange;

use App\Models\PropertySP;
use App\Models\RequestLog;
use Illuminate\Http\Request;

class RequestLogController
{
    public function index(PropertySP $property, Request $request)
    {
        $requestLogs = RequestLog::with('user')
            ->whereHas('serviceProviders', function ($q) use ($property) {
                $q->where('property_id', $property->id);
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('subject', 'like', '%'.$request->get('search').'%');
            })
            ->withCount(['playlists'])
            ->latest()
            ->paginate(10);

        return view('manage.sp.request-log.index', [
            'property_id' => $property->id,
            'property' => $property,
            'requestLogs' => $requestLogs,
        ]);
    }

    public function show(PropertySP $property, RequestLog $requestLog)
    {
        $requestLog = $requestLog->where('id', $requestLog->id)
            ->with('user')
            ->whereHas('serviceProviders', function ($q) use ($property) {
                $q->where('property_id', $property->id);
            })
            ->withCount(['playlists'])
            ->with(['playlists' => function ($q) {
                $q->withCount('entries');
            }])
            ->first();

        if (!$requestLog) {
            abort(404);
        }

        return view('manage.sp.request-log.show', [
            'property_id' => $property->id,
            'property' => $property,
            'requestLog' => $requestLog,
        ]);
    }
}
