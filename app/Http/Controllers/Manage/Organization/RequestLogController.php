<?php

namespace App\Http\Controllers\Manage\Organization;

use App\Models\Playlist;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RequestLogController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $pending_list_query = $playlists = Organization::Organization()->playlists()->where('playlists.publish_status', Playlist::PUBLISH_STATUS_REVIEW)
            ->when($search, function ($query) use ($search) {
                return $query->where('playlists.name', 'like', '%'.$search.'%');
            });

        $all_pending_list_query = clone $pending_list_query;

        $pending_list = $pending_list_query
        ->withCount(['entries' => function ($query) {
            $query->ready();
        }])
        ->with('contentProvider', 'creator')->orderby('updated_at', 'desc')->paginate(10);

        $all_ids = $all_pending_list_query->pluck('playlists.id')->implode(',');

        return view('manage.organization.request-logs.index', compact('pending_list', 'search', 'all_ids'));
    }

    public function approve(Request $request, $playlist_id)
    {
        $playlist = Playlist::whereHas('contentProvider', function ($q) {
            $q->where('organization_id', Organization::Organization()->id);
        })->findOrFail($playlist_id);

        if (1 == $playlist->publish) {
            $playlist->publish_status = Playlist::PUBLISH_STATUS_PUBLISHED;
        } else {
            $playlist->publish_status = Playlist::PUBLISH_STATUS_UNPUBLISH;
        }

        $result = $playlist->save();

        if ($result) {
            session()->flash('success', trans('manage/cp/exchange/request_logs.msg_successfully_approved'));
        } else {
            session()->flash('error', trans('manage/cp/exchange/request_logs.msg_error'));
        }

        return redirect(route('manage.organization.request-logs.index'));
    }

    public function reject(Request $request, $playlist_id)
    {
        $playlist = Playlist::whereHas('contentProvider', function ($q) {
            $q->where('organization_id', Organization::Organization()->id);
        })->findOrFail($playlist_id);
        $playlist->publish_status = Playlist::PUBLISH_STATUS_REJECTED;
        $result = $playlist->save();

        if ($result) {
            session()->flash('success', trans('manage/cp/exchange/request_logs.msg_successfully_rejected'));
        } else {
            session()->flash('error', trans('manage/cp/exchange/request_logs.msg_error'));
        }

        return redirect(route('manage.organization.request-logs.index'));
    }

    public function bulkApprove(Request $request)
    {
        $approve_items = explode(',', $request->get('approve_items'));
        $select_all = $request->select_all;
        if (1 == $select_all) {
            $approve_items = explode(',', $request->all_ids);
        }

        $this->bulkChangeStatus($approve_items, true);

        session()->flash('success', __('manage/cp/contents/request_logs.request_logs_are_bulk_approved'));

        return redirect()->route('manage.organization.request-logs.index');
    }

    private function bulkChangeStatus($items, $isApprove)
    {
        $playlists = Organization::Organization()->playlists()->whereIn('playlists.id', $items)->get();

        foreach ($playlists as $playlist) {
            if ($isApprove) {
                if (1 == $playlist->publish) {
                    $playlist->publish_status = Playlist::PUBLISH_STATUS_PUBLISHED;
                } else {
                    $playlist->publish_status = Playlist::PUBLISH_STATUS_UNPUBLISH;
                }
            } else {
                $playlist->publish_status = Playlist::PUBLISH_STATUS_REJECTED;
            }
            $playlist->save();
        }

        return;
    }

    public function bulkReject(Request $request)
    {
        $reject_items = explode(',', $request->get('reject_items'));
        $select_all = $request->select_all;
        if (1 == $select_all) {
            $reject_items = explode(',', $request->all_ids);
        }

        $this->bulkChangeStatus($reject_items, false);

        session()->flash('success', __('manage/cp/contents/request_logs.request_logs_are_bulk_rejected'));

        return redirect()->route('manage.organization.request-logs.index');
    }

    public function showPlaylist($playlist_id)
    {
        return view('manage.organization.request-logs.playlist_show', [
            'playlist' => Playlist::with('marketplaceTerm')->whereHas('contentProvider', function ($q) {
                $q->where('organization_id', Organization::Organization()->id);
            })->FindOrFail($playlist_id),
        ]);
    }

    public function commentEdit($playlist_id)
    {
        $item = Playlist::whereHas('contentProvider', function ($q) {
            $q->where('organization_id', Organization::Organization()->id);
        })->FindOrFail($playlist_id);

        return view('manage.organization.request-logs.comment-edit', [
            'item' => $item,
        ]);
    }

    public function commentStore(Request $request, $playlist_id)
    {
        $playlist = Playlist::whereHas('contentProvider', function ($q) {
            $q->where('organization_id', Organization::Organization()->id);
        })->findorfail($playlist_id);
        $playlist->publish_review_comment = $request->get('comments');

        if ($playlist->save()) {
            session()->flash('success', trans('manage/cp/contents/request_logs.msg_comment_successfully'));
        } else {
            session()->flash('error', trans('manage/cp/contents/request_logs.msg_comment_unsuccessfully'));
        }

        return redirect(route('manage.organization.request-logs.index'));
    }
}
