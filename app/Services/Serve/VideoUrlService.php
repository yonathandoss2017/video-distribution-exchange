<?php

namespace App\Services\Serve;

use App\Models\Entry;

class VideoUrlService
{
    /**
     * Get a specific platform.
     *
     * @param Entry  $entry
     * @param string $platform
     *
     * @return array|null
     */
    public static function getVideoByPlatform(Entry $entry, $platform)
    {
        $relation = Entry::getRelationNameFromPlatform($platform);
        if (!$entry->$relation) {
            return null;
        }

        return $entry->$relation->getVideoData();
    }

    /**
     * Get platform waterfall priority.
     *
     * @param Entry $entry
     *
     * @return array|null
     */
    public static function getVideo(Entry $entry)
    {
        $platform = collect(Entry::PLATFORMS)->filter(function ($platform) use ($entry) {
            return $entry->hasPlatform($platform);
        })->first(function ($platform) use ($entry) {
            return self::getVideoByPlatform($entry, $platform);
        });

        if (!$platform) {
            return null;
        }

        return self::getVideoByPlatform($entry, $platform);
    }
}
