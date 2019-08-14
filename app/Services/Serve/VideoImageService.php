<?php

namespace App\Services\Serve;

use App\Models\Entry;
use App\Services\Vod\Ali\UrlService as VodUrlService;
use App\Services\Storage\Oss\UrlService as OssUrlService;

class VideoImageService
{
    /**
     * Provides a standard URL format to quickly get images via /serve/image.
     * This is a 301 redirect URL that will be cached by Cloudflare or CDN.
     * With this, if a page generates 20 images, we don't have to query
     * the database 20 times anymore to fetch the images if we could
     * return 20 generic image URLs via a standard URL format that
     * is created by getImageUrl().
     *
     * For APIs however, we use the getImage() as some third-party services
     * like set-top boxes are not able to consume redirected images.
     */
    public static function getImageUrl(Entry $entry, $prop_id, $timestamp = null, $width = null)
    {
        // Timestamp is used a version control to expire caches
        $timestamp = $timestamp ?? $entry->updated_at->timestamp;
        $parameters = ['prop_id' => $prop_id, 'entry' => $entry->id, 'timestamp' => $timestamp];
        if ($width) {
            $parameters['width'] = $width;
        }

        return route('serve.image.video', $parameters);
    }

    /**
     * Do not access this function directly. Access through the
     * /serve/image route instead. ONLY use this if you are
     * using it to generate images for APIs.
     */
    public static function getImage(Entry $entry, $prop_id, $width = null)
    {
        // Check if CP is a match first (cheapest check before hitting database)
        if ($prop_id == $entry->property_id) {
            return self::getCPImage($entry, $width);
        }

        // Check if SP is a match second
        $spEntry = $entry->properties()->where('property_id', $prop_id)->first();
        if (!empty($spEntry)) {
            if (!empty($spEntry->pivot->image_path)) {
                return self::getSPImage($spEntry->pivot, $width);
            }
        }

        // Fallback to CP Image because no SP Image found
        return self::getCPImage($entry, $width);
    }

    public static function getSPImage($spPivot, $width = null)
    {
        $options = $width ? ['width' => $width] : [];

        return OssUrlService::getUrl($spPivot->image_path, $options);
    }

    public static function getCPImage(Entry $entry, $width = null)
    {
        if (Entry::STATUS_PROCESSING == $entry->status) {
            return url('/images/video-default.jpg');
        }

        $options = $width ? ['width' => $width] : [];
        /*
         * CP has a custom image uploaded
         */
        if (!empty($entry->thumbnail_url)) {
            return VodUrlService::getUrl($entry->thumbnail_url, $options);
        } elseif (!empty($entry->image_path)) {
            return OssUrlService::getUrl($entry->image_path, $options);
        }

        /*
         * TODO: Wait for later handle with it.
         * Filer and return only the platforms that are available.
         * Then return the first available AND ready platform.
         */
        /*$platforms = collect($entry::PLATFORMS);
        $platform = $platforms->filter(function ($platform) use ($entry) {
            return $entry->hasPlatform($platform);
        })->first(function ($platform) use ($entry) {
            $platformEntry = self::getPlatformEntry($platform, $entry);

            return !empty($platformEntry) && $platformEntry->isReady();
        });

        // Get the image
        if (!empty($platform)) {
            $platformEntry = self::getPlatformEntry($platform, $entry);

            return $platformEntry->getImage($width);
        }*/

        // When nothing is available...
        return url('/images/video-default.jpg');
    }

    private static function getPlatformEntry($platform, Entry $entry)
    {
        $child = Entry::getRelationNameFromPlatform($platform);

        return $entry->$child;
    }
}
