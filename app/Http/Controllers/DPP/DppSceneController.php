<?php

namespace App\Http\Controllers\DPP;

use App\Models\Entry;
use App\Models\Property;
use App\Models\EntryScene;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\FeaturedImageService;
use App\Services\Storage\StorageService;
use Illuminate\Support\Facades\Validator;

class DppSceneController extends Controller
{
    public function __construct()
    {
    }

    public static function querySceneSummary($scenes)
    {
        $scene_summary = [];
        $types = EntryScene::getTypes();
        foreach ($types as $type) {
            $s = $scenes->where('type', $type);
            $scene_summary[] = [
                'type' => $type,
                'scenes_num' => $s->count(),
                'dpp_sum' => $s->sum('dpp_duration'),
            ];
        }

        return $scene_summary;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $playlist_id
     * @param $entry_id
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $playlist_id, $entry_id)
    {
        $entry = Entry::with('scenes')->findOrFail($entry_id);
        $scene_summary = $this->querySceneSummary($entry->scenes);

        return view('dpp.scene.index', compact('playlist_id', 'entry', 'scene_summary'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param $playlist_id
     * @param $entry_id
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $playlist_id, $entry_id)
    {
        $entry = Entry::with('scenes')->findOrFail($entry_id);
        $scene_summary = $this->querySceneSummary($entry->scenes);

        return view('dpp.scene.create', compact('playlist_id', 'entry', 'scene_summary'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, StorageService $storageService)
    {
        $image_path = $request->image_path;
        if ($storageService->exist($image_path)) {
            try {
                $entry_id = $request->entry_id;
                $property_id = Entry::where('id', $entry_id)->value('property_id');
                $organization_id = Property::where('id', $property_id)->value('organization_id');

                $dst_path = $organization_id.'/'.$property_id.'/entry_'.$entry_id.'/';
                $image_array = explode('/', $image_path);
                $image_oss_path = $dst_path.$image_array[count($image_array) - 1];

                $move_image_result = $storageService->move($image_path, $image_oss_path);

                if ($move_image_result) {
                    $scene = EntryScene::create([
                        'entry_id' => $request->entry_id,
                        'name' => $request->scenes_name,
                        'image_path' => $image_oss_path,
                    ]);
                    if (!is_null($scene) && !empty($scene->image_path)) {
                        $scene->image_path = FeaturedImageService::generateImageUrl($scene->image_path);
                    }

                    $response = response()->json(['statusCode' => 200, 'scene' => $scene]);
                }
            } catch (\Exception $exception) {
                $response = response()->json(['statusCode' => 403, 'message' => __('dpp.created_failed')]);
            }
        } else {
            $response = response()->json(['statusCode' => 403, 'message' => __('dpp.upload_failed')]);
        }

        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($playlist_id, $entry_id, $id)
    {
        $scene = EntryScene::findOrFail($id);

        return view('dpp.scene.show', compact('playlist_id', 'entry_id', 'scene'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $playlist_id
     * @param $entry_id
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($playlist_id, $entry_id, $id)
    {
        $scene = EntryScene::findOrFail($id);

        return view('dpp.scene.edit', compact('playlist_id', 'entry_id', 'scene'));
    }

    private function strToSeconds($formatTime)
    {
        $timeExploded = explode(':', $formatTime);
        if (3 == count($timeExploded)) {
            return $timeExploded[0] * 3600 + $timeExploded[1] * 60 + $timeExploded[2];
        }

        return $timeExploded[0] * 60 + $timeExploded[1];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param $playlist_id
     * @param $entry_id
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $playlist_id, $entry_id, $id)
    {
        $messages = [
            'end_in_seconds.min' => 'Scene End Time must larger than Scene Start Time',
        ];
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'start' => 'required',
            'end' => 'required',
            'start_in_seconds' => 'required|integer|min:0',
            'end_in_seconds' => 'required|integer|min:'.($request->start_in_seconds + 1),
            'dpp_duration' => 'required|not_in:00:00:00',
            'type' => 'required',
            'locale' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        $scene = EntryScene::findOrFail($id);
        $scene->name = $request->input('name');
        $scene->start_in_seconds = $this->strToSeconds($request->input('start'));
        $scene->end_in_seconds = $this->strToSeconds($request->input('end'));
        $scene->dpp_duration = $this->strToSeconds($request->input('dpp_duration'));
        $scene->type = $request->input('type');
        $scene->locale = $request->input('locale');
        $scene->save();
        session()->flash('success', trans('dpp.update_success'));

        return redirect()->route('dpp.request.scene.create', [$playlist_id, $entry_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $playlist_id, $entry_id, $id)
    {
        $scene = EntryScene::findOrFail($id);

        $scene->delete();

        session()->flash('success', __('dpp.delete_success'));

        return back();
    }
}
