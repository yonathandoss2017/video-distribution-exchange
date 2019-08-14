<?php

namespace App\Http\Controllers\Api\V2\Common;

use Carbon\Carbon;
use App\Models\Entry;
use App\Models\Playlist;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\Entry as EntryResource;
use App\Http\Controllers\Api\PropertyApiController;

class EntryController extends PropertyApiController
{
    public function __invoke(Request $request)
    {
        $sp = $request->get('sp');

        $this->validateWithException($request, [
            'entry_id' => 'required',
        ]);

        $entry_id = $request->get('entry_id');

        $video = Entry::where('id', $entry_id)
            ->with([
                'content_provider',
                'content_provider.organization',
                'subtitles',
            ])
            ->whereHasPlatformVideos()
            ->withPlatformVideos();

        $spPlaylistIds = [];
        if (!empty($sp)) {
            $spPlaylistIds = Playlist::whitelistedForSP($sp)
                ->pluck('id')
                ->all();

            //SP can only access published video
            $video = $video->published()
                ->whereHas('playlists', function ($q) use ($spPlaylistIds) {
                    $q->whereIn('id', $spPlaylistIds);
                });
        }

        $video = $video->first();

        if (!$video) {
            return $this->responseNotFoundWithMessage('Video not found');
        }

        if (!empty($sp)) {
            $playlistIds = $video->playlists->whereIn('id', $spPlaylistIds)->pluck('id')->toArray();
            $todPlaylists = Playlist::whitelistedForSP($sp)
                ->with([
                    'availableTods' => function ($q) use ($sp) {
                        $q->where('sp_property_id', $sp->id)
                            ->with(['regionRights' => function ($q) {
                                $q->where(function ($q) {
                                    $q->whereNull('started_at');
                                    $q->whereNull('ended_at');
                                });
                                $q->orWhere(function ($q) {
                                    $q->where('started_at', '<=', Carbon::now());
                                    $q->where('ended_at', '>=', Carbon::now());
                                });
                            }]);
                    }, 'contentProvider' => function ($q) use ($sp) {
                        $q->where('organization_id', $sp->organization_id);
                        $q->with('internalTod');
                    },
                ])
                ->whereIn('id', $playlistIds)
                ->get();

            $video->access_control = $this->transformAccessControl($todPlaylists);
            $video->service_provider = $sp;
            $video->playlistIds = $playlistIds;
        }

        Resource::withoutWrapping();

        return new EntryResource($video);
    }

    protected function transformAccessControl(Collection $playlists)
    {
        $result = [];
        if ($playlists->isEmpty()) {
            return $result;
        }

        foreach ($playlists as $playlist) {
            $internalTod = optional($playlist->contentProvider)->internalTod;
            if ($internalTod) {
                $internalTod->load('regionRights');
                $allowed = !empty($internalTod->regionRights->last()->allowed_regions) ? explode(',', $internalTod->regionRights->last()->allowed_regions) : [];
                $blocked = !empty($internalTod->regionRights->last()->excepted_regions) ? explode(',', $internalTod->regionRights->last()->excepted_regions) : [];
                $result[] = $this->accessControlFormat($playlist, $allowed, $blocked);
            } else {
                $tod = $playlist->availableTods->last();
                if (!empty($tod) && !empty($tod->regionRights)) {
                    $allowed = !empty($tod->regionRights->last()->allowed_regions) ? explode(',', $tod->regionRights->last()->allowed_regions) : [];
                    $blocked = !empty($tod->regionRights->last()->excepted_regions) ? explode(',', $tod->regionRights->last()->excepted_regions) : [];
                    $result[] = $this->accessControlFormat($playlist, $allowed, $blocked);
                }
            }
        }

        return $result;
    }

    protected function accessControlFormat(Playlist $playlist, $allowed, $blocked)
    {
        return [
            'playlist_id' => $playlist->id,
            'allowed' => $allowed,
            'blocked' => $blocked,
        ];
    }
}
