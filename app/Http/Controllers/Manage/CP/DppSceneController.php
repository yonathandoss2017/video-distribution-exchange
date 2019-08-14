<?php

namespace App\Http\Controllers\Manage\CP;

use App\Models\Entry;
use App\Models\Playlist;
use App\Models\EntryScene;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class DppSceneController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $property_id
     * @param $playlist_id
     * @param $entry_id
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $property_id, $playlist_id, $entry_id)
    {
        $playlist = Playlist::where('property_id', $property_id)->findOrFail($playlist_id);

        $queryType = $request->input('type');
        $entry = Entry::with('scenes')->where('property_id', $property_id)->findOrFail($entry_id);
        $scenes = [
            'total' => 0,
            'total_dpp' => 0,
            'types' => [],
            'scenes' => collect([]),
        ];
        if (Entry::DPP_STATUS_PROCESSING != $entry->dpp_status) {
            $scenes = [
                'total' => $entry->scenes->count(),
                'total_dpp' => $entry->scenes->sum('dpp_duration'),
                'scenes' => $entry->scenes,
            ];
        }

        $types = EntryScene::getTypes();
        foreach ($types as $type) {
            if (Entry::DPP_STATUS_PROCESSING != $entry->dpp_status) {
                $s = $entry->scenes->where('type', $type);
                $scenes['types'][$type] = [$s->count(), $s->sum('dpp_duration')];
                if ($queryType == $type) {
                    $scenes['scenes'] = $s;
                }
            } else {
                $scenes['types'][$type] = [0, 0];
            }
        }
        $locales = $entry->scenes->groupBy('locale');
        $res = [];
        foreach ($locales as $locale => $items) {
            if (Entry::DPP_STATUS_PROCESSING != $entry->dpp_status) {
                $types = $items->groupBy('type');
                foreach ($types as $type) {
                    if (!empty($locale)) {
                        $res[] = [
                            'locale' => $locale,
                            'type' => $type->pluck('type')->first(),
                            'dpp_sum' => $type->sum('dpp_duration'),
                        ];
                    }
                }
            } else {
                $locales = '';
            }
        }
        $locales = $this->paginate($res, 10, null, ['path' => Paginator::resolveCurrentPath()]);
        if ($request->ajax()) {
            return response()->json(view('manage.cp.dpp_scenes.locales', ['locales' => $locales])->render());
        }

        return view('manage.cp.dpp_scenes.index', compact('property_id', 'playlist_id', 'entry', 'scenes', 'locales'));
    }

    private function paginate($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    /**
     * Display the specified resource.
     *
     * @param $property_id
     * @param $playlist_id
     * @param $entry_id
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($property_id, $playlist_id, $entry_id, $id)
    {
        $this->checkPlaylistAndEntry($property_id, $playlist_id, $entry_id);

        $scene = EntryScene::where('entry_id', $entry_id)->findOrFail($id);

        return view('manage.cp.dpp_scenes.show', compact('property_id', 'playlist_id', 'entry_id', 'scene'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $property_id
     * @param $playlist_id
     * @param $entry_id
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($property_id, $playlist_id, $entry_id, $id)
    {
        $this->checkPlaylistAndEntry($property_id, $playlist_id, $entry_id);

        $scene = EntryScene::where('entry_id', $entry_id)->findOrFail($id);

        return view('manage.cp.dpp_scenes.edit', compact('property_id', 'playlist_id', 'entry_id', 'scene'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param $property_id
     * @param $playlist_id
     * @param $entry_id
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $property_id, $playlist_id, $entry_id, $id)
    {
        $this->checkPlaylistAndEntry($property_id, $playlist_id, $entry_id);

        $scene = EntryScene::where('entry_id', $entry_id)->findOrFail($id);
        $scene->suitable = $request->input('suitable_verticals');
        $scene->blacklist = $request->input('blacklisted_verticals');
        $scene->description = $request->input('description');
        $scene->keywords = $request->input('keywords');
        $scene->save();
        session()->flash('success', trans('manage/cp/ivideoadd/ivideoadd.update_success'));

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($property_id, $playlist_id, $entry_id, $id)
    {
        $this->checkPlaylistAndEntry($property_id, $playlist_id, $entry_id);

        $scene = EntryScene::where('entry_id', $entry_id)->findOrFail($id);
        $scene->delete();
        session()->flash('success', trans('manage/cp/ivideoadd/ivideoadd.msg_successfully_deleted'));

        return back();
    }

    private function checkPlaylistAndEntry($property_id, $playlist_id, $entry_id)
    {
        $playlist = Playlist::where('property_id', $property_id)->findOrFail($playlist_id);
        $entry = Entry::where('property_id', $property_id)->findOrFail($entry_id);
    }
}
