<?php

namespace App\Http\Controllers\Manage\Organization;

use Log;
use App\Models\Entry;
use League\Csv\Writer;
use App\Models\Playlist;
use App\Models\Property;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Jobs\EntryDeletionJob;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Services\Serve\VideoUrlService;

class VideoController extends Controller
{
    /**
     * Display a listing of all videos.
     *
     * @param Request $request
     * @param $property_id
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $playlist_id = $request->playlist;
        $playlist = $playlist_id ? Playlist::findOrFail($playlist_id) : '';
        $search = $request->input('search');
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $sort = $request->input('sort');
        $show_sort = $sort ?? 'desc';

        if ($request->filled('playlist') || $request->filled('search') || $request->filled('start_date') || $request->filled('end_date')) {
            if ($request->filled('playlist')) {
                if (Gate::denies('manager-cp-playlist', [$playlist->property_id, $playlist])) {
                    abort(403);
                }
            }

            $videos_query = Organization::Organization()->entries()
                ->where('entries.status', Entry::STATUS_READY)
                ->when($playlist_id, function ($query) use ($playlist_id) {
                    return $query->join('entry_playlist', 'entries.id', '=', 'entry_playlist.entry_id')
                        ->where('entry_playlist.playlist_id', '=', $playlist_id);
                })->requestFilter($search, null, $start_date, $end_date);
            $all_videos_query = clone $videos_query;
            $videos = $videos_query->with('platformAlivod', 'content_provider_property', 'owner')->orderby('entries.updated_at', $show_sort)->paginate(10);
            $all_video_ids = $all_videos_query->pluck('entries.id')->implode(',');

            return view('manage.organization.video.index', compact('videos', 'playlist', 'playlist_id', 'search', 'sort', 'start_date', 'end_date', 'all_video_ids'));
        }

        //~corresponds to date added.
        $videos = Organization::Organization()->entries()->with('platformAlivod', 'content_provider_property', 'owner')
            ->where('entries.status', Entry::STATUS_READY)
            ->orderby('entries.updated_at', $show_sort)->paginate(10);
        $all_video_ids = Organization::Organization()->entries()->where('entries.status', Entry::STATUS_READY)->pluck('entries.id')->implode(',');

        return view('manage.organization.video.index', compact('videos', 'playlist', 'playlist_id', 'search', 'sort', 'start_date', 'end_date', 'all_video_ids'));
    }

    public function delete(Request $request, $id)
    {
        $entry = Entry::find($id);

        if ($entry) {
            $entry->delete();
            session()->flash('success', trans('videofeed.msg_successfully_deleted'));

            return back();
        } else {
            session()->flash('error', trans('videofeed.msg_error'));

            return back();
        }
    }

    private function getVideoIds($request)
    {
        $video_ids = explode(',', $request->get('video_ids'));
        $select_all = $request->select_all;
        if (1 == $select_all) {
            $video_ids = explode(',', $request->all_video_ids);
        }

        return $video_ids;
    }

    public function download($id)
    {
        $video = Entry::withPlatformVideos()->findOrFail($id);

        if ($video->source_url) {
            return response()->streamDownload(function () use ($video) {
                readfile($video->source_url);
            }, $video->source_name, [
                'Content-Type' => 'application/octet-stream',
            ]);
        } else {
            session()->flash('error', __('manage/cp/contents/videos.video_not_found'));

            return back();
        }
    }

    public function bulkDelete(Request $request)
    {
        $video_ids = $this->getVideoIds($request);

        $entries = Entry::whereIn('id', $video_ids)->get();

        if ($entries->count() < 50) {
            foreach ($entries as $entry) {
                if ($entry->delete()) {
                    Log::info('delete video['.$entry->id.'] successfully');
                } else {
                    Log::info('delete video['.$entry->id.'] unsuccessfully');
                }
            }
        } else {
            dispatch(new EntryDeletionJob($video_ids));
        }

        session()->flash('success', __('manage/cp/contents/videos.videos_are_bulk_deleted'));

        return back();
    }

    public function bulkExport(Request $request)
    {
        $video_ids = $this->getVideoIds($request);

        if (empty($video_ids)) {
            session()->flash('error', __('manage/cp/contents/videos.video_metadata_export_failed'));

            return back();
        } else {
            $csv = Writer::createFromFileObject(new \SplTempFileObject());
            $csv->insertOne(['id', 'title', 'description', 'tags', 'download_url']);

            $entries = Entry::with('metadata')->whereIn('id', $video_ids)->get();

            $entries->map(function ($entry) use (&$csv) {
                $entry_video = VideoUrlService::getVideo($entry);
                $csv->insertOne([
                    $entry->id,
                    $entry->name,
                    $entry->description,
                    optional($entry->metadata)->tags,
                    $entry_video['download_url'],
                ]);
            });

            $csv->output(date('YmdHis').'-entry.csv');
            exit();
        }
    }
}
