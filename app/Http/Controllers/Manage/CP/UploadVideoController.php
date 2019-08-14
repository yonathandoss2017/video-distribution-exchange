<?php

namespace App\Http\Controllers\Manage\CP;

use Auth;
use File;
use Session;
use Validator;
use Carbon\Carbon;
use App\Models\Entry;
use App\Models\Language;
use App\Models\Playlist;
use App\Models\Property;
use App\Models\EntryMeta;
use App\Events\VideoUpdated;
use Illuminate\Http\Request;
use App\Models\PlatformAlivod;
use App\Services\Vod\VodService;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Jobs\Vod\Ali\VideoIngestJob;
use Illuminate\Support\Facades\Gate;
use App\Services\FeaturedImageService;
use App\Models\PlatformAlivodTranscode;
use App\Services\Serve\VideoUrlService;
use App\Repositories\PlaylistRepository;
use App\Services\Storage\Oss\UrlService;
use App\Services\Storage\StorageService;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadVideoController extends Controller
{
    protected $playlistRepository;

    public function __construct(PlaylistRepository $playlistRepository)
    {
        $this->playlistRepository = $playlistRepository;
        $this->middleware('can:upload-video,property', ['only' => ['index', 'store']]);
    }

    public function index(Request $request, $property_id)
    {
        $playlists = $this->playlistRepository->getAllPlaylistsByPropertyId($property_id)->pluck('name', 'id');
        $subtitles = Language::select('code', 'name')
            ->distinct() //cause ivs_code can have duplicate
            ->orderBy('name')
            ->get()
            ->pluck('name', 'code');

        foreach ($subtitles as $key => $subtitle) {
            $subtitles[$key] = __('language.'.$key);
        }

        $data = [
            'property_id' => $property_id,
            'playlists' => $playlists,
            'subtitles' => $subtitles,
        ];

        return view('manage.cp.new_video', $data);
    }

    public function errorMessage($info, $obj)
    {
        if ('type' == $info) {
            $message = __('manage/cp/contents/videos.file_specified_is_invalid', ['ucfirst_obj' => ucfirst($obj)]);
        } elseif ('url' == $info) {
            $message = __('manage/cp/contents/videos.url_specified_is_not_a_valid', ['ucwords_obj' => ucwords($obj)]);
        } elseif ('notfound' == $info) {
            $message = __('manage/cp/contents/videos.specified_cannot_be_found', ['ucwords_obj' => ucwords($obj)]);
        } elseif ('length' == $info && 'image' == $obj) {
            $message = __('manage/cp/contents/videos.image_is_too_large');
        }

        return ['status' => 'error', 'message' => $message];
    }

    public function getRules($video_method, $img_method)
    {
        $rules['video_method'] = 'required';
        $rules['title'] = 'required|string|max:256|min:1';
        $rules['playlist'] = 'required';
        if ('zh' == App::getLocale()) {
            $rules['produced_at'] = 'date_format:Y年m月d日';
        } elseif ('en' == App::getLocale()) {
            $rules['produced_at'] = 'date_format:m/d/Y';
        }
        $rules['img_method'] = 'required';

        if (Entry::VIDEO_OSS_URL == $video_method) {
            $rules['video_url'] = ['required', 'url', 'regex:/\.(3gp|asf|avi|dat|dv|flv|f4v|gif|m2t|m4v|mj2|mjpeg|mkv|mov|mp4|mpe|mpg|mpeg|mts|ogg|qt|rm|rmvb|swf|ts|vob|wmv|webm)$/i'];
        } elseif (Entry::VIDEO_DIRECT_UPLOAD == $video_method) {
            $rules['video_path'] = ['required', 'regex:/\.(3gp|asf|avi|dat|dv|flv|f4v|gif|m2t|m4v|mj2|mjpeg|mkv|mov|mp4|mpe|mpg|mpeg|mts|ogg|qt|rm|rmvb|swf|ts|vob|wmv|webm)$/i'];
        }

        if ('img_direct' == $img_method) {
            $rules['imagefile'] = 'required|mimes:jpeg,png|max:1024|dimensions:max_width=1920,max_height=1080';
        }

        return $rules;
    }

    private function isPlaylistReady($playlist_ids)
    {
        return 0 == Playlist::whereIn('id', $playlist_ids)->where('status', '!=', Playlist::STATUS_READY)->count();
    }

    public function store(Request $request, $property_id, VodService $vodService)
    {
        $description = $request->description;
        $video_upload = $request->video_method;
        $image_upload = $request->img_method;

        $rules = $this->getRules($video_upload, $image_upload);
        $validator = Validator::make($request->all(), $rules, [
            'video_method.required' => __('manage/cp/contents/videos.video_upload_is_required'),
            'img_method.required' => __('manage/cp/contents/videos.featured_image_upload_is_required'),
            'video_path.required' => __('manage/cp/contents/videos.direct_upload_field_is_required'),
            'video_url.required' => __('manage/cp/contents/videos.video_url_field_is_required'),
        ]);

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        if (!$this->isPlaylistReady($request->playlist)) {
            session()->flash('error', __('manage/cp/contents/videos.playlist_is_not_ready'));

            return back()->withInput();
        }

        if ($request->produced_at) {
            $produced_at = str_replace(['年', '月', '日'], '-', $request->produced_at);
            $request->produced_at = Carbon::parse(rtrim($produced_at, '-'))->toDateString();
        } else {
            $request->produced_at = null;
        }

        $thumbnail_url = '';
        $thumbnail_cover_id = '';
        if ('img_direct' == $image_upload && $request->hasFile('imagefile')) {
            $result = $this->uploadImageToVod($request->file('imagefile'), $vodService);
            if ('error' == $result) {
                return back()->withInput();
            } elseif (is_array($result)) {
                $thumbnail_url = $result['thumbnail_url'];
                $thumbnail_cover_id = $result['cover_id'];
            }
        }

        $metaAttributes = [];
        if ($request->filled('tags')) {
            $metaAttributes = [
                'tags' => $request->tags,
            ];
        }

        $entry = Entry::createWithPlatform(
            $property_id,
            Auth::user()->id,
            [
                'name' => $request->title,
                'description' => (isset($description) && !empty(trim($description)) ? trim($description) : null),
                'duration' => 0,
                'published_at' => Carbon::now(),
                'status' => Entry::STATUS_PROCESSING,
                'thumbnail_url' => $thumbnail_url,
                'produced_at' => $request->produced_at,
            ],
            $metaAttributes,
            Entry::PLATFORM_ALIVOD,
            [
                'status' => PlatformAlivod::STATUS_PROCESSING,
                'cover_id' => $thumbnail_cover_id,
            ]
        );

        if ($entry) {
            if (Entry::VIDEO_OSS_URL == $video_upload) {
                $video_source_url = $request->video_url;
            } else {
                $video_source_url = UrlService::getUrl($request->video_path);
            }
            VideoIngestJob::dispatch($entry, $video_source_url);

            $entry->addToPlaylist($request->playlist);

            session()->flash('success', __('manage/cp/contents/videos.video_is_being_imported'));
        } else {
            session()->flash('error', __('manage/cp/contents/videos.video_import_failed'));
        }

        return redirect(route('manage.cp.videos', $property_id));
    }

    public function edit(Request $request, $property_id, $id)
    {
        $video = Entry::with('playlists')
            ->with(['metadata', 'platformAlivodTranscodes' => function ($query) {
                $query->whereIn('platform_alivod_transcodes.status', [PlatformAlivodTranscode::STATUS_SUCCESS, PlatformAlivodTranscode::STATUS_NORMAL])
                    ->where('format', 'm3u8')
                    ->orderBy('bitrate', 'desc');
            }])
            ->withPlatformVideos()
            ->find($id);

        if ($video) {
            $playlists = $this->playlistRepository->getAllPlaylistsByPropertyId($property_id);

            $platform = VideoUrlService::getVideo($video);

            $downloadable = false;
            if (in_array($platform['platform'], [Entry::PLATFORM_ALIVOD])) {
                if (optional($video->platformAlivod)->source_url) {
                    $downloadable = true;
                }
            }

            $property = Property::findOrFail($property_id);
            $data = [
                'property_id' => $property_id,
                'property' => $property,
                'video' => $video,
                'playlists' => $playlists,
                'downloadable' => $downloadable,
                'downloadUrl' => $video->source_url,
                'videoStreamUrl' => null,
                'aiReviewVideoResults' => null,
            ];

            if (Gate::allows('view-ai-review', $property)) {
                $data['aiReviewVideoResults'] = $video->aiReviewVideoResult()->paginate(10);
            }

            return view('manage.cp.video.edit', $data);
        } else {
            session()->flash('error', __('manage/cp/contents/videos.video_not_found'));

            return back()->withInput();
        }
    }

    public function update(Request $request, $property_id, $id, VodService $vodService)
    {
        if ($request->isQuickEdit) {
            $validator = Validator::make($request->all(), [
                'title' => 'required|max:255|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => __('manage/cp/common.title_required'),
                    'success' => false,
                ], 400);
            }

            $entry = Entry::find($id);
            $entry->name = $request->title;
            $entry->save();

            return response()->json([
                'message' => 'success',
                'success' => true,
            ]);
        }

        $rules = [
            'title' => 'required|max:255|min:1',
            'playlist' => 'required',
            'imagefile' => 'mimes:jpeg,png|max:1024|dimensions:max_width=1920,max_height=1080',
        ];
        if ('zh' == App::getLocale()) {
            $rules['produced_at'] = 'date_format:Y年m月d日';
        } elseif ('en' == App::getLocale()) {
            $rules['produced_at'] = 'date_format:m/d/Y';
        }

        $validator = Validator::make($request->all(), $rules);

        $entry = Entry::with([
            'metadata',
            'platformAlivod',
            'platformAlivodTranscodes' => function ($query) {
                $query->whereIn('platform_alivod_transcodes.status', [PlatformAlivodTranscode::STATUS_SUCCESS, PlatformAlivodTranscode::STATUS_NORMAL])
                    ->where('format', 'm3u8')
                    ->orderBy('bitrate', 'desc');
            },
        ])->findOrFail($id);

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        if (!$this->isPlaylistReady($request->playlist)) {
            session()->flash('error', __('manage/cp/contents/videos.playlist_is_not_ready'));

            return back()->withInput();
        }

        if ($request->hasFile('imagefile')) {
            $result = true;
            if ($entry->hasPlatform(Entry::PLATFORM_ALIVOD)) {
                if ($entry->platformAlivod->video_id) {
                    $result = $this->updateByAliVod($entry, $request, $vodService);
                }
            }
            if (!$result) {
                return back()->withInput();
            }
        }

        $entry->name = $request->title;
        $entry->description = $request->description;
        $entry->published_at = Carbon::now();
        $entry->produced_at = $request->produced_at;
        $entry->playlists()->sync($request->playlist);
        if (config('features.content_review')) {
            if ($request->is_submit) {
                if (Entry::STATUS_REJECTED == $entry->status
                    || Entry::STATUS_DRAFT == $entry->status
                    || Entry::STATUS_READY == $entry->status
                    || Entry::STATUS_SCHEDULED == $entry->status
                ) {
                    $entry->status = Entry::STATUS_PENDING;
                }
            } else {
                if (Entry::STATUS_REJECTED == $entry->status
                    || Entry::STATUS_READY == $entry->status
                    || Entry::STATUS_PENDING == $entry->status
                    || Entry::STATUS_SCHEDULED == $entry->status
                ) {
                    $entry->status = Entry::STATUS_DRAFT;
                }
            }
        }
        if ($entry->platformAlivodTranscodes->count() > 0) {
            $entry->price_note = $request->price_note;
            foreach ($entry->platformAlivodTranscodes as $alivodTranscode) {
                $key = 'price_'.$alivodTranscode->definition;
                if (null !== $request->input($key)) {
                    $alivodTranscode->price = '' == $request->input($key) ? null : $request->input($key);
                    $alivodTranscode->save();
                }
            }
        }
        $entry->save();

        if ($request->filled('tags')) {
            if ($entry->metadata) {
                $video_meta = $entry->metadata;
            } else {
                $video_meta = new EntryMeta(['entry_id' => $entry->id]);
            }
            $video_meta->tags = $request->tags;
            $video_meta->save();
        } else {
            //user not fill tags and have existing metadata record
            if ($entry->metadata) {
                $entry->metadata->tags = null;
                $entry->metadata->save();
            }
        }
        if (Entry::STATUS_READY == $entry->status) {
            event(new VideoUpdated($id, true));
        }

        session()->flash('success', __('manage/cp/contents/videos.video_updated_successfully'));

        return redirect(route('manage.cp.video.edit', [$property_id, $id]));
    }

    private function updateByAliVod(Entry $entry, $request, $vodService)
    {
        $result = $this->uploadImageToVod($request->file('imagefile'), $vodService);
        if (is_array($result)) {
            $entry->thumbnail_url = $result['thumbnail_url'];

            $entry->platformAlivod->cover_id = $result['cover_id'];
            $entry->platformAlivod->save();

            return false !== $vodService->updateVideoInfo($entry->platformAlivod->video_id, [
                'name' => $request->title,
                'thumbnail_url' => $result['thumbnail_url'],
            ]);
        }

        return false;
    }

    /**
     * Downlaod video file.
     *
     * @param $propertyId
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($propertyId, $id)
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

    public function storeVideo(Request $request, StorageService $storageService)
    {
        if ($request->hasFile('qqfile')) {
            $file = $request->file('qqfile');
            $pathFile = 'tmp';

            $contractFile = $storageService->store($file, $pathFile);

            if (200 == $contractFile->getData()->statusCode) {
                $video_tmp_path = $contractFile->getData()->pathImg;
                session(['video_tmp_path' => $video_tmp_path]);

                return [
                    'success' => true,
                    'message' => __('manage/cp/contents/videos.upload_success'),
                    'data' => [
                        'video_url' => FeaturedImageService::generateImageUrl($video_tmp_path),
                    ],
                ];
            } else {
                return [
                    'success' => false,
                    'message' => __('manage/cp/contents/videos.upload_fail'),
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => __('manage/cp/contents/videos.no_upload_fail'),
            ];
        }
    }

    public function deleteObject(Request $request, StorageService $storageService)
    {
        $storageService->delete(session('video_tmp_path'));

        $request->session()->forget('video_tmp_path');
    }

    public function requestUpload(Request $request, StorageService $storageService)
    {
        $filename = $request->get('filename');

        return array_merge($storageService->requestUploadPolicy(), ['filename' => md5($filename.time().rand(1, 10000)).'.'.pathinfo($filename)['extension']]);
    }

    private function uploadImageToVod(UploadedFile $image, $vodService)
    {
        $response = $vodService->uploadLocalImage($image);
        if ($response && $response->ImageId) {
            return [
                'cover_id' => $response->ImageId,
                'thumbnail_url' => $response->ImageURL,
            ];
        } else {
            session()->flash('error', __('manage/cp/contents/videos.image_upload_fail'));

            return 'error';
        }
    }
}
