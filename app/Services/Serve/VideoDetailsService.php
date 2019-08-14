<?php

namespace App\Services\Serve;

use App\Models\Entry;

class VideoDetailsService
{
    public static function loadPropertiesRelation($video, $property_id)
    {
        return $video->load(['properties' => function ($query) use ($video, $property_id) {
            $query->where('property_id', $property_id);
        }]);
    }

    public static function getTitle(Entry $video, $property_id)
    {
        $properties = self::loadPropertiesRelation($video, $property_id)->properties;
        if (count($properties) > 0) {
            return $properties->first()->pivot->title;
        }

        return $video->name;
    }

    public static function getDescription($video, $property_id)
    {
        $properties = self::loadPropertiesRelation($video, $property_id)->properties;
        if (count($properties) > 0) {
            return $properties->first()->pivot->description;
        }

        return $video->description;
    }
}
