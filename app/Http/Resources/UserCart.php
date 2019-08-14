<?php

namespace App\Http\Resources;

use App\Services\Serve\PlaylistImageService;
use Illuminate\Http\Resources\Json\Resource;

class UserCart extends Resource
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
        $playlist = $this->playlist;

        return [
            'id' => $this->id,
            'playlist' => [
                'id' => $playlist->id,
                'name' => $playlist->name,
                'thumbnail_url' => PlaylistImageService::getImageUrl($playlist, $playlist->property_id),
                'genre' => $playlist->genre,
                'publish_start_date' => $playlist->publish_start_date,
                'publish_end_date' => $playlist->publish_end_date,
                'entries_count' => $playlist->published_entries_count,
                'property_id' => optional($playlist->content_provider)->id,
                'property_name' => optional($playlist->content_provider)->name,
                'organization_name' => optional($playlist->content_provider->organization)->name,
            ],
            'requested_at' => $this->requested_at ? $this->requested_at->timestamp : '',
            'created_at' => $this->created_at->timestamp,
        ];
    }

    public function with($request)
    {
        return ['status' => 'success'];
    }
}
