<?php

namespace App\Http\Resources;

use App\Models\Property as PropertyModel;
use App\Services\Serve\PropertyImageService;
use Illuminate\Http\Resources\Json\Resource;

class Property extends Resource
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
        $types = [
            PropertyModel::TYPE_SP_PLUS => 'Service Platform',
            PropertyModel::TYPE_SP => 'Service Provider',
            PropertyModel::TYPE_CP => 'Content Provider',
        ];

        $last_updated_at = $this->updated_at->toDateTimeString();
        if (PropertyModel::TYPE_CP == $this->type) {
            $last_indexed_entry = $this->entries()->orderBy('indexed_at_marketplace', 'desc')->first();
            $last_indexed_playlist = $this->playlists()->orderBy('indexed_at_marketplace', 'desc')->first();

            $last_entry_indexed_at = $last_indexed_entry ? $last_indexed_entry->indexed_at_marketplace : null;
            $last_playlist_indexed_at = $last_indexed_playlist ? $last_indexed_playlist->indexed_at_marketplace : null;
            if ($last_entry_indexed_at > $last_updated_at) {
                $last_updated_at = $last_entry_indexed_at;
            }
            if ($last_playlist_indexed_at > $last_updated_at) {
                $last_updated_at = $last_playlist_indexed_at;
            }
        } elseif (PropertyModel::TYPE_SP == $this->type) {
            $this->entries = null;
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $types[$this->type],
            'type_value' => $this->type,
            'organization_id' => $this->organization_id,
            'organization_name' => optional($this->organization)->name,
            'logo' => PropertyImageService::getImageUrl($this->resource, 'logo'),
            'playlist_count' => $this->playlists ? $this->playlists->count() : 0,
            'entry_count' => $this->entries ? $this->entries->count() : 0,
            'last_updated_at' => $last_updated_at,
        ];
    }

    public function with($request)
    {
        return ['status' => 'success'];
    }
}
