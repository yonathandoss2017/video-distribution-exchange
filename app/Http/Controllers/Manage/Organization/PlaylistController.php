<?php

namespace App\Http\Controllers\Manage\Organization;

use App\Models\Playlist;
use App\Models\Property;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Services\PlaylistService;
use App\Models\TermsOfMarketplace;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PlaylistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $property_id Property id
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort');
        $show_sort = $sort ?? 'desc';

        $playlists = Organization::Organization()->playlists()->with(['contentProvider', 'creator'])->where('playlists.status', Playlist::STATUS_READY)->when($search, function ($query) use ($search) {
            return $query->where('playlists.name', 'like', '%'.$search.'%');
        })->withCount(['entries' => function ($query) {
            $query->ready();
        }])->orderby('updated_at', $show_sort)
        ->paginate(10);

        return view('manage.organization.playlist.index', compact('playlists', 'sort', 'search'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $result = 1;
        $playlist = Playlist::whereHas('contentProvider', function ($q) {
            $q->where('organization_id', Organization::Organization()->id);
        })->FindOrFail($request->get('playlist_id'));

        $callback = PlaylistService::deletePlaylist($playlist->id);
        if ('success' != $callback['status']) {
            $result = 0;
        }

        if ($result) {
            session()->flash('success', trans('manage/cp/contents/playlists.playlists_are_destroyed'));
        } else {
            session()->flash('error', trans('manage/cp/contents/playlists.some_playlist_delete_failed'));
        }

        return back();
    }

    public function publish(Playlist $playlist)
    {
        $terms = TermsOfMarketplace::where('property_id', $playlist->property_id)->pluck('name', 'id');

        return view('manage.organization.playlist.publish', compact('playlist', 'terms'));
    }

    public function updatePublish(Request $request, Playlist $playlist)
    {
        if ('on' == $request->radio_publish) {
            $validator = Validator::make($request->all(), ['radio_publish' => 'required', 'marketplace_terms' => 'required']);
            $validator = PlaylistService::validatePlaylistPublished($validator, $request);
            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator);
            }
            TermsOfMarketplace::where('property_id', $playlist->property_id)->findOrFail($request->marketplace_terms);
        }

        $inputData = $request->all();
        $inputData['user_id'] = Auth::user()->id;
        $inputData['publish'] = false;
        $inputData['publish_start_date'] = null;
        $inputData['publish_end_date'] = null;
        $inputData['tom_id'] = null;

        $inputData['publish_status'] = Playlist::PUBLISH_STATUS_UNPUBLISH;

        session()->flash('success', trans('manage/cp/contents/playlists.playlist_is_unpublished_successfully'));

        if ('on' == $request->radio_publish && Playlist::STATUS_READY == optional($playlist)->status) {
            $inputData['publish'] = true;
            if (empty($request->available_now)) {
                $inputData['publish_start_date'] = Carbon::parse($request->publish_start_date)->toDateTimeString();
            } else {
                $inputData['publish_start_date'] = null;
            }

            if (empty($request->until_forever)) {
                $inputData['publish_end_date'] = Carbon::parse($request->publish_end_date)->hour(23)->minute(59)->second(59)->toDateTimeString();
            } else {
                $inputData['publish_end_date'] = null;
            }

            $inputData['tom_id'] = $inputData['marketplace_terms'];

            if ($request->is_submit) {
                $inputData['publish_status'] = Playlist::PUBLISH_STATUS_PUBLISHED;
            }

            session()->flash('success', trans('manage/cp/contents/playlists.playlist_is_published_successfully'));
        }

        $playlist->fill($inputData);
        $playlist->save();

        return redirect(route('manage.organization.playlists.index'));
    }
}
