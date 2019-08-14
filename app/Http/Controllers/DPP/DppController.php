<?php

namespace App\Http\Controllers\DPP;

use DB;
use Exception;
use App\Models\Entry;
use App\Models\Playlist;
use App\Models\EntryScene;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Notifications\Dpp\ReviewEmail;
use Illuminate\Support\Facades\Notification;

class DppController extends Controller
{
    /**
     * GET /dpp/request/new.
     */
    public function newRequest()
    {
        $status = 'processing';
        $playlists = Playlist::with('content_provider.organization')
            ->where('dpp_status', Playlist::DPP_STATUS_PROCESSING)
            ->withCount(['dpp_entries' => function ($query) {
                $query->where('dpp_status', Entry::DPP_STATUS_PROCESSING);
            }])
            ->orderBy('dpp_updated_at', 'desc')->paginate(10);

        return view('dpp.request.index', compact('playlists', 'status'));
    }

    public function readyRequest()
    {
        $status = 'all';
        $playlists = Playlist::with('content_provider.organization')
            ->where('dpp_status', '<>', Playlist::DPP_STATUS_PROCESSING)
            ->withCount(['dpp_entries' => function ($query) {
                $query->where('dpp_status', '<>', Entry::DPP_STATUS_PROCESSING);
            }])
            ->orderBy('dpp_updated_at', 'desc')->paginate(10);

        return view('dpp.request.index', compact('playlists', 'status'));
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $property_id
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $property_id)
    {
        $status = $request->input('status');
        $playlists = Playlist::when($status, function ($query) use ($status) {
            if ('processing' == $status) {
                return $query->where('dpp_status', Playlist::DPP_STATUS_PROCESSING);
            } else {
                return $query->where('dpp_status', '<>', Playlist::DPP_STATUS_PROCESSING);
            }
        })->withCount(['dpp_entries' => function ($query) use ($status) {
            if ('processing' == $status) {
                $query->where('dpp_status', Entry::DPP_STATUS_PROCESSING);
            } else {
                $query->where('dpp_status', '<>', Entry::DPP_STATUS_PROCESSING);
            }
        }])->orderBy('dpp_updated_at', 'desc')->paginate(10);

        return view('dpp.request.index', compact('playlists', 'status'));
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param $playlist_id
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $playlist_id)
    {
        $playlist = Playlist::with('dpp_entries')->findOrFail($playlist_id);

        $entryIds = $playlist->dpp_entries->pluck('id');

        $scenes = EntryScene::whereIn('entry_id', $entryIds)->get();

        //only all scenes is filled with info, can be ready
        $ready = $scenes->isNotEmpty() && $scenes->where('dpp_duration', null)->isEmpty();
        $playlist_summary = [
            'total_videos' => $playlist->dpp_entries->count(),
            'total_scenes' => $scenes->count(),
            'total_dpp' => $scenes->sum('dpp_duration'),
            'scenes' => [],
        ];

        $types = EntryScene::getTypes();
        foreach ($types as $type) {
            $s = $scenes->where('type', $type);
            $playlist_summary['scenes'][] = [
                'type' => $type,
                'scenes_num' => $s->count(),
                'dpp_sum' => $s->sum('dpp_duration'),
            ];
        }

        $status = $request->input('status');
        $entries = Entry::withCount('scenes')
            ->whereIn('id', $entryIds)
            ->when($status, function ($query) use ($status) {
                return $query->where('dpp_status', $status);
            })->orderby('updated_at', 'desc')
            ->paginate(10);

        //check ready again:all videos should has scenes
        $ready = $ready ? 0 == Entry::whereIn('id', $entryIds)->doesntHave('scenes')->count() : false;
        //get new scenes number
        $newScenes = 0;
        if ($ready) {
            $processingEntryIds = $playlist->dpp_entries->where('dpp_status', Entry::DPP_STATUS_PROCESSING)->pluck('id');
            $newScenes = $scenes->whereIn('entry_id', $processingEntryIds)->count();
        }
        $locales = '';

        return view('dpp.request.show', compact('playlist', 'playlist_summary', 'entries', 'ready', 'newScenes', 'locales'));
    }

    public function update(Request $request, $playlist_id)
    {
        $playlist = Playlist::with(['entries' => function ($q) {
            $q->where('dpp_requested', true);
        }])->with('content_provider')->findOrFail($playlist_id);
        DB::beginTransaction();
        try {
            $playlist->dpp_status = Playlist::DPP_STATUS_REVIEW;
            $playlist->save();

            $videoIds = $playlist->entries->pluck('id')->toArray();
            Entry::whereIn('id', $videoIds)->update([
                'dpp_status' => Entry::DPP_STATUS_REVIEW,
            ]);
            DB::commit();

            $users = $playlist->content_provider->users->merge($playlist->content_provider->organization->admins);
            //send notification to cp's users
            Notification::send($users, new ReviewEmail($playlist->content_provider->organization, $playlist->content_provider, $playlist, App::getLocale()));

            return response()->json(true);
        } catch (Exception $e) {
            DB::rollBack();
        }
    }
}
