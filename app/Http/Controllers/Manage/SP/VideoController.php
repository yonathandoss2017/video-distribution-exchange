<?php

namespace App\Http\Controllers\Manage\SP;

use Carbon\Carbon;
use App\Models\Entry;
use League\Csv\Writer;
use App\Models\Playlist;
use App\Models\Property;
use App\Models\PropertySP;
use Illuminate\Http\Request;
use App\Services\VideoService;
use App\Models\PlaylistProperty;
use App\Http\Controllers\Controller;
use App\Services\Serve\VideoUrlService;
use Illuminate\Support\Facades\Validator;

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
    public function index(Request $request, $property_id)
    {
        $provider = PropertySP::findOrFail($property_id);

        $status = $request->status;
        $playlist_id = $request->playlist;
        $playlist = '';
        $pp = '';
        if ($playlist_id) {
            $playlist = Playlist::findOrFail($playlist_id);
            $pp = PlaylistProperty::where('property_id', $property_id)->where('playlist_id', $playlist_id)->first();
        }
        $search = $request->input('search');
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $sort = $request->input('sort');
        $show_sort = $sort ?? 'desc';

        //~corresponds to date added.
        $videos = $provider->entries($status, $playlist_id, $search)
            ->with('playlists')
            ->when($start_date, function ($query) use ($start_date) {
                return $query->whereDate('published_at', '>=', Carbon::parse($start_date)->format('Y-m-d'));
            })
            ->when($end_date, function ($query) use ($end_date) {
                return $query->whereDate('published_at', '<=', Carbon::parse($end_date)->format('Y-m-d'));
            })
            ->withPlatformVideos()
            ->published()
            ->orderBy('entries.published_at', $show_sort)
            ->paginate(10);

        $all_video_ids = implode(',', $provider->entries()->published()->pluck('id')->toArray());

        return view('manage.sp.videos', compact('videos', 'property_id', 'playlist', 'playlist_id', 'status', 'search', 'provider', 'pp', 'sort', 'start_date', 'end_date', 'all_video_ids'));
    }

    public function edit(Request $request, $property_id, $id)
    {
        $video = VideoService::getEntryProperty($property_id, $id);
        if ($video) {
            $property = Property::findOrFail($property_id);
            $playlists = Playlist::where('property_id', $property_id)->orderby('id', 'desc')->get();
            $video = VideoService::getEntryData($video);

            return view('manage.sp.video.edit', compact('property_id', 'video', 'playlists', 'property'));
        } else {
            session()->flash('error', __('manage/sp/content/video.video_not_found'));

            return back()->withInput();
        }
    }

    public function update(Request $request, $property_id, $entry_id, VideoService $videoService)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:255|min:1',
            'imagefile' => 'mimes:jpeg,png|max:1024|dimensions:max_width=1920,max_height=1080',
        ]);

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        $videoService->updateVideo($request, $property_id, $entry_id);

        session()->flash('success', __('manage/sp/content/video.video_updated_success'));

        return redirect(route('manage.sp.video.edit', [$property_id, $entry_id]));
    }

    public function download($propertyId, $id)
    {
        $sp = PropertySP::findOrFail($propertyId);
        $video = $sp->entries(Entry::STATUS_READY)->withPlatformVideos()->findOrFail($id);

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

    public function bulkDownload(Request $request, $property_id)
    {
        $video_ids = $request->video_ids;
        $select_all = $request->select_all;
        $status = $request->status;
        $search = $request->input('search');
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (1 == $select_all) {
            $video_ids = $request->all_video_ids;
        }
        $sp = PropertySP::findOrFail($property_id);

        if (empty($video_ids)) {
            session()->flash('error', __('manage/cp/contents/videos.video_metadata_export_failed'));

            return back();
        } else {
            $csv = Writer::createFromFileObject(new \SplTempFileObject());
            $csv->insertOne(['id', 'download_url']);

            $entries = $sp->entries(Entry::STATUS_READY)->whereIn('id', explode(',', $video_ids))->get();

            $entries->map(function ($entry) use (&$csv) {
                $entry_video = VideoUrlService::getVideo($entry);
                $csv->insertOne([
                    $entry->id,
                    $entry_video['download_url'],
                ]);
            });

            $csv->output(date('YmdHis').'download-entry.csv');
            exit();
        }
    }
}
