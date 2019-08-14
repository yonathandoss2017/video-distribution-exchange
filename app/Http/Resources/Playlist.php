<?php

namespace App\Http\Resources;

use App\Services\Serve\PlaylistImageService;
use Illuminate\Http\Resources\Json\Resource;

class Playlist extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'entries_count' => $this->entries_count,
            'name' => $this->name,
            'thumbnail_url' => PlaylistImageService::getImageUrl($this->resource, $this->property_id, null, 768),
            'language' => $this->language ? __('language.'.$this->language) : '',
            'region' => $this->region ? __('country.'.$this->region) : '',
            'genre' => $this->genre,
            'stars' => $this->stars,
            'publish_start_date' => $this->publish_start_date,
            'publish_end_date' => $this->publish_end_date,
            'organization' => [
                'id' => optional($this->content_provider)->organization->id,
                'name' => optional($this->content_provider)->organization->name,
            ],
            'property' => [
                'id' => optional($this->content_provider)->id,
                'name' => optional($this->content_provider)->name,
            ],
            'term' => new MarketplaceTerm($this->marketplaceTerm),
            'in_cart' => $this->userCarts->count() > 0 ? 1 : 0,
            'created_at' => $this->created_at->timestamp,
            'updated_at' => $this->updated_at->timestamp,
        ];
    }

    public function with($request)
    {
        return ['status' => 'success'];
    }
}
