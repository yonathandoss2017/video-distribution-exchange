<?php

namespace App\Http\Controllers\Manage\CP;

use Log;
use App\Models\Entry;
use League\Csv\Reader;
use League\Csv\Writer;
use App\Models\Playlist;
use App\Models\Property;
use App\Models\EntryMeta;
use App\Models\PropertyCP;
use App\Models\EntryAnalyze;
use Illuminate\Http\Request;
use App\Jobs\EntryDeletionJob;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
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
        $provider = PropertyCP::findOrFail($property_id);

        $status = $request->status;
        $playlist_id = $request->playlist;
        $playlist = $playlist_id ? Playlist::findOrFail($playlist_id) : '';
        $search = $request->input('search');
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $sort = $request->input('sort');
        $show_sort = $sort ?? 'desc';
        $statusArray = [
            Entry::STATUS_DRAFT,
            Entry::STATUS_READY,
            Entry::STATUS_PROCESSING,
            Entry::STATUS_PENDING,
            Entry::STATUS_REJECTED,
            Entry::STATUS_SCHEDULED,
            Entry::STATUS_ERROR,
        ];

        if (!config('features.content_review')) {
            $statusArray = array_diff($statusArray, [Entry::STATUS_DRAFT, Entry::STATUS_PENDING, Entry::STATUS_REJECTED]);
        }

        if ($request->filled('playlist') || $request->filled('status') || $request->filled('search') || $request->filled('start_date') || $request->filled('end_date')) {
            if ($request->filled('playlist')) {
                if (Gate::denies('manager-cp-playlist', [$property_id, $playlist])) {
                    abort(403);
                }
            }

            $videos_query = Entry::where('property_id', $property_id)
                ->when($playlist_id, function ($query) use ($playlist_id) {
                    return $query->join('entry_playlist', 'entries.id', '=', 'entry_playlist.entry_id')
                        ->where('entry_playlist.playlist_id', '=', $playlist_id);
                })->requestFilter($search, $status, $start_date, $end_date);
            $videos = $videos_query->with('platformAlivod', 'anzhengEvidence')->orderby('entries.updated_at', $show_sort)->paginate(10);
            $all_video_ids = $videos_query->pluck('id')->implode(',');

            return view('manage.cp.videos', compact('videos', 'property_id', 'playlist', 'playlist_id', 'status', 'search', 'provider', 'sort', 'statusArray', 'start_date', 'end_date', 'all_video_ids'));
        }

        //~corresponds to date added.
        $videos = Entry::with('platformAlivod', 'anzhengEvidence')
            ->where('property_id', $property_id)
            ->orderby('entries.updated_at', $show_sort)->paginate(10);
        $all_video_ids = Entry::where('property_id', $property_id)->pluck('id')->implode(',');

        return view('manage.cp.videos', compact('videos', 'property_id', 'playlist', 'playlist_id', 'status', 'search', 'provider', 'sort', 'statusArray', 'start_date', 'end_date', 'all_video_ids'));
    }

    public function delete(Request $request, $property_id, $id)
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

    public function bulkDelete(Request $request, $property_id)
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

    public function bulkExport(Request $request, $property_id)
    {
        $video_ids = $this->getVideoIds($request);

        if (empty($video_ids)) {
            session()->flash('error', __('manage/cp/contents/videos.video_metadata_export_failed'));

            return back();
        } else {
            $csv = Writer::createFromFileObject(new \SplTempFileObject());
            $csv->insertOne(['id', 'title', 'description', 'tags', 'download_url']);

            $entries = Entry::with('metadata')->where('property_id', $property_id)->whereIn('id', $video_ids)->get();

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

    public function bulkImport(Request $request, $property_id)
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|mimes:csv,txt',
        ]);
        if ($validator->fails()) {
            session()->flash('error', $validator->errors()->get('csv_file')[0]);

            return back();
        } else {
            $file = $request->file('csv_file');
            $reader = Reader::createFromPath($file->getRealPath(), 'r');
            $reader->setHeaderOffset(0);
            if ($reader->count() > 0) {
                foreach ($reader->getRecords() as $record) {
                    if (intval($record['id']) > 0 && !empty($record['title'])) {
                        $update_datas = [
                            'name' => $record['title'],
                            'description' => $record['description'],
                        ];
                        if (config('features.content_review')) {
                            $update_datas['status'] = Entry::STATUS_DRAFT;
                        }
                        Entry::where('id', $record['id'])->where('property_id', $property_id)->update($update_datas);
                    }
                    if (!empty($record['tags'])) {
                        $entry = Entry::where('id', $record['id'])->where('property_id', $property_id)->first();
                        if (!is_null($entry)) {
                            EntryMeta::updateOrCreate(
                                ['entry_id' => $record['id']],
                                ['tags' => $record['tags']]
                            );
                        }
                    }
                }
                session()->flash('success', __('manage/cp/contents/videos.video_metadata_import_success'));
            } else {
                session()->flash('error', __('manage/cp/contents/videos.video_metadata_import_failed'));
            }

            return back();
        }
    }

    public function requestReview(Request $request, $property_id)
    {
        if (!config('features.content_review')) {
            return back();
        }

        $video_ids = $this->getVideoIds($request);

        $entries = Entry::where([
            'status' => Entry::STATUS_DRAFT,
            'property_id' => $property_id,
        ])->whereIn('id', $video_ids)->get();

        foreach ($entries as $entry) {
            $entry->status = Entry::STATUS_PENDING;
            if ($entry->save()) {
                Log::info('request review video['.$entry->id.'] successfully');
            } else {
                Log::info('request review video['.$entry->id.'] unsuccessfully');
            }
        }

        session()->flash('success', __('manage/cp/contents/videos.send_video_review_requests_successfully'));

        return back();
    }

    /**
     * Start analyze on a specific entry.
     *
     * @param Request $request
     * @param $property_id
     * @param $video_id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function analyze(Request $request, $property_id, $video_id)
    {
        /*$entry = Entry::with('platformAlivod')->findOrFail($video_id);
        if ($entry->property_id != $property_id) {
            abort(403);
        }

        if (Entry::ANALYZE_STATUS_PROCESSING == $entry->analyze_status || Entry::ANALYZE_STATUS_READY == $entry->analyze_status) {
            session()->flash('error', trans('manage/cp/contents/videos.analyze_already_done'));

            return back();
        }

        $provider = PropertyCP::findOrFail($property_id);
        $entry->analyze_status = Entry::ANALYZE_STATUS_PROCESSING;
        $entry->save();

        dispatch(new \App\Jobs\Viscovery\AnalyzeVideoJob($provider, $entry));
        session()->flash('success', trans('manage/cp/contents/videos.analyze_success'));*/

        return back();
    }

    /**
     * Show analyze result with specific entry.
     *
     * @param Request $request
     * @param $property_id
     * @param $video_id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resultAnalyze(Request $request, $property_id, $video_id)
    {
        $entry = Entry::where('property_id', $property_id)->where('analyze_status', Entry::ANALYZE_STATUS_READY)->findOrFail($video_id);
        $analyze_results = EntryAnalyze::where('entry_id', $entry->id)->get();
        $analyze_results = collect($analyze_results
            ->groupBy('category_name')
            ->map(function ($item, $key) {
                return [
                    'category_name' => $key,
                    'tags_count' => $item->count(),
                    'tags' => $item->groupBy('tag_name')->map(function ($v, $k) use ($key) {
                        return [
                            'category_name' => $key,
                            'frames_count' => $v->count(),
                            'frames' => $v->map(function ($m, $n) use ($key) {
                                return [
                                    'frame_rect_percentage' => $m->frame_rect_percentage,
                                    'frame_time' => str_replace('.5', '', $m->frame_time),
                                    'milliseconds' => $this->convertToMillisecond($m->frame_time),
                                    'category_name' => $key,
                                    'tag_name' => $m->tag_name,
                                ];
                            }),
                        ];
                    }),
                ];
            })
            ->all());

        $tags = $analyze_results->pluck('tags')->toArray();
        $top_tags = collect($tags)->collapse()->sortByDesc('frames_count')->take(10);

        $property = Property::findOrFail($property_id);

        return view('manage.cp.analyze.results')
            ->with(compact('property_id', 'property', 'entry', 'top_tags', 'tags', 'analyze_results'));
    }

    private function convertToMillisecond($time)
    {
        $date = explode(':', $time);

        return ($date[0] * 3600 + $date[1] * 60 + $date[2]) * 1000;
    }

    public function getTimeFrames(Request $request, $property_id, $video_id)
    {
        $analyze_results = EntryAnalyze::where('entry_id', $video_id)
            ->where('category_name', $request->category)
            ->where('tag_name', $request->tag)
            ->orderBy('id', 'asc')->get();
        $categories = [];
        $frames = [];
        foreach ($analyze_results as $key => $analyze_result) {
            $category_name = $analyze_result['category_name'];
            $categories[$category_name] = isset($categories[$category_name]) ? $categories[$category_name] + 1 : 1;

            $tag_name = $analyze_result['tag_name'];
            $frames[] = [
                'frame_rect' => $analyze_result['frame_rect'],
                'frame_time' => str_replace('.5', '', $analyze_result['frame_time']),
                'milliseconds' => $this->convertToMillisecond($analyze_result['frame_time']),
                'category' => $category_name,
                'tag_name' => $tag_name,
            ];
        }

        $frames = $this->groupTimeFrame($frames);

        return view('manage.cp.analyze.time-frames')
            ->with(compact('property_id', 'categories', 'frames'));
    }

    public function playerShow(Request $request, Property $property, $video_id)
    {
        $entry = Entry::with('metadata')->findOrFail($video_id);

        return view('video_player.player', compact('entry', 'property'))->render();
    }
}
