<?php

namespace App\Http\Controllers\Manage\SP;

use Carbon\Carbon;
use App\Models\Playlist;
use App\Models\Property;
use App\Models\PropertySP;
use Illuminate\Http\Request;
use App\Models\PlaylistProperty;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Services\FeaturedImageService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Notification;
use App\Services\Serve\PlaylistDetailsService;
use App\Notifications\Exchange\SpDeletePlaylistEmail;

class PlaylistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $property_id Property id
     *
     * @return \Illuminate\Http\Response
     */
    public function index($property_id, Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort');
        $show_sort = $sort ?? 'desc';
        $sp = PropertySP::findOrFail($property_id);

        $playlists = Playlist::select('playlists.*', 'playlist_property.playlist_name')
            ->leftJoin('playlist_property', function ($j) use ($property_id) {
                $j->on('playlists.id', '=', 'playlist_property.playlist_id')
                    ->where('playlist_property.property_id', $property_id);
            })
            ->whitelistedForSP($sp)
            ->with(['contentProvider.organization', 'activeTods' => function ($q) use ($property_id) {
                $q->where('sp_property_id', $property_id)
                    ->latest('published_at');
            }])
            ->withCount(['entries' => function ($query) {
                $query->published();
            }])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('playlist_property.playlist_name', 'like', '%'.$search.'%');
                });
            })
            ->orderby('updated_at', $show_sort)
            ->paginate(10);

        return view('manage.sp.playlist.index', compact('property_id', 'playlists', 'sort', 'status', 'sp', 'search'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $property_id
     * @param Playlist $playlist
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($property_id, Playlist $playlist)
    {
        $spPlaylist = PlaylistProperty::findRecord($playlist->property_id, $property_id, $playlist->id)->first();
        $playlist_name = PlaylistDetailsService::getName($playlist, $property_id);
        $playlist_thumbnail = object_get($spPlaylist, 'pivot.thumbnail_path') ?: $playlist->thumbnail_path;
        $playlist_tod = PlaylistDetailsService::getActiveTodFromPlaylist($playlist, $property_id);

        return view('manage.sp.playlist.edit', compact('property_id', 'playlist', 'playlist_name', 'playlist_thumbnail', 'playlist_tod'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param $spId property id
     * @param Playlist $playlist
     *
     * @return \Illuminate\Http\Response
     *
     * @internal param int $id
     */
    public function update(Request $request, $spId, Playlist $playlist, FeaturedImageService $imageService)
    {
        $this->validate($request, array_merge([
            'playlist_name' => 'required|max:255',
        ], FeaturedImageService::getValidator()));

        $playlistProperty = PlaylistProperty::findRecord($playlist->property_id, $spId, $playlist->id)->first();

        $cpId = $playlist->property_id;
        $data = [
            'playlist_name' => $request->get('playlist_name'),
            'status_bak' => 'approved',
        ];

        $imageFile = $imageService->update($request, optional($playlistProperty)->thumbnail_path, FeaturedImageService::getImageSavePath([
            'property_id' => $spId,
            'playlist_id' => $playlist->id,
        ]));
        if ('error' == $imageFile) {
            return back()->withInput();
        }
        if ($imageFile) {
            $data['thumbnail_path'] = $imageFile;
        }

        PlaylistProperty::createOrUpdateRecord($cpId, $spId, $playlist->id, $data);

        session()->flash('success', __('manage/sp/content/playlist.playlist_updated_success'));

        return redirect(route('manage.sp.playlists.index', [$spId]));
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param int     $property_id
     * @param int     $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, PropertySP $property, $id)
    {
        $playlist = Playlist::with(['activeTods' => function ($query) use ($property) {
            return $query->where('sp_property_id', $property->id);
        }])->findOrFail($id);

        $delete = PlaylistProperty::where('playlist_id', $id)->where('property_id', $property->id)->delete();
        if ($delete) {
            $playlist->activeTods->map(function ($tod) use ($property, $id) {
                Notification::send($tod->contentProvider->license_notifications, new SpDeletePlaylistEmail($property, $tod, App::getLocale()));
                $tod->playlists()->updateExistingPivot($id, ['deleted_at' => Carbon::now()]);
            });
            Session::flash('success', __('manage/sp/content/playlist.playlist_deleted_success'));
        } else {
            return back()->withErrors(['error' => __('manage/sp/content/playlist.playlist_deleted_failed')]);
        }

        return back();
    }
}
