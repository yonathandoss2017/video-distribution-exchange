<?php

namespace App\Http\Controllers\Manage\CP;

use Validator;
use App\Models\Entry;
use App\Models\Playlist;
use App\Models\Property;
use App\Models\PropertyCP;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\Vod\VodService;
use App\Models\EntryAiReviewResult;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Jobs\Vod\Ali\VideoAiReviewJob;
use App\Jobs\NotifyServiceProvidersJob;
use App\Models\EntryPlaylistRequestReviewViewData;

class RequestLogController extends Controller
{
    public function __construct(Request $request)
    {
        if (null != $request->route('property')) {
            $this->middleware('can:manage-cp-request-logs,property');
            $this->middleware('can:manage-cp-request-log-comments,property')->only(['commentEdit', 'commentStore']);
            $this->middleware('can:manage-ai-review,property')->only(['aiReview', 'bulkAiReview']);
            $this->middleware('can:view-ai-review,property')->only(['aiReviewResult']);
        }
    }

    public function index(Request $request, $property_id)
    {
        $provider = PropertyCP::findOrFail($property_id);

        $search = $request->input('search');
        $isContentUploader = Auth::User()->isContentUploader($provider);

        $pending_list_query = EntryPlaylistRequestReviewViewData::where([
            'property_id' => $property_id,
        ])->when(!$isContentUploader, function ($query) {
            return $query->whereNotIn('status', [Entry::STATUS_REJECTED, Playlist::STATUS_REJECTED]);
        })->when($search, function ($query) use ($search) {
            return $query->where('name', 'like', '%'.$search.'%');
        });

        $all_pending_list_query = clone $pending_list_query;

        $pending_list = $pending_list_query->with('playlist', 'entry', 'entry.platformAlivod', 'entry.aiReviewResult')->orderby('created_at', 'desc')->paginate(10);

        $all_pending_list = $all_pending_list_query->select('id', 'type')->get();
        $all_ids = '';
        foreach ($all_pending_list as $key => $value) {
            if ($all_ids) {
                $all_ids .= ','.$value->type.'-'.$value->id;
            } else {
                $all_ids .= $value->type.'-'.$value->id;
            }
        }

        $isContentUploader = Auth::user()->isContentUploader(PropertyCP::findOrFail($property_id));

        return view('manage.cp.request-logs', compact('pending_list', 'property_id', 'search', 'provider', 'isContentUploader', 'all_ids'));
    }

    public function approve(Request $request, $property_id, $id)
    {
        $validator = Validator::make($request->all(), [
            'type' => [
                'required',
                Rule::in(['Entry', 'Playlist']),
            ],
        ], [
            'type.required' => 'type is required.',
        ]);

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        $result = false;
        $type = $request->get('type');
        if ('Playlist' == $type) {
            $playlist = Playlist::findOrFail($id);
            if ($property_id != $playlist->property_id) {
                abort(401);
            }
            $playlist->status = Playlist::STATUS_READY;
            $result = $playlist->save();
        } elseif ('Entry' == $type) {
            $entry = Entry::findOrFail($id);
            if ($property_id != $entry->property_id) {
                abort(401);
            }
            $entry->status = Entry::STATUS_READY;
            $result = $entry->save();

            NotifyServiceProvidersJob::dispatch($entry);
        }

        if ($result) {
            session()->flash('success', trans('manage/cp/exchange/request_logs.msg_successfully_approved'));
        } else {
            session()->flash('error', trans('manage/cp/exchange/request_logs.msg_error'));
        }

        return redirect(route('manage.cp.request-logs.index', $property_id));
    }

    public function reject(Request $request, $property_id, $id)
    {
        $validator = Validator::make($request->all(), [
            'type' => [
                'required',
                Rule::in(['Entry', 'Playlist']),
            ],
        ], [
            'type.required' => trans('manage/cp/exchange/request_logs.type_is_required'),
        ]);

        $result = false;
        $type = $request->get('type');
        if ('Playlist' == $type) {
            $playlist = Playlist::findOrFail($id);
            if ($property_id != $playlist->property_id) {
                abort(401);
            }
            $playlist->status = Playlist::STATUS_REJECTED;
            $result = $playlist->save();
        } elseif ('Entry' == $type) {
            $entry = Entry::findOrFail($id);
            if ($property_id != $entry->property_id) {
                abort(401);
            }
            $entry->status = Entry::STATUS_REJECTED;
            $result = $entry->save();
        }

        if ($result) {
            session()->flash('success', trans('manage/cp/exchange/request_logs.msg_successfully_rejected'));
        } else {
            session()->flash('error', trans('manage/cp/exchange/request_logs.msg_error'));
        }

        return redirect(route('manage.cp.request-logs.index', $property_id));
    }

    public function aiReview(Request $request, $property_id, $id, VodService $vodService)
    {
        $entry_playlist = EntryPlaylistRequestReviewViewData::findOrFail($id);
        if ('Entry' != $entry_playlist->type) {
            session()->flash('error', __('manage/cp/exchange/request_logs.ai_review_only_support_entry'));

            return back();
        }

        $entry = Entry::whereHas('platformAlivod')
            ->whereDoesntHave('aiReviewResult', function ($query) {
                $query->whereIn('ali_status', [EntryAiReviewResult::STATUS_PROCESSING, EntryAiReviewResult::STATUS_SUCCESS]);
            })
            ->where('property_id', $property_id)
            ->whereNotIn('status', [Entry::STATUS_PROCESSING, Entry::STATUS_ERROR])
            ->with('platformAlivod', 'aiReviewResult')
            ->findOrFail($id);

        $this->aiReviewEntry($entry, $vodService);

        return redirect(route('manage.cp.request-logs.index', $property_id));
    }

    public function bulkApprove(Request $request, $property_id)
    {
        $approve_items = explode(',', $request->get('approve_items'));
        $select_all = $request->select_all;
        if (1 == $select_all) {
            $approve_items = explode(',', $request->all_ids);
        }

        $this->bulkChangeStatus($approve_items, true);

        session()->flash('success', __('manage/cp/contents/request_logs.request_logs_are_bulk_approved'));

        return redirect()->route('manage.cp.request-logs.index', $property_id);
    }

    public function bulkReject(Request $request, $property_id)
    {
        $reject_items = explode(',', $request->get('reject_items'));
        $select_all = $request->select_all;
        if (1 == $select_all) {
            $reject_items = explode(',', $request->all_ids);
        }

        $this->bulkChangeStatus($reject_items, false);

        session()->flash('success', __('manage/cp/contents/request_logs.request_logs_are_bulk_rejected'));

        return redirect()->route('manage.cp.request-logs.index', $property_id);
    }

    public function bulkAiReview(Request $request, $property_id, VodService $vodService)
    {
        $ai_review_items = explode(',', $request->get('ai_review_items'));
        $select_all = $request->select_all;
        if (1 == $select_all) {
            $ai_review_items = explode(',', $request->all_ids);
        }
        $entryIds = [];
        $hasPlaylist = false;
        foreach ($ai_review_items as $review_item) {
            $arr = explode('-', $review_item);
            if ('Playlist' == $arr[0]) {
                $hasPlaylist = true;
                break;
            }
            if ('Entry' == $arr[0]) {
                $entryIds[] = $arr[1];
            }
        }

        if ($hasPlaylist) {
            session()->flash('error', __('manage/cp/exchange/request_logs.ai_review_only_support_entry'));

            return back()->withInput();
        }

        $entries = Entry::whereHas('platformAlivod')
            ->whereDoesntHave('aiReviewResult', function ($query) {
                $query->whereIn('ali_status', [EntryAiReviewResult::STATUS_PROCESSING, EntryAiReviewResult::STATUS_SUCCESS]);
            })
            ->with('platformAlivod')
            ->where('property_id', $property_id)
            ->whereIn('id', $entryIds)
            ->whereNotIn('status', [Entry::STATUS_PROCESSING, Entry::STATUS_ERROR])
            ->get();

        if ($entries->count() > 1) {
            VideoAiReviewJob::dispatch($entries);
            session()->flash('success', __('manage/cp/exchange/request_logs.ai_review_in_processing'));
        } elseif (1 == $entries->count()) {
            $this->aiReviewEntry($entries[0], $vodService);
        } else {
            session()->flash('error', __('manage/cp/exchange/request_logs.ai_review_failed'));
        }

        return redirect()->route('manage.cp.request-logs.index', $property_id);
    }

    private function aiReviewEntry($entry, $vodService)
    {
        $response = $vodService->submitAIReview($entry->platformAlivod->video_id);
        $save_datas = ['jobid' => '', 'ali_status' => EntryAiReviewResult::STATUS_FAIL];
        if ($response && $response->JobId) {
            $save_datas = [
                'jobid' => $response->JobId,
                'ali_status' => EntryAiReviewResult::STATUS_PROCESSING,
            ];
            session()->flash('success', __('manage/cp/exchange/request_logs.ai_review_in_processing'));
        } else {
            session()->flash('error', __('manage/cp/exchange/request_logs.ai_review_failed'));
        }

        return $entry->aiReviewResult()->updateOrCreate([
            'entry_id' => $entry->id,
        ], $save_datas);
    }

    private function bulkChangeStatus($items, $isApprove)
    {
        if ($isApprove) {
            $playlistStatus = Playlist::STATUS_READY;
            $entryStatus = Entry::STATUS_READY;
        } else {
            $playlistStatus = Playlist::STATUS_REJECTED;
            $entryStatus = Entry::STATUS_REJECTED;
        }

        $playlistIds = [];
        $entryIds = [];
        foreach ($items as $item) {
            $item = explode('-', $item);
            $type = $item[0];
            if ('Playlist' == $type) {
                $playlistIds[] = $item[1];
            } else {
                $entryIds[] = $item[1];
            }
        }

        $playlists = Playlist::whereIn('id', $playlistIds)->get();
        $entries = Entry::whereIn('id', $entryIds)->get();

        foreach ($playlists as $playlist) {
            $playlist->status = $playlistStatus;
            $playlist->save();
        }

        foreach ($entries as $entry) {
            $entry->status = $entryStatus;
            $entry->save();
        }

        return;
    }

    public function show(Request $request, $property_id, $id)
    {
        $video = Entry::with('playlists')
        ->with(['metadata'])
        ->withPlatformVideos()
        ->findOrFail($id);

        $property = Property::findOrFail($property_id);
        $data = [
            'property_id' => $property_id,
            'property' => $property,
            'video' => $video,
            'isContentUploader' => Auth::user()->isContentUploader(PropertyCP::findOrFail($property_id)),
            'aiReviewVideoResults' => null,
        ];

        if (Gate::allows('view-ai-review', $property)) {
            $data['aiReviewVideoResults'] = $video->aiReviewVideoResult()->paginate(10);
        }

        return view('manage.cp.request_log.entry_show', $data);
    }

    public function showPlaylist($property_id, $id)
    {
        return view('manage.cp.request_log.playlist_show', [
            'property_id' => $property_id,
            'playlist' => Playlist::find($id),
            'isContentUploader' => Auth::user()->isContentUploader(PropertyCP::findOrFail($property_id)),
        ]);
    }

    public function commentEdit($property_id, $id, $type)
    {
        $item = '';
        if ('Playlist' == $type) {
            $item = Playlist::find($id);
        } else {
            $item = Entry::find($id);
        }

        return view('manage.cp.request_log.comment-edit', [
            'property_id' => $property_id,
            'item' => $item,
            'type' => $type,
        ]);
    }

    public function commentStore(Request $request, $property_id, $id, $type)
    {
        $result = '';

        if ('Playlist' == $type) {
            $result = Playlist::where('id', $id)->update(['comment' => $request->get('comments')]);
        } else {
            $result = Entry::where('id', $id)->update(['comment' => $request->get('comments')]);
        }

        if ($result) {
            session()->flash('success', trans('manage/cp/contents/request_logs.msg_comment_successfully'));
        } else {
            session()->flash('error', trans('manage/cp/contents/request_logs.msg_comment_unsuccessfully'));
        }

        return redirect(route('manage.cp.request-logs.index', $property_id));
    }

    public function aiReviewResult(Request $request, $property_id, $id)
    {
        $video = Entry::where('property_id', $property_id)->findOrFail($id);

        $aiReviewVideoResults = $video->aiReviewVideoResult()->paginate(10);

        return view('manage.cp.ai_review_video_result', compact('aiReviewVideoResults'))->render();
    }
}
