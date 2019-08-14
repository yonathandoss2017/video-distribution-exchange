<?php

namespace App\Http\Resources;

use App\Services\Serve\VideoUrlService;
use App\Services\Serve\VideoImageService;
use Illuminate\Http\Resources\Json\Resource;

class Entry extends Resource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'status' => 'success',
            'entry' => [
                'id' => $this->id,
                'name' => $this->name,
                'duration' => $this->duration,
                'views' => $this->views,
                'published_at' => $this->published_at->getTimestamp(),
                'service_provider' => empty($this->service_provider) ? $this->_emptySp() : [
                    'id' => $this->service_provider->id,
                    'name' => $this->service_provider->name,
                    'organization_id' => $this->service_provider->organization_id,
                    'organization' => [
                        'id' => $this->service_provider->organization->id,
                        'name' => $this->service_provider->organization->name,
                    ],
                ],
                'thumbnail_url' => VideoImageService::getImageUrl($this->resource, $this->content_provider->id),
                'video' => VideoUrlService::getVideo($this->resource),
                'access_control' => $this->access_control,
                'playlist_ids' => empty($this->playlistIds) ? [] : $this->playlistIds,
                'content_provider' => [
                    'id' => $this->content_provider->id,
                    'name' => $this->content_provider->name,
                    'organization_id' => $this->content_provider->organization_id,
                    'organization' => [
                        'id' => $this->content_provider->organization->id,
                        'name' => $this->content_provider->organization->name,
                    ],
                ],
                'subtitles' => new EntrySubtitleCollection($this->subtitles),
            ],
        ];
    }

    protected function _emptySp()
    {
        return [
            'id' => 0,
            'name' => 'IVX',
            'organization_id' => 0,
        ];
    }
}
