<?php

namespace App\Http\Controllers\Manage\CP;

use Exception;
use Validator;
use App\Models\User;
use App\Models\Language;
use App\Models\Playlist;
use App\Models\Property;
use App\Models\PropertyCP;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Services\PlaylistService;
use App\Models\TermsOfMarketplace;
use Illuminate\Support\Facades\DB;
use Webpatser\Countries\Countries;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\FeaturedImageService;

class PlaylistController extends Controller
{
    public function __construct(Request $request)
    {
        if (null != $request->route('playlist')) {
            $this->middleware('can:manager-cp-playlist,property,playlist')->except('destroy');
        }
        $this->middleware('can:create-cp-playlist,property', ['only' => ['create', 'store']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $property_id Property id
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $property_id)
    {
        $search = $request->input('search');
        $sort = $request->input('sort');
        $show_sort = $sort ?? 'desc';

        $playlist_query = Playlist::where('property_id', $property_id)->when($search, function ($query) use ($search) {
            return $query->where('name', 'like', '%'.$search.'%');
        });

        $all_playlist_query = clone $playlist_query;

        $playlists = $playlist_query->withCount('entries')
            ->orderby('updated_at', $show_sort)
            ->paginate(10);

        $all_playlist_ids = $all_playlist_query->pluck('id')->implode(',');

        return view('manage.cp.playlist.index', compact('property_id', 'playlists', 'sort', 'search', 'all_playlist_ids'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param $property_id property id
     *
     * @return \Illuminate\Http\Response
     */
    public function create($property_id)
    {
        $property = PropertyCP::findOrFail($property_id);

        $other_option = ['others' => __('manage/cp/contents/playlists.others')];
        $languages = Language::select('code', 'name')
            ->distinct()
            ->orderBy('name')
            ->pluck('name', 'code')->toArray();
        $languages = array_merge($languages, $other_option, __('language'));

        $countries_keys = Countries::pluck('iso_3166_2')->toArray();
        $countries_keys = array_flip($countries_keys);
        $countries = array_merge($countries_keys, __('country'));
        asort($countries); // sort value by alphabetical
        $countries = array_merge($countries, $other_option);
        $genres = [];
        collect(config('enums.genre'))->keys()->each(function ($genre) use (&$genres) {
            $genres[$genre] = __('manage/cp/contents/playlists.'.$genre);
        });

        return view('manage.cp.playlist.create', compact('property_id', 'countries', 'languages', 'genres'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param $property_id property id
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $property_id, FeaturedImageService $imageService)
    {
        $property = PropertyCP::findOrFail($property_id);
        $validator = $this->runValidate($property, $request);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        DB::beginTransaction();
        try {
            $playlist = $this->process($property, new Playlist(), $request);

            //store image
            $imageFile = $imageService->store($request, FeaturedImageService::getImageSavePath([
                'property_id' => $property_id,
                'playlist_id' => $playlist->id,
            ]));
            if ('error' == $imageFile) {
                DB::rollBack();

                return back()->withInput();
            }
            if (!is_null($imageFile)) {
                $playlist->thumbnail_path = $imageFile;
                $playlist->save();
            }

            session()->flash('success', trans('manage/cp/contents/playlists.playlist_is_created_successfully'));
            DB::commit();
        } catch (Exception $e) {
            session()->flash('error', trans('manage/cp/contents/playlists.playlist_is_created_failed'));
            DB::rollback();
        }

        return redirect(route('manage.cp.playlists.index', [$property_id]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $property_id
     * @param Playlist $playlist
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($property_id, Playlist $playlist)
    {
        $property = PropertyCP::findOrFail($property_id);

        $other_option = ['others' => __('manage/cp/contents/playlists.others')];
        $languages = Language::select('code', 'name')
            ->distinct()
            ->orderBy('name')
            ->pluck('name', 'code')
            ->toArray();
        $languages = array_merge($languages, $other_option, __('language'));

        $countries_keys = Countries::pluck('iso_3166_2')->toArray();
        $countries_keys = array_flip($countries_keys);
        $countries = array_merge($countries_keys, __('country'));
        asort($countries); // sort value by alphabetical
        $countries = array_merge($countries, $other_option);
        $genres = [];
        collect(config('enums.genre'))->keys()->each(function ($genre) use (&$genres) {
            $genres[$genre] = __('manage/cp/contents/playlists.'.$genre);
        });

        return view('manage.cp.playlist.edit', compact('property_id', 'playlist', 'languages', 'countries', 'genres'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param $property_id property id
     * @param Playlist $playlist
     *
     * @return \Illuminate\Http\Response
     *
     * @internal param int $id
     */
    public function update(Request $request, $property_id, Playlist $playlist, FeaturedImageService $imageService)
    {
        if ($request->isQuickEdit) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => __('manage/cp/common.title_required'),
                    'success' => false,
                ], 400);
            }

            $playlist->name = $request->name;
            $playlist->save();

            return response()->json([
                'message' => 'success',
                'success' => true,
            ]);
        }
        $property = PropertyCP::findOrFail($property_id);
        $validator = $this->runValidate($property, $request, true);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        $imageFile = $imageService->update($request, $playlist->thumbnail_path, FeaturedImageService::getImageSavePath([
            'property_id' => $property_id,
            'playlist_id' => $playlist->id,
        ]));
        if ('error' == $imageFile) {
            return back()->withInput();
        }

        if (!is_null($imageFile)) {
            $request->offsetSet('thumbnail_path', $imageFile);
        }
        $playlist = $this->process($property, $playlist, $request, true);

        session()->flash('success', trans('manage/cp/contents/playlists.playlist_is_updated_successfully'));

        return redirect(route('manage.cp.playlists.index', [$property_id]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $property_id)
    {
        $playlist_ids = explode(',', $request->get('playlist_id'));
        $select_all = $request->select_all;
        $search = $request->search;
        if (1 == $select_all) {
            $playlist_ids = explode(',', $request->all_playlist_ids);
        }

        $result = 1;
        foreach ($playlist_ids as $playlist_id) {
            $callback = PlaylistService::deletePlaylist($playlist_id);
            if ('success' != $callback['status']) {
                $result = 0;
            }
        }

        if ($result) {
            session()->flash('success', trans('manage/cp/contents/playlists.playlists_are_destroyed'));
        } else {
            session()->flash('error', trans('manage/cp/contents/playlists.some_playlist_delete_failed'));
        }

        return back();
    }

    protected function getPlaylistWithTerm($playlist_id)
    {
        return Playlist::with('content_provider')
            ->with(['content_provider.terms' => function ($query) {
                $query->where('allowed', 1);
            }])
            ->findOrFail($playlist_id);
    }

    protected function spIdRule()
    {
        return ['sp_id' => 'required'];
    }

    protected function ErrorMsgCustomSp()
    {
        return __('manage/cp/contents/playlists.no_properties_is_selected');
    }

    /**
     * Handle Validation of Playlist.
     *
     * @param \App\Models\PropertyCP   $property
     * @param \Illuminate\Http\Request $request
     * @param bool                     $isEdit
     *
     * @return \Illuminate\Validation\Validator
     */
    protected function runValidate(PropertyCP $property, Request $request, $isEdit = false)
    {
        $validator = Validator::make($request->all(), PlaylistService::newPlaylistValidator());

        return $validator;
    }

    /**
     * Handle Create or Update Playlist.
     *
     * @param \App\Models\PropertyCP   $property
     * @param \App\Models\Playlist     $playlist
     * @param \Illuminate\Http\Request $request
     * @param bool                     $isEdit
     *
     * @return \App\Models\Playlist
     */
    protected function process(PropertyCP $property, Playlist $playlist, Request $request, $isEdit = false)
    {
        $inputData = $request->all();
        $inputData['user_id'] = Auth::user()->id;
        $inputData['publish'] = false;
        $inputData['publish_start_date'] = null;
        $inputData['publish_end_date'] = null;
        $inputData['publish_status'] = Playlist::PUBLISH_STATUS_UNPUBLISH;

        $playlist->fill($inputData);
        if (!$isEdit) {
            $playlist->contentProvider()->associate($property->id);
        }
        if (config('features.content_review')) {
            if (isset($inputData['is_submit']) && $inputData['is_submit']) {
                $playlist->status = Playlist::STATUS_PENDING;
            } else {
                $playlist->status = Playlist::STATUS_DRAFT;
            }
        } else {
            if (Playlist::STATUS_READY != $playlist->status) {
                $playlist->status = Playlist::STATUS_READY;
            }
        }
        $playlist->save();

        return $playlist;
    }

    public function getPlaylists($property_id)
    {
        $playlists = Playlist::select('id', 'name')->where('property_id', $property_id)->where('status', Playlist::STATUS_READY)->orderby('name')->get();

        return response()->json([
            'status' => 'success',
            'playlists' => $playlists,
        ]);
    }

    public function requestReview(Request $request, $property_id)
    {
        $playlist_ids = explode(',', $request->get('playlist_ids'));
        $select_all = $request->select_all;
        $search = $request->search;
        if (1 == $select_all) {
            $playlist_ids = explode(',', $request->all_playlist_ids);
        }

        $review_result = Playlist::where([
                'property_id' => $property_id,
                'status' => Playlist::STATUS_DRAFT,
            ])->whereIn('id', $playlist_ids)
            ->update(['status' => Playlist::STATUS_PENDING]);

        if ($review_result) {
            session()->flash('success', trans('manage/cp/contents/playlists.send_playlist_review_requests_successfully'));
        } else {
            session()->flash('error', trans('manage/cp/contents/playlists.send_playlist_review_requests_unsuccessfully'));
        }

        return back();
    }

    public function publish($property_id, Playlist $playlist)
    {
        $terms = TermsOfMarketplace::where('property_id', $property_id)->pluck('name', 'id');

        return view('manage.cp.playlist.publish', compact('property_id', 'playlist', 'terms'));
    }

    public function updatePublish(Request $request, $property_id, Playlist $playlist)
    {
        if ('on' == $request->radio_publish) {
            $validator = Validator::make($request->all(), ['radio_publish' => 'required', 'marketplace_terms' => 'required']);
            $validator = PlaylistService::validatePlaylistPublished($validator, $request);
            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator);
            }
            TermsOfMarketplace::where('property_id', $property_id)->findOrFail($request->marketplace_terms);
        }

        $inputData = $request->all();
        $inputData['user_id'] = Auth::user()->id;
        $inputData['publish'] = false;
        $inputData['publish_start_date'] = null;
        $inputData['publish_end_date'] = null;
        $inputData['tom_id'] = null;

        if ($request->is_submit) {
            $inputData['publish_status'] = Playlist::PUBLISH_STATUS_REVIEW;
        } else {
            $inputData['publish_status'] = Playlist::PUBLISH_STATUS_UNPUBLISH;
        }

        session()->flash('success', trans('manage/cp/contents/playlists.playlist_is_unpublished_successfully'));

        if ('on' == $request->radio_publish && Playlist::STATUS_READY == optional($playlist)->status) {
            $inputData['publish'] = true;
            if (empty($request->available_now)) {
                $inputData['publish_start_date'] = Carbon::parse($request->publish_start_date)->toDateTimeString();
            } else {
                $inputData['publish_start_date'] = null;
            }

            if (empty($request->until_forever)) {
                $inputData['publish_end_date'] = Carbon::parse($request->publish_end_date)->hour(23)->minute(59)->second(59)->toDateTimeString();
            } else {
                $inputData['publish_end_date'] = null;
            }

            $inputData['tom_id'] = $inputData['marketplace_terms'];

            session()->flash('success', trans('manage/cp/contents/playlists.playlist_is_published_successfully'));
        }

        $playlist->fill($inputData);
        $playlist->save();

        return redirect(route('manage.cp.playlists.index', [$property_id]));
    }
}
