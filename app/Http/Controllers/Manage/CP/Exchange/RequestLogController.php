<?php

namespace App\Http\Controllers\Manage\CP\Exchange;

use Carbon\Carbon;
use App\Models\PropertyCP;
use App\Models\RequestLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RequestLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $property_id)
    {
        $search = $request->input('search');
        $cp = PropertyCP::findOrFail($property_id);
        $cp->requestLogs()->notRead()->update(['read_at' => Carbon::now()]);
        $requestLogs = $cp->requestLogs()->withCount(['playlists' => function ($query) use ($property_id) {
            $query->where('property_id', $property_id);
        }])
            ->with('user')
            ->when($search, function ($query) use ($search) {
                return $query->where('subject', 'like', '%'.$search.'%');
            })
            ->orderby('created_at', 'desc')
            ->paginate(10);

        return view('manage.cp.exchange.request-logs.index', compact('property_id', 'requestLogs', 'search'));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $property_id, $log_id)
    {
        $tom_id = intval($request->get('tom_id'));

        $requestLog = RequestLog::with('serviceProviders.organization')
            ->with('recipients')
            ->with(['playlists' => function ($query) use ($property_id, $tom_id) {
                $query->where('property_id', $property_id);
                $query->when($tom_id > 0, function ($q) use ($tom_id) {
                    $q->where('tom_id', $tom_id);
                });
                $query->withCount(['entries' => function ($q) {
                    $q->published();
                }]);
            }, 'playlists.marketplaceTerm'])
            ->findOrFail($log_id);

        if ($request->isXmlHttpRequest()) {
            return view('manage.cp.exchange.request-logs.playlist', compact('property_id', 'requestLog'))->render();
        } else {
            $terms = collect([]);
            $requestLog->playlists->each(function ($playlist) use (&$terms) {
                if ($playlist->marketplaceTerm) {
                    $terms = $terms->push($playlist->marketplaceTerm);
                }
            });
            $terms = $terms->unique();

            return view('manage.cp.exchange.request-logs.show', compact('property_id', 'requestLog', 'terms'));
        }
    }
}
