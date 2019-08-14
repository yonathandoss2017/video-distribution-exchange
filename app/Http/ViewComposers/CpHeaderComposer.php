<?php

namespace App\Http\ViewComposers;

use App\Models\Entry;
use App\Models\Playlist;
use App\Models\Property;
use Illuminate\View\View;
use App\Models\PropertyCP;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\EntryPlaylistRequestReviewViewData;

class CpHeaderComposer
{
    /**
     * Bind data to the view.
     *
     * @param View $view
     */
    public function compose(View $view)
    {
        // split URL to get property ID
        $url = url()->current();
        $path = parse_url($url, PHP_URL_PATH);
        $segments = explode('/', rtrim($path, '/'));

        $property_id = $segments[2];
        $cp = PropertyCP::findOrFail($property_id);
        $notReadRequestLogCount = $cp->requestLogs()
            ->notRead()
            ->count();

        $headerDetails = [
            'notReadRequestLogCount' => $notReadRequestLogCount,
            'allow_livestream' => $cp->allow_livestream,
            'pendingCount' => 0,
        ];
        $isContentUploader = Auth::User()->isContentUploader($cp);

        if (Gate::allows('manage-cp-request-logs', $property_id)) {
            $headerDetails['pendingCount'] = EntryPlaylistRequestReviewViewData::where([
                    'property_id' => $property_id,
                ])->when(!$isContentUploader, function ($query) {
                    return $query->whereNotIn('status', [Entry::STATUS_REJECTED, Playlist::STATUS_REJECTED]);
                })->count();
        }

        $view->with('headerDetails', $headerDetails);
    }
}
