<?php

namespace App\Http\Controllers\Marketplace\V1;

use Carbon\Carbon;
use App\Models\Entry;
use App\Models\Playlist;
use App\Models\UserCart;
use App\Models\PropertyCP;
use App\Models\PropertySP;
use App\Models\RequestLog;
use Illuminate\Http\Request;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\models\EntryAnzhengEvidence;
use Illuminate\Support\Facades\Auth;
use App\Models\PlatformAlivodTranscode;
use App\Services\Solr\SolrQueryService;
use App\Services\Serve\VideoImageService;
use App\Http\Resources\UserCartCollection;
use App\Jobs\SendLicensingRequestEmailJob;
use App\Services\Serve\VideoPlayerService;
use App\Services\Serve\PlaylistImageService;
use App\Services\Serve\PropertyImageService;
use App\Services\Marketplace\CheckoutService;
use App\Http\Resources\Playlist as PlaylistResource;

class MarketplaceController extends Controller
{
    protected $solr;
    protected $default_keywords;

    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR = 'error';

    public function __construct(SolrQueryService $solr, CheckoutService $checkoutService)
    {
        $this->solr = $solr;
        $this->default_keywords = '((type:playlist AND videos_count:[1 TO *]) OR type:video)';
    }

    /*
    * Query videos, playlists or both for search result
    */
    public function query(Request $request)
    {
        $keywords = $this->setKeywords($request, null);
        $filter = $request->filter;
        $genre = $request->genre;
        $propertyName = $request->propertyName;
        $mostpopular = (bool) $request->mostpopular ?? false;
        $latestUpdate = (bool) $request->latestUpdate ?? false;
        $justIn = (bool) $request->justIn ?? false;
        $start = $request->start ?? 0;
        $limit = $request->limit ?? 6;
        $options = [
            'playlist_ids' => null,
            'random' => 1 == $request->get('random'),
            'propertyId' => null,
            'playlistId' => $request->get('playlistId') ?? null,
        ];
        if ($request->get('playlist_id')) {
            $playlist_ids = explode(',', $request->get('playlist_id'));
            foreach ($playlist_ids as $playlist_id) {
                if ($options['playlist_ids']) {
                    $options['playlist_ids'] .= ' OR playlist_ids:'.$playlist_id;
                } else {
                    $options['playlist_ids'] .= 'playlist_ids:'.$playlist_id;
                }
            }
            $options['playlist_ids'] = '('.$options['playlist_ids'].')';
        }
        if ($request->get('propertyId')) {
            $property_ids = explode(',', $request->get('propertyId'));
            foreach ($property_ids as $property_id) {
                if ($options['propertyId']) {
                    $options['propertyId'] .= ' OR property_id:'.$property_id;
                } else {
                    $options['propertyId'] .= 'property_id:'.$property_id;
                }
            }
            $options['propertyId'] = '('.$options['propertyId'].')';
        }

        if (!empty(trim($propertyName))) {
            $keywords = 'property_name:"'.$propertyName.'"';
        }

        $result = $this->solr->eDismaxQuery($keywords, $genre, $filter, $start, $limit, $latestUpdate, $mostpopular, $justIn, $options);
        if ('noresult' == $result && empty(trim($propertyName))) {
            $title = 'title:'.str_replace(' ', ' OR title:', $request->keywords).'*';
            $cpname = 'property_name:'.str_replace(' ', ' OR property_name:', $request->keywords).'*';
            $setKeywords = '('.$title.' OR '.$cpname.') AND '.$this->default_keywords;

            return $this->solr->eDismaxQuery($setKeywords, $genre, $filter, $start, $limit, $latestUpdate, $mostpopular, $justIn);
        }

        return $result;
    }

    private function setKeywords($request, $utf8)
    {
        if (is_null($utf8)) {
            if (empty(trim($request->keywords))) {
                return $this->default_keywords;
            }
            $mapKeyword = [
                'title:*'.$request->keywords.'*',
                'title:'.$request->keywords,
                'property_name:*'.$request->keywords.'*',
                'property_name:'.$request->keywords,
                'tags:*'.$request->keywords.'*',
                'tags:'.$request->keywords,
            ];
            if (count(explode(' ', $request->keywords)) > 1) {
                foreach (explode(' ', $request->keywords) as $keyword) {
                    $mapKeyword[] = 'title:*'.$keyword.'*';
                    $mapKeyword[] = 'title:'.$keyword;
                    $mapKeyword[] = 'property_name:*'.$keyword.'*';
                    $mapKeyword[] = 'property_name:'.$keyword;
                    $mapKeyword[] = 'tags:*'.$keyword.'*';
                    $mapKeyword[] = 'tags:'.$keyword;
                }
            }

            return '('.implode(' OR ', $mapKeyword).') AND '.$this->default_keywords;
        }

        return empty(trim($request->keywords)) ? '*:*' : 'title:'.str_replace(' ', '* OR title:*', $utf8).'*';
    }

    public function subscribe(Request $request, PropertyCP $property)
    {
        $user = Auth::user();
        $subscription = UserSubscription::withTrashed()->where('user_id', $user->id)->where('property_id', $property->id)->first();
        if (!$subscription) {
            $user->subscriptions()->create([
                'property_id' => $property->id,
            ]);
        } else {
            if ($subscription->trashed()) {
                $subscription->where('user_id', $user->id)->where('property_id', $property->id)->restore();
            }
        }

        return response()->json([
            'status' => self::STATUS_SUCCESS,
        ]);
    }

    public function unsubscribe(Request $request, PropertyCP $property)
    {
        $user = Auth::user();
        $res = UserSubscription::where('user_id', $user->id)->where('property_id', $property->id)->delete();

        if ($res) {
            return response()->json([
                'status' => self::STATUS_SUCCESS,
            ]);
        } else {
            return response()->json([
                'status' => self::STATUS_ERROR,
            ]);
        }
    }

    public function propertyList(Request $request)
    {
        $region = $request->get('region') ?? null;
        $start = $request->get('start') ?? 0;
        $limit = $request->get('limit') ?? 10;
        $chinaRegionCodes = ['CN', 'TW', 'MO', 'HK'];

        $properties_query = PropertyCP::whereHas('playlists', function ($query) {
            $query->published()->onScheduled();
        })->with(['playlists' => function ($query) {
            $query->withCount(['entries' => function ($query) {
                $query->published();
            }]);
        }])
            ->when('china' == $region, function ($query) use ($chinaRegionCodes) {
                $query->whereIn('country', $chinaRegionCodes);
            })
            ->when('international' == $region, function ($query) use ($chinaRegionCodes) {
                $query->whereNotIn('country', $chinaRegionCodes)->whereNotNull('country');
            })
            ->withCount('userSubscriptions');
        $total_count = $properties_query->count();
        $properties = $properties_query->skip($start)->take($limit)->get();

        $result_properties = [];
        foreach ($properties as $property) {
            $property_playlists = [];
            foreach ($property->playlists as $key => $playlist) {
                $property_playlists[] = [
                    'id' => $playlist->id,
                    'name' => $playlist->name,
                    'logo' => PlaylistImageService::getImageUrl($playlist, $playlist->property_id),
                    'video_count' => $playlist->entries_count,
                ];
                if (2 == $key) {
                    break;
                }
            }
            $result_properties[] = [
                'id' => $property->id,
                'name' => $property->name,
                'logo' => PropertyImageService::getImageUrl($property),
                'region' => empty($property->country) ? '' : __('country.'.$property->country),
                'playlist_count' => $property->playlists->count(),
                'entry_count' => $property->entries->count(),
                'is_subscribed' => $property->user_subscriptions_count ? 1 : 0,
                'updated_at' => $property->updated_at->toDateTimeString(),
                'playlists' => $property_playlists,
            ];
        }

        return response()->json([
            'status' => self::STATUS_SUCCESS,
            'data' => $result_properties,
            'total_count' => $total_count,
        ]);
    }

    public function getProperty(Request $request, $id)
    {
        $property = PropertyCP::whereHas('playlists', function ($query) {
            $query->published()->onScheduled();
        })->withCount('userSubscriptions')->find($id);
        if ($property) {
            return response()->json([
                'status' => self::STATUS_SUCCESS,
                'data' => [
                    'id' => $property->id,
                    'name' => $property->name,
                    'logo' => PropertyImageService::getImageUrl($property),
                    'feature_image' => PropertyImageService::getImageUrl($property, PropertyImageService::IMAGE_FEATURE_TYPE),
                    'region' => empty($property->country) ? '' : __('country.'.$property->country),
                    'playlist_count' => $property->playlists->count(),
                    'entry_count' => $property->entries->count(),
                    'updated_at' => $property->updated_at->toDateTimeString(),
                    'description' => $property->description,
                    'is_subscribed' => $property->user_subscriptions_count ? 1 : 0,
                ],
            ]);
        } else {
            return response()->json([
                'status' => self::STATUS_ERROR,
                'message' => 'Property Not Found',
            ]);
        }
    }

    public function playlistIndex(Request $request)
    {
        $property_ids = $request->get('propertyId');
        $entry_id = $request->get('entryId');
        $start = $request->get('start') ?? 0;
        $limit = $request->get('limit') ?? 10;

        $playlists_query = Playlist::with('contentProvider')->onScheduled()->when($property_ids, function ($query, $property_ids) {
            return $query->whereIn('property_id', explode(',', $property_ids));
        })->when($entry_id, function ($query, $entry_id) {
            return $query->whereHas('entries', function ($query) use ($entry_id) {
                $query->where('id', $entry_id);
            });
        })->published();
        $playlists_count = $playlists_query->whereHas('entries', function ($query) {
            $query->published()->ready();
        })->count();
        $playlists = $playlists_query->withCount(['entries' => function ($query) {
            $query->published()->ready();
        }])->having('entries_count', '>', '0')->skip($start)->take($limit)->get();

        $result_playlists = [];
        foreach ($playlists as $playlist) {
            $result_playlists[] = [
                'id' => $playlist->id,
                'name' => $playlist->name,
                'genre' => $playlist->genre,
                'logo' => PlaylistImageService::getImageUrl($playlist, $playlist->property_id),
                'entries_count' => $playlist->entries_count,
                'cp_id' => $playlist->contentProvider->id,
                'cp_name' => $playlist->contentProvider->name,
                'cp_logo' => PropertyImageService::getImageUrl($playlist->content_provider),
            ];
        }

        return response()->json([
            'status' => self::STATUS_SUCCESS,
            'data' => $result_playlists,
            'total_count' => $playlists_count,
        ]);
    }

    public function entryShow(Request $request, Entry $entry)
    {
        $entry->load(['platformAlivodTranscodes' => function ($query) {
            $query->whereIn('platform_alivod_transcodes.status', [PlatformAlivodTranscode::STATUS_SUCCESS, PlatformAlivodTranscode::STATUS_NORMAL])
                ->where('format', 'm3u8')
                ->orderBy('bitrate', 'desc');
        }, 'anzhengEvidence' => function ($query) {
            $query->where('status', EntryAnzhengEvidence::STATUS_EVIDENCE_READY);
        }]);

        $prices = [];
        $entry->platformAlivodTranscodes->each(function ($alivodTranscode) use (&$prices, $request) {
            $prices[] = [
                'resolution' => __('resolution.'.strtolower($alivodTranscode->definition), [], $request->get('locale')),
                'price' => $alivodTranscode->price,
            ];
        });

        return response()->json([
            'status' => self::STATUS_SUCCESS,
            'data' => [
                'id' => $entry->id,
                'name' => $entry->name,
                'logo' => VideoImageService::getImageUrl($entry, $entry->property_id),
                'duration' => $entry->duration,
                'description' => $entry->description,
                'playlistId' => $entry->playlists->pluck('id')->all(),
                'propertyId' => $entry->property_id,
                'prices' => $prices,
                'price_note' => $entry->price_note,
                'evidence' => $entry->anzhengEvidence ? 1 : 0,
            ],
        ]);
    }

    public function entryIndex(Request $request)
    {
        $property_ids = $request->get('propertyId');
        $playlist_id = $request->get('playlistId');
        $start = $request->get('start') ?? 0;
        $limit = $request->get('limit') ?? 10;

        $entries_query = Entry::published()
            ->whereHas('playlists', function ($query) {
                $query->onScheduled()->published();
            })
            ->when($property_ids, function ($query) use ($property_ids) {
                $query->whereHas('content_provider', function ($q) use ($property_ids) {
                    $q->whereIn('id', explode(',', $property_ids));
                })->with(['content_provider', 'playlists' => function ($q) use ($property_ids) {
                    $q->whereIn('property_id', explode(',', $property_ids))->where('status', Playlist::STATUS_READY)->published();
                }]);
            })
            ->when($playlist_id, function ($query) use ($playlist_id) {
                $query->whereHas('playlists', function ($query) use ($playlist_id) {
                    $query->where('id', $playlist_id);
                });
            });
        $entries_count = $entries_query->count();
        $entries = $entries_query->skip($start)->take($limit)->get();

        $result_entries = [];
        foreach ($entries as $entry) {
            $data = [
                'id' => $entry->id,
                'name' => $entry->name,
                'logo' => VideoImageService::getImageUrl($entry, $entry->property_id),
                'duration' => $entry->duration,
            ];
            if ($property_ids) {
                $genres = $entry->playlists->pluck('genre')->toArray();
                $data['genre'] = implode(',', $genres);
                $data['description'] = $entry->description;
                $data['property_name'] = $entry->content_provider->name;
                $data['property_logo'] = PropertyImageService::getImageUrl($entry->content_provider);
            }
            $result_entries[] = $data;
        }

        return response()->json([
            'status' => self::STATUS_SUCCESS,
            'data' => $result_entries,
            'total_count' => $entries_count,
        ]);
    }

    public function player(Request $request, Entry $entry)
    {
        $playerRender = '<div id="video_player" class="img-container prism-player"></div>';
        $playerRender .= VideoPlayerService::player($entry->content_provider, $entry, 'video_player');

        return $playerRender;
    }

    public function playlist(Request $request, $playlist_id)
    {
        $user = Auth::user();
        $playlist = Playlist::with(['content_provider', 'content_provider.organization', 'marketplaceTerm', 'userCarts' => function ($query) use ($user) {
            $query->where('user_id', $user->id)->whereNull('requested_at');
        }])
            ->withCount(['entries' => function ($query) {
                $query->published();
            }])
            ->onScheduled()
            ->published()
            ->findOrFail($playlist_id);

        return response()->json([
            'status' => self::STATUS_SUCCESS,
            'data' => new PlaylistResource($playlist),
        ]);
    }

    public function subscription(Request $request)
    {
        $user = Auth::user();
        $start = $request->get('start') ?? 0;
        $limit = $request->get('limit') ?? 10;

        $properties_query = PropertyCP::whereHas('userSubscriptions', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with(['playlists' => function ($query) {
            $query->withCount(['entries' => function ($query) {
                $query->published();
            }]);
        }]);
        $total_count = $properties_query->count();
        $properties = $properties_query->skip($start)->take($limit)->get();

        $result_properties = [];
        foreach ($properties as $property) {
            $property_playlists = [];
            foreach ($property->playlists as $key => $playlist) {
                $property_playlists[] = [
                    'id' => $playlist->id,
                    'name' => $playlist->name,
                    'logo' => PlaylistImageService::getImageUrl($playlist, $playlist->property_id),
                    'video_count' => $playlist->entries_count,
                ];
                if (2 == $key) {
                    break;
                }
            }
            $result_properties[] = [
                'id' => $property->id,
                'name' => $property->name,
                'logo' => PropertyImageService::getImageUrl($property),
                'playlist_count' => $property->playlists->count(),
                'entry_count' => $property->entries->count(),
                'updated_at' => $property->updated_at->toDateTimeString(),
                'playlists' => $property_playlists,
            ];
        }

        return response()->json([
            'status' => self::STATUS_SUCCESS,
            'data' => $result_properties,
            'total_count' => $total_count,
        ]);
    }

    public function cart(Request $request)
    {
        $user = Auth::user();
        $requested = $request->get('requested');
        $start = intval($request->get('start'));
        $limit = intval($request->get('limit'));

        $userCarts_query = UserCart::whereHas('playlist', function ($query) {
            $query->published()->where('status', Playlist::STATUS_READY);
        })
            ->with(['playlist' => function ($query) {
                $query->withCount('publishedEntries');
            }, 'playlist.content_provider', 'playlist.content_provider.organization'])
            ->where('user_id', $user->id)
            ->when($requested, function ($query) {
                $query->whereNotNull('requested_at')->orderBy('requested_at', 'DESC');
            }, function ($query) {
                $query->whereNull('requested_at')->orderBy('created_at', 'DESC');
            });
        $total_count = $userCarts_query->count();
        $userCarts = $userCarts_query->when($limit, function ($query) use ($start, $limit) {
            $query->skip($start)->take($limit);
        })->get();

        return (new UserCartCollection($userCarts))->additional([
            'status' => self::STATUS_SUCCESS,
            'total_count' => $total_count,
        ]);
    }

    public function cartAdd(Request $request)
    {
        $playlist = Playlist::published()->onScheduled()->find($request->playlist_id);
        if (!$playlist) {
            return response()->json([
                'status' => self::STATUS_ERROR,
                'message' => 'playlist not found',
            ]);
        }

        $user = Auth::user();

        $userCart = UserCart::where('user_id', $user->id)->where('playlist_id', $playlist->id)->whereNull('requested_at')->first();
        if (!$userCart) {
            UserCart::create([
                'user_id' => $user->id,
                'playlist_id' => $playlist->id,
            ]);
        }

        return response()->json([
            'status' => self::STATUS_SUCCESS,
        ]);
    }

    public function cartRemove(Request $request)
    {
        $ids = $request->get('ids');
        if (!$ids) {
            return response()->json([
                'status' => self::STATUS_ERROR,
                'message' => 'cart id must provide',
            ]);
        }

        $user = Auth::user();
        UserCart::where('user_id', $user->id)->whereIn('id', explode(',', $ids))->whereNull('requested_at')->delete();

        return response()->json([
            'status' => self::STATUS_SUCCESS,
        ]);
    }

    public function cartCheckout(Request $request)
    {
        $subject = $request->get('subject');
        $message = $request->get('message');
        $cart_ids = $request->get('ids');
        $sp_ids = $request->get('sp_ids');

        if (!$subject || !$message || !$cart_ids || !$sp_ids) {
            return response()->json([
                'status' => self::STATUS_ERROR,
                'message' => 'param missing',
            ]);
        }

        $user = Auth::user();

        $cart_ids = explode(',', $cart_ids);
        $sp_ids = explode(',', $sp_ids);

        $playlist_ids = UserCart::where('user_id', $user->id)->whereIn('id', $cart_ids)->whereNull('requested_at')->pluck('playlist_id')->toArray();
        if (empty($playlist_ids)) {
            return response()->json([
                'status' => self::STATUS_ERROR,
                'message' => 'playlist not found',
            ]);
        }

        $contentProviders = PropertyCP::whereHas('playlists', function ($q) use ($playlist_ids) {
            $q->whereIn('id', $playlist_ids);
        })->get()->keyBy('id')->map(function ($cp) {
            return [
                'property_name' => $cp->name,
            ];
        })->toArray();

        $serviceProviders = PropertySP::whereIn('id', $sp_ids)
            ->get()->keyBy('id')->map(function ($cp) {
                return [
                    'property_name' => $cp->name,
                ];
            })->toArray();

        DB::beginTransaction();
        try {
            $requestLog = RequestLog::create([
                'requester_user_id' => $user->id,
                'subject' => $subject,
                'message' => $message,
            ]);
            $requestLog->serviceProviders()->attach($serviceProviders);
            $requestLog->contentProviders()->attach($contentProviders);
            $requestLog->playlists()->attach($playlist_ids);

            UserCart::where('user_id', $user->id)->whereIn('playlist_id', $playlist_ids)->update([
                'requested_at' => Carbon::now(),
            ]);

            SendLicensingRequestEmailJob::dispatch($requestLog, $playlist_ids, $user, App::getLocale());

            DB::commit();

            return response()->json([
                'status' => self::STATUS_SUCCESS,
            ]);
        } catch (\Exception $exception) {
            Log::error('cartCheckout:'.$exception->getMessage());
            DB::rollBack();
        }

        return response()->json([
            'status' => self::STATUS_ERROR,
        ]);
    }

    public function setLocale($locale)
    {
        if (array_key_exists($locale, config('language_code.codes'))) {
            session()->put('locale', $locale);
            App::setLocale(session()->get('locale'));
        }

        return response()->json([
            'status' => self::STATUS_SUCCESS,
        ]);
    }

    public function SpList()
    {
        $user = Auth::user();
        $serviceProviders = $user->serviceProviderCanManage();
        $spList = [];
        foreach ($serviceProviders as $serviceProvider) {
            array_push($spList, [
                'id' => $serviceProvider->id,
                'name' => $serviceProvider->name,
            ]);
        }

        return response()->json([
            'status' => self::STATUS_SUCCESS,
            'data' => $spList,
        ]);
    }
}
