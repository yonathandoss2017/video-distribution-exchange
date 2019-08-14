<?php

namespace App\Services\Serve;

use App\Models\Playlist;
use App\Models\PlaylistProperty;
use App\Services\Storage\Oss\UrlService;

class PlaylistImageService
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
    public static function getImageUrl(Playlist $playlist, $prop_id, $timestamp = null, $width = null)
    {
        // Timestamp is used a version control to expire caches
        $timestamp = $timestamp ?? $playlist->updated_at->timestamp;

        $parameters = ['prop_id' => $prop_id, 'playlist' => $playlist->id, 'timestamp' => $timestamp];
        if ($width) {
            $parameters['width'] = $width;
        }

        return route('serve.image.playlist', $parameters);
    }

    /**
     * Do not access this function directly. Access through the
     * /serve/image route instead. ONLY use this if you are
     * using it to generate images for APIs.
     */
    public static function getImage(Playlist $playlist, $prop_id, $width = null)
    {
        // Check if CP is a match first
        if ($prop_id == $playlist->property_id) {
            return self::getCPImage($playlist, $width);
        }

        // Check if SP is a match second
        $spPlaylist = PlaylistProperty::findRecord($playlist->property_id, $prop_id, $playlist->id)->first();
        if (!empty($spPlaylist)) {
            if (!empty($spPlaylist->thumbnail_path)) {
                return self::getSPImage($spPlaylist, $width);
            }
        }

        return self::getCPImage($playlist, $width);
    }

    public static function getCPImage(Playlist $playlist, $width)
    {
        /*
         * Check if CP has uploaded a custom image
         */
        if (!empty($playlist->thumbnail_path)) {
            $options = $width ? ['width' => $width] : [];

            return UrlService::getUrl($playlist->thumbnail_path, $options);
        }

        /**
         * Get Image from the latest entry.
         */
        $entry = $playlist->latestReadyEntry();
        if (!empty($entry)) {
            $videoImage = VideoImageService::getImage($entry, $playlist->property_id, $width);
            if ($videoImage == url('/images/video-default.jpg')) {
                return url('/images/playlist-default.jpg');
            }

            return $videoImage;
        }

        return url('/images/playlist-default.jpg');
    }

    public static function getSPImage($spPlaylist, $width)
    {
        $options = $width ? ['width' => $width] : [];

        return UrlService::getUrl($spPlaylist->thumbnail_path, $options);
    }
}
