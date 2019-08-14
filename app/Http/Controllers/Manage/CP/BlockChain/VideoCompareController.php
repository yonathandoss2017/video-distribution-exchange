<?php

namespace App\Http\Controllers\Manage\CP\BlockChain;

use App\Models\Entry;
use App\Models\PropertyCP;
use App\Models\VideoCompare;
use Illuminate\Http\Request;
use App\Models\EntryFingerprint;
use App\Models\VideoCompareResult;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Services\Blockchain\VideoCompareService;

class VideoCompareController extends Controller
{
    const LOG_TAG = '[Video:Fingerprint Contrast]: ';

    public function index(Request $request, $property_id)
    {
        $status = $request->get('status');
        $videoCompares = VideoCompare::withCount('compareResults')
            ->where('property_id', $property_id)
            ->when($status, function ($query) use ($status) {
                $query->where('status', array_search($status, VideoCompare::STATUS));
            })
            ->orderby('created_at', 'desc')
            ->paginate(10);

        return view('manage.cp.video-compare.index', compact('property_id', 'videoCompares'));
    }

    public function create(Request $request, $property_id)
    {
        return view('manage.cp.video-compare.create', compact('property_id'));
    }

    public function store(Request $request, $property_id, VideoCompareService $compareService)
    {
        $rules = ['title' => 'required|string|max:256|min:1'];
        if (Entry::VIDEO_OSS_URL == $request->video_method) {
            $rules['video_url'] = ['required', 'url', 'regex:/\.(mpg|asf|avi|wav|swf|flv|rm|mov|mp4|dv|mkv)$/i'];
        } elseif (Entry::VIDEO_DIRECT_UPLOAD == $request->video_method) {
            $rules['video_path'] = ['required', 'regex:/\.(mpg|asf|avi|wav|swf|flv|rm|mov|mp4|dv|mkv)$/i'];
        }

        $validator = Validator::make($request->all(), $rules, [
            'video_method.required' => __('manage/cp/contents/videos.video_upload_is_required'),
            'video_path.required' => __('manage/cp/contents/videos.direct_upload_field_is_required'),
            'video_url.required' => __('manage/cp/contents/videos.video_url_field_is_required'),
        ]);

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        $property = PropertyCP::findOrFail($property_id);
        $res = $compareService->requestVideoCompare($property, $request);
        if ($res) {
            return redirect(route('manage.cp.block-chain.video-compare.index', $property_id));
        } else {
            return back()->withInput();
        }
    }

    public function show(Request $request, $property_id, $id)
    {
        $videoCompare = VideoCompare::withCount('compareResults')->where('property_id', $property_id)->where('status', VideoCompare::STATUS_FINISHED)->findOrFail($id);

        $compareResults = VideoCompareResult::with(['entry', 'matchFragments'])->where('compare_id', $videoCompare->id)->paginate(10);

        if ($request->isXmlHttpRequest()) {
            return view('manage.cp.video-compare.show_videos', compact('compareResults'))->render();
        } else {
            $videoCompare->load([
                'compareResults' => function ($q) {
                    $q->withCount('matchFragments');
                }, 'compareResults.matchFragments',
            ]);

            $match_fragment_count = 0;
            $match_duration_count = 0;
            $match_max_duration = 0;
            $match_max_fragment = 0;
            $max_similarity = 0;
            if ($videoCompare->compareResults->count() > 0) {
                $similarities = [];
                $match_durations = [];
                $match_fragment_counts = [];
                foreach ($videoCompare->compareResults as $item) {
                    $similarities[] = $item->confidence;
                    $match_fragment_counts[] = $item->match_fragments_count;
                    $item->matchFragments->each(function ($match_fragment) use (&$match_durations) {
                        $match_durations[] = $match_fragment->duration_ms;
                    });
                }
                $match_fragment_count = array_sum($match_fragment_counts);
                $match_duration_count = array_sum($match_durations);
                $match_max_duration = max($match_durations);
                $match_max_fragment = max($match_fragment_counts);
                $max_similarity = max($similarities);
            }

            $compare_video_count = Entry::whereHas('fingerprint', function ($q) {
                $q->where('status', EntryFingerprint::STATUS_FINGERPRINT_EXTRACTION_SUCCESS);
            })->where('property_id', $property_id)->count();

            return view('manage.cp.video-compare.show', compact('property_id', 'videoCompare', 'compareResults', 'match_fragment_count', 'compare_video_count', 'match_duration_count', 'match_max_duration', 'match_max_fragment', 'max_similarity'));
        }
    }
}
