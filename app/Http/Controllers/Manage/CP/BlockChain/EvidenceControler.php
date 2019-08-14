<?php

namespace App\Http\Controllers\Manage\CP\BlockChain;

use Log;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Entry;
use App\Models\Playlist;
use App\Models\Property;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Jobs\AnzhengDownReceipt;
use App\Jobs\AnzhengSyncFileInfo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Models\EntryAnzhengEvidence;
use Illuminate\Support\Facades\Auth;
use App\Models\PlaylistEvidenceRequest;
use App\Jobs\VideoFingerprintExtractionJob;
use App\Services\Blockchain\AnZhengEvidenceService;

class EvidenceControler extends Controller
{
    private $tag = '[block chain]: ';

    public function index(Request $request, $property_id)
    {
        $playlist_evidences = PlaylistEvidenceRequest::where('property_id', $property_id)
            ->with(['playlist' => function ($query) use ($property_id) {
                $query->withCount('evidenceEntries');
            }])
            ->orderby('created_at', 'desc')
            ->paginate(10);

        return view('manage.cp.block-chain.index', compact('property_id', 'playlist_evidences'));
    }

    public function create(Request $request, $property_id)
    {
        $playlistBuilder = Playlist::where([
            'property_id' => $property_id,
            'status' => Playlist::STATUS_READY,
        ])
        ->whereHas('entries', function ($query) {
            $query->DoesntHave('anzhengEvidence');
        });
        if ($request->filled('playlistId')) {
            $playlist = $playlistBuilder->findOrFail($request->playlistId);
        } else {
            $playlists = $playlistBuilder->pluck('name', 'id');
        }

        $org_name = Organization::Organization()->name;
        $property = Property::findOrFail($property_id);

        return view('manage.cp.block-chain.create', compact('property_id', 'playlists', 'playlist', 'org_name', 'property'));
    }

    public function getVideoList(Request $request, $property_id, $playlist_id)
    {
        $videos = Entry::join('entry_playlist', 'entries.id', '=', 'entry_playlist.entry_id')
            ->doesntHave('anzhengEvidence')
            ->where('property_id', $property_id)
            ->where('entries.status', Entry::STATUS_READY)
            ->where('entry_playlist.playlist_id', $playlist_id)
            ->latest()->paginate(10);

        return view('manage.cp.block-chain.video-list', ['videos' => $videos, 'property_id' => $property_id])->render();
    }

    public function selectVideo(Request $request, $property_id)
    {
        $this->validate($request, [
            'playlists' => 'required',
            'selectedList' => 'required',
            'entity' => 'required',
        ]);

        $videoIds = explode(',', $request->selectedList);
        $playlistId = $request->playlists;
        $entity = $request->entity;
        session()->put('videoIds', $videoIds);
        session()->put('playlistId', $playlistId);
        session()->put('entity', $entity);

        return redirect()->route('manage.cp.block-chain.create.review', ['property_id' => $property_id]);
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
        if (!session()->has('videoIds') || !session()->has('playlistId') || !session()->has('entity')) {
            return redirect()->route('manage.cp.block-chain.index', ['property_id' => $property_id]);
        }

        $videoIds = session()->get('videoIds');
        $playlistId = session()->get('playlistId');
        $entity = session()->get('entity');
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

        return view('manage.cp.block-chain.create-review')->with(compact('property_id', 'videos', 'playlistId', 'videoIds', 'playlist', 'entity'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $property_id, AnZhengEvidenceService $anZhengEvidenceService)
    {
        $this->validate($request, [
            'playlistId' => 'required',
            'selectedList' => 'required',
            'entity' => 'required',
        ]);

        $videoIds = explode(',', $request->selectedList);
        $entity = $request->entity;
        $playlist = Playlist::with('entries')->findOrFail($request->playlistId);
        $date_time = Carbon::now();

        try {
            DB::transaction(function () use ($playlist, $videoIds, $property_id, $date_time, $entity) {
                $playlist_evidence = PlaylistEvidenceRequest::where('playlist_id', $playlist->id)->first();
                if ($playlist_evidence) {
                    $playlist_evidence->status = PlaylistEvidenceRequest::STATUS_PROCESSING;
                    $playlist_evidence->updated_by = Auth::user()->id;
                    $playlist_evidence->save();
                } else {
                    PlaylistEvidenceRequest::create([
                        'property_id' => $property_id,
                        'playlist_id' => $playlist->id,
                        'created_by' => Auth::user()->id,
                    ]);
                }

                $evidences = [];
                foreach ($videoIds as $videoId) {
                    $entry_evidence = EntryAnzhengEvidence::where('entry_id', $videoId)->first();
                    if ($entry_evidence) {
                        if (EntryAnzhengEvidence::STATUS_EVIDENCE_ERROR == $entry_evidence->status) {
                            $entry_evidence->status = EntryAnzhengEvidence::STATUS_REQUEST_EVIDENCE;
                            $entry_evidence->save();
                        }
                    } else {
                        $evidences[] = [
                            'entry_id' => $videoId,
                            'entity' => $entity,
                            'case_id' => '',
                            'status' => EntryAnzhengEvidence::STATUS_REQUEST_EVIDENCE,
                            'created_at' => $date_time,
                            'updated_at' => $date_time,
                        ];
                    }
                }
                if (!empty($evidences)) {
                    EntryAnzhengEvidence::insert($evidences);
                }
            });
        } catch (Exception $e) {
            Log::error($this->tag.print_r($e->getMessage(), true));

            session()->flash('error', trans('manage/cp/block-chain/block-chain.msg_store_error'));

            return back()->withInput();
        }

        AnzhengSyncFileInfo::dispatch($videoIds, $entity);

        //start video fingerprint extraction
        VideoFingerprintExtractionJob::dispatch($videoIds);

        session()->forget('videoIds');
        session()->forget('playlistId');
        session()->forget('entity');

        return redirect()->route('manage.cp.block-chain.index', ['property_id' => $property_id]);
    }

    public function edit(Request $request, $property_id, $id)
    {
        $playlist_evidence_request = PlaylistEvidenceRequest::where('property_id', $property_id)->findOrFail($id);

        $entries = $playlist_evidence_request->playlist->evidenceEntries()->with('anzhengEvidence')->paginate(10, ['*'], 'video');

        if ($request->ajax()) {
            if ('video' == $request->type) {
                return response()->json(view('manage.cp.block-chain.videos', compact('property_id', 'playlist_evidence_request', 'entries'))->render());
            }
        }

        return view('manage.cp.block-chain.edit', compact('property_id', 'playlist_evidence_request', 'entries'));
    }

    public function getReceipt(Request $request, $property_id, $entry_id)
    {
        $entry = Entry::where('property_id', $property_id)->findOrFail($entry_id);
        EntryAnzhengEvidence::where('case_id', $entry->anzhengEvidence->case_id)->update(['status' => EntryAnzhengEvidence::STATUS_REQUEST_DOWNLOAD]);

        AnzhengDownReceipt::dispatch([$entry_id]);

        session()->flash('success', trans('manage/cp/block-chain/block-chain.msg_downloading_receipt'));

        return back();
    }
}
