<?php

namespace App\Http\Controllers\Manage\CP;

use App\Models\Playlist;
use App\Models\PropertyCP;
use Illuminate\Http\Request;
use App\Models\TermsOfDistribution;
use App\Http\Controllers\Controller;
use App\Http\Resources\PlaylistCollection;

class DistributionPlaylistController extends Controller
{
    public function index(PropertyCP $property, TermsOfDistribution $termsOfDistribution)
    {
        $termsOfDistribution->load(['playlists' => function ($q) {
            $q->withCount(['entries' => function ($q) {
                $q->published()
                    ->WhereHasPlatformVideos();
            }]);
        }]);

        $selectedPlaylist = $termsOfDistribution->playlists->map(function ($q) {
            return [
                'id' => $q->id,
                'entries_count' => $q->entries_count,
                'name' => $q->name,
                'created_at' => $q->created_at->timestamp,
                'updated_at' => $q->updated_at->timestamp,
            ];
        });

        return view('manage.cp.exchange.distribution.playlist.index', [
            'active_menu' => 'exchange',
            'property_id' => $property->id,
            'property' => $property,
            'tod' => $termsOfDistribution,
            'selectedPlaylist' => $selectedPlaylist,
        ]);
    }

    public function jsonPlaylist(PropertyCP $property, Request $request)
    {
        $playlists = Playlist::where('property_id', $property->id)
            ->where('status', Playlist::STATUS_READY)
            ->withCount(['entries' => function ($q) {
                $q->published()
                    ->WhereHasPlatformVideos();
            }])
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->get('search').'%');
            })
            ->paginate(10)
            ->appends($request->all());

        return new PlaylistCollection($playlists);
    }

    public function update(PropertyCP $property, TermsOfDistribution $termsOfDistribution, Request $request)
    {
        $request->validate([
            'playlists' => 'required|array',
        ]);

        $termsOfDistribution->playlists()->sync($request->get('playlists'));

        session()->flash('success', __('manage/cp/exchange/distribution.playlist_updated_successfully'));

        return response()->json([
            'status' => 'success',
            'data' => $termsOfDistribution->load('playlists'),
        ]);
    }
}
