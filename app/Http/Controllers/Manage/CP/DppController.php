<?php

namespace App\Http\Controllers\Manage\CP;

use App\Models\User;
use App\Models\Entry;
use App\Models\Playlist;
use App\Models\Property;
use App\Models\EntryScene;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use App\Notifications\Dpp\RequestEmail;
use Illuminate\Pagination\LengthAwarePaginator;

class DppController extends Controller
{
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
        $playlists = Playlist::where('property_id', $property_id)->whereNotNull('dpp_status')
            ->with(['dpp_entries' => function ($query) {
                $query->where('status', Entry::STATUS_READY)
                    ->where('dpp_status', '<>', Entry::DPP_STATUS_PROCESSING)
                    ->withCount('scenes')->latest();
            }])
            ->whereHas('dpp_entries', function ($query) {
                $query->where('platforms', 'like', '%'.Entry::PLATFORM_ALIVOD.'%');
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('dpp_status', $status);
            })
            ->orderby('dpp_updated_at', 'desc')->paginate(10);

        return view('manage.cp.dpp.index', compact('property_id', 'playlists'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param $property_id
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $property_id)
    {
        $playlistBuilder = Playlist::where([
                'property_id' => $property_id,
                'status' => Playlist::STATUS_READY,
            ])
            ->whereHas('entries', function ($query) {
                $query->where('status', Entry::STATUS_READY)
                    ->where('platforms', 'like', '%'.Entry::PLATFORM_ALIVOD.'%');
            });
        if ($request->filled('playlistId')) {
            $playlist = $playlistBuilder->findOrFail($request->playlistId);
        } else {
            $playlists = $playlistBuilder->pluck('name', 'id');
        }

        return view('manage.cp.dpp.create', compact('property_id', 'playlists', 'playlist', 'hasPlaylist'));
    }

    /**
     * Show the form for review creating a new resource.
     *
     * @param $property_id
     *
     * @return \Illuminate\Http\Response
     */
    public function createReview(Request $request, $property_id)
    {
        if (!session()->has('videoIds') || !session()->has('playlistId')) {
            return redirect()->route('manage.cp.dpp.index', ['property_id' => $property_id]);
        }

        $videoIds = session()->get('videoIds');
        $playlistId = session()->get('playlistId');
        $playlist = Playlist::find($playlistId);
        if ($request->filled('deleteVideoId')) {
            $position = array_search($request->deleteVideoId, $videoIds);
            unset($videoIds[$position]);
            session()->put('videoIds', $videoIds);

            return back();
        }

        $videos = Entry::whereIn('id', $videoIds)
            ->where('property_id', $property_id)
            ->paginate(10);

        return view('manage.cp.dpp.create-review')->with(compact('property_id', 'videos', 'playlistId', 'videoIds', 'playlist'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $property_id)
    {
        $this->validate($request, [
            'playlistId' => 'required',
            'selectedList' => 'required',
        ]);

        $videoIds = explode(',', $request->selectedList);
        $playlist = Playlist::with('entries')->findOrFail($request->playlistId);
        $data = [];
        foreach ($videoIds as $videoId) {
            $data[$videoId] = [
                'dpp_requested' => true,
            ];
        }

        DB::transaction(function () use ($playlist, $data, $videoIds, $property_id) {
            $playlist->entries()->syncWithoutDetaching($data);

            $now = date('Y-m-d H:i:s');
            $playlist->dpp_created_at = $now;
            $playlist->dpp_updated_at = $now;
            $playlist->dpp_status = Playlist::DPP_STATUS_PROCESSING;
            $playlist->save();

            Entry::whereIn('id', $videoIds)
                ->whereNull('dpp_status')
                ->update(['dpp_status' => Entry::DPP_STATUS_PROCESSING]);

            $property = Property::with('organization')->find($property_id);
            $organization = $property->organization;
            $user = User::where('email', 'andrew@digital-vantage.com')->firstOrFail();
            //send a notification
            $user->notify(new RequestEmail($organization, $property, $playlist, App::getLocale()));
        });

        session()->forget('videoIds');
        session()->forget('playlistId');

        return redirect()->route('manage.cp.dpp.index', ['property_id' => $property_id]);
    }

    /**
     * Display the specified resource.
     *
     * @param $playlist_id
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $property_id, $playlist_id)
    {
        list($playlist_summary, $scenes, $playlist) = $this->prepareSummaryData($playlist_id, $property_id);
        $locales = $this->paginate($playlist_summary['locales'], 10, null, ['path' => Paginator::resolveCurrentPath()]);
        if ($request->ajax()) {
            return response()->json(view('manage.cp.dpp.locales', ['locales' => $locales])->render());
        }

        return view('manage.cp.dpp.show', compact('property_id', 'playlist_id', 'playlist_summary', 'playlist', 'locales'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param $property_id
     * @param $playlist_id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $property_id, $playlist_id)
    {
        $status = $request->input('status');
        list($playlist_summary, $scenes, $playlist) = $this->prepareSummaryData($playlist_id, $property_id);
        $entries = $playlist->dpp_entries()->withCount('scenes')
            ->when($status, function ($query) use ($status) {
                return $query->where('dpp_status', $status);
            })
            ->where('platforms', 'like', '%'.Entry::PLATFORM_ALIVOD.'%')
            ->orderby('updated_at', 'desc')->paginate(10, ['*'], 'video');
        $review_scenes_count = EntryScene::join('entries', 'entries.id', '=', 'entry_scenes.entry_id')
            ->whereIn('entries.id', $playlist->dpp_entries->pluck('id'))
            ->where('entries.dpp_status', '<>', Entry::DPP_STATUS_PROCESSING)
            ->count();
        $locales = $this->paginate($playlist_summary['locales'], 10, $request->locales, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'locale',
        ]);
        if ($request->ajax()) {
            if ('locale' == $request->type) {
                return response()->json(view('manage.cp.dpp.locales', ['locales' => $locales])->render());
            }
            if ('video' == $request->type) {
                return response()->json(view('manage.cp.dpp.videos', ['entries' => $entries, 'property_id' => $property_id, 'playlist' => $playlist, 'status' => $status])->render());
            }
        }

        return view('manage.cp.dpp.edit', compact('property_id', 'playlist', 'entries', 'scenes', 'playlist_summary', 'review_scenes_count', 'locales'));
    }

    private function paginate($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    private function prepareSummaryData($playlist_id, $property_id)
    {
        $playlist = Playlist::with('dpp_entries')->where('property_id', $property_id)->findOrFail($playlist_id);
        $scene = EntryScene::join('entries', 'entries.id', '=', 'entry_scenes.entry_id')
            ->whereIn('entries.id', $playlist->dpp_entries->pluck('id'))
            ->where('entries.dpp_status', '<>', Entry::DPP_STATUS_PROCESSING)
            ->select('entry_scenes.dpp_duration', 'entry_scenes.type', 'entry_scenes.locale')->get();
        $playlist_summary = [
            'total_videos' => $playlist->dpp_entries->count(),
            'total_scenes' => $scene->count(),
            'total_dpp' => $scene->sum('dpp_duration'),
            'scenes' => [],
            'locales' => [],
        ];
        $scenes = [
            'total' => $scene->count(),
            'total_dpp' => $scene->sum('dpp_duration'),
            'types' => [],
        ];
        $types = EntryScene::getTypes();
        foreach ($types as $type) {
            $s = $scene->where('type', $type);
            $scenes['types'][$type] = [$s->count(), $s->sum('dpp_duration')];
            $playlist_summary['scenes'][] = [
                'type' => $type,
                'scenes_num' => $s->count(),
                'dpp_sum' => $s->sum('dpp_duration'),
            ];
        }
        $locales = $scene->groupBy('locale');
        foreach ($locales as $locale => $items) {
            if (!empty($locale)) {
                $playlist_summary['locales'][] = [
                    'locale' => $locale,
                    'scenes_num' => $items->count(),
                    'dpp_sum' => $items->sum('dpp_duration'),
                ];
            }
        }

        return [$playlist_summary, $scenes, $playlist];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param $property_id
     * @param $playlist_id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $property_id, $playlist_id)
    {
        $playlist = Playlist::with(['dpp_entries' => function ($query) {
            $query->where('dpp_status', Entry::DPP_STATUS_REVIEW);
        }])->where('property_id', $property_id)->findOrFail($playlist_id);
        DB::beginTransaction();
        try {
            $playlist->dpp_status = Playlist::DPP_STATUS_PUBLISHED;
            $playlist->save();

            $entryIds = $playlist->dpp_entries->pluck('id')->toArray();
            Entry::whereIn('id', $entryIds)->update([
                'dpp_status' => Entry::DPP_STATUS_PUBLISHED,
            ]);
            DB::commit();

            return response()->json(true);
        } catch (Exception $e) {
            DB::rollBack();
        }
    }

    /**
     * Remove the specified entry from specified playlist.
     *
     * @param $property_id
     * @param $playlist_id
     * @param $entry_id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteEntry($property_id, $playlist_id, $entry_id)
    {
        $playlist = Playlist::where('property_id', $property_id)->findOrFail($playlist_id);
        $entry = Entry::where('property_id', $property_id)->findOrFail($entry_id);

        DB::table('entry_playlist')->where('entry_id', $entry_id)->where('playlist_id', $playlist_id)
            ->update(['dpp_requested' => null]);

        session()->flash('success', trans('manage/cp/ivideoadd/ivideoadd.msg_successfully_deleted'));

        return back();
    }

    public function getVideoList(Request $request, $property_id, $playlist_id)
    {
        $videos = Entry::join('entry_playlist', 'entries.id', '=', 'entry_playlist.entry_id')
            ->where('property_id', $property_id)
            ->where('entries.status', Entry::STATUS_READY)
            ->where('entry_playlist.playlist_id', $playlist_id)
            ->whereNull('entry_playlist.dpp_requested')
            ->where('entries.platforms', 'like', '%'.Entry::PLATFORM_ALIVOD.'%')
            ->latest()->paginate(10);

        return view('manage.cp.dpp.video-list', ['videos' => $videos])->render();
    }

    public function selectVideo(Request $request, $property_id)
    {
        $this->validate($request, [
            'playlists' => 'required',
            'selectedList' => 'required',
        ]);

        $videoIds = explode(',', $request->selectedList);
        $playlistId = $request->playlists;
        session()->put('videoIds', $videoIds);
        session()->put('playlistId', $playlistId);

        return redirect()->route('manage.cp.dpp.create.review', ['property_id' => $property_id]);
    }
}
