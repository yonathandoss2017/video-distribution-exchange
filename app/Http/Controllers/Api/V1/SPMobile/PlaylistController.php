<?php

namespace App\Http\Controllers\Api\V1\SPMobile;

use Carbon\Carbon;
use App\Models\Entry;
use App\Models\Playlist;
use Illuminate\Http\Request;
use App\Models\PlatformAlivod;
use App\Models\TermsOfDistribution;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use App\Repositories\EntryRepository;
use App\Services\Serve\VideoUrlService;
use App\Services\Serve\VideoImageService;
use App\Services\Serve\VideoDetailsService;
use App\Services\Serve\PlaylistImageService;
use App\Services\Serve\PlaylistDetailsService;

class PlaylistController extends Controller
{
    public function index(Request $request)
    {
        $sp = $request->get('sp');

        $property_id = $sp->id;

        $max = $request->query('per_page');
        $max = isset($max) ? (int) $max : 50;
        if ($max > 50) {
            $max = 50;
        }

        $playlists = Playlist::select(['id', 'name', 'created_at', 'updated_at', 'thumbnail_path'])
            ->withCount(['readyEntries' => function ($q) {
                $q->published();
            }])
            ->WhitelistedForSP($sp)
            ->get()
            ->sortByDesc(function ($playlist, $key) {
                return $playlist->entries()->sum('views');
            });
        $playlists = new Paginator($playlists, $max);
        $playlists = $playlists->toArray();
        foreach ($playlists['data'] as $index => $playlist) {
            $playlistObject = Playlist::find($playlist['id']);
            $playlists['data'][$index]['name'] = PlaylistDetailsService::getName($playlistObject, $property_id);
            $playlists['data'][$index]['created_at'] = Carbon::parse($playlist['created_at'])->timestamp;
            $playlists['data'][$index]['updated_at'] = Carbon::parse($playlist['updated_at'])->timestamp;
            $playlists['data'][$index]['entries_count'] = $playlist['ready_entries_count'];
            $playlists['data'][$index]['thumbnail_url'] = PlaylistImageService::getImageUrl($playlistObject, $property_id);
            unset($playlists['data'][$index]['ready_entries_count']);
        }

        $result['status'] = 'success';
        $result['playlists'] = $playlists;
        $result['playlists']['data'] = array_values($playlists['data']);

        return response()->json($result);
    }

    public function withVideos($id, Request $request)
    {
        $sp = $request->get('sp');

        $max = $request->query('per_page');
        $max = isset($max) ? (int) $max : 50;
        if ($max > 50) {
            $max = 50;
        }

        $platform = $request->query('platform');
        if (isset($platform)) {
            $platform = strtolower($platform);
            if (!Entry::isValidPlatform($platform)) {
                abort(400);
            }
        }

        $playlist = Playlist::where('id', '=', $id)
            ->select(['id', 'name', 'genre', 'region', 'language', 'property_id'])
            ->WhitelistedForSP($sp)
            ->with(['tods' => function ($q) use ($sp) {
                $q->where('sp_property_id', $sp->id)
                    ->where('status', TermsOfDistribution::STATUS_ACTIVE)
                    ->with(['serviceProvider' => function ($spQuery) {
                        $spQuery->select(['id', 'name', 'organization_id'])
                            ->with(['organization' => function ($org) {
                                $org->select(['id', 'name']);
                            },
                            ]);
                    }]);
            }, 'content_provider' => function ($q) {
                $q->select(['id', 'name', 'organization_id']);
                $q->with(['organization' => function ($q) {
                    $q->select(['id', 'name']);
                }]);
            }])->first();

        if (!isset($playlist)) {
            abort(403);
        }

        $property_id = $sp->id;
        $playlist['name'] = PlaylistDetailsService::getName($playlist, $property_id);

        // 为了消除返回结果中无用的content_provider信息 所以注释这一段 stone
        /* if ($playlist->contentProvider->internalTod) {
            $playlist->service_provider = [
                'id' => $sp->id,
                'name' => $sp->name,
                'organization_id' => $sp->organization_id,
                'organization' => [
                    'id' => $sp->organization_id,
                    'name' => $sp->name,
                ],
            ];
        } else {
            $playlist->service_provider = $playlist->tods->first()->serviceProvider;
        }
        unset($playlist->contentProvider->internalTod); */
        unset($playlist->tods);

        $query = $playlist->readyEntries()
            ->published()
            ->select(['id', 'name', 'description', 'duration', 'views', 'created_at', 'updated_at'])
            ->with(['metadata' => function ($q) {
                $q->select(['entry_id', 'tags']);
            }])->orderBy('published_at', 'desc');

        if (isset($platform)) {
            if (Entry::PLATFORM_ALIVOD == $platform) {
                $statusReady = PlatformAlivod::STATUS_READY;
            }

            $platformRelation = Entry::getRelationNameFromPlatform($platform);
            $query = $query->whereHas($platformRelation, function ($q) use ($statusReady) {
                $q->where('status', '=', $statusReady);
            })->with([$platformRelation]);
        } else {
            $query = EntryRepository::prepareEntryQueryWhereHasPlatformVideos($query);
            $query = EntryRepository::prepareEntryQueryWithPlatformVideos($query);
        }

        $entries = $query->paginate($max, ['id', 'name', 'description', 'duration', 'created_at', 'updated_at'])->toArray();
        for ($i = 0; $i < count($entries['data']); ++$i) {
            $entry = Entry::findOrFail($entries['data'][$i]['id']);
            $entries['data'][$i]['video'] = VideoUrlService::getVideo($entry);

            if (null != $entries['data'][$i]['platform_alivod']) {
                $entries['data'][$i]['thumbnail_url'] = $this->getVideoThumbnail($entries['data'][$i]['id'], $sp->id, Playlist::DEFAULT_THUMBNAIL_WIDTH);
            }

            unset($entries['data'][$i]['platform_alivod']);

            $entries['data'][$i]['created_at'] = Carbon::parse($entries['data'][$i]['created_at'])->timestamp;
            $entries['data'][$i]['updated_at'] = Carbon::parse($entries['data'][$i]['updated_at'])->timestamp;
            $entries['data'][$i]['name'] = VideoDetailsService::getTitle($entry, $sp->id);
            $entries['data'][$i]['description'] = VideoDetailsService::getDescription($entry, $sp->id);
        }

        $result['status'] = 'success';
        $result['playlist'] = $playlist;
        $result['playlist']['entries'] = $entries;

        return response()->json($result);
    }

    private function getVideoThumbnail($video_id, $property_id, $width = null)
    {
        $entry = Entry::findOrFail($video_id);

        return VideoImageService::getImageUrl($entry, $property_id, $entry->getSpEntryTimestamp($property_id), $width);
    }
}
