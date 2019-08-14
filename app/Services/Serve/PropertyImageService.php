<?php

namespace App\Services\Serve;

use App\Models\Property;
use App\Services\Storage\Oss\UrlService;

class PropertyImageService
{
    const IMAGE_FEATURE_TYPE = 'feature';
    const IMAGE_LOGO_TYPE = 'logo';

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
    public static function getImageUrl(Property $property, $imageType = 'logo', $timestamp = null, $width = null)
    {
        // Timestamp is used a version control to expire caches
        $timestamp = $timestamp ?? $property->updated_at->timestamp;
        $paramters = ['property' => $property->id, 'imagetype' => $imageType, 'timestamp' => $timestamp];
        if ($width) {
            $paramters['width'] = $width;
        }

        return route('serve.image.property', $paramters);
    }

    /**
     * Do not access this function directly. Access through the
     * /serve/image route instead. ONLY use this if you are
     * using it to generate images for APIs.
     */
    public static function getImage(Property $property, $imageType = 'logo', $width = null)
    {
        $image_url = null;
        $options = $width ? ['width' => $width] : [];
        if (self::IMAGE_FEATURE_TYPE == $imageType) {
            if ($property->feature_path) {
                $image_url = UrlService::getUrl($property->feature_path, $options);
            }
            if (!$image_url) {
                $image_url = url('/images/featured-image-default.png');
            }
        } elseif (self::IMAGE_LOGO_TYPE == $imageType) {
            if ($property->logo_path) {
                $image_url = UrlService::getUrl($property->logo_path, $options);
            } else {
                $image_url = UrlService::getUrl($property->organization_id.'/'.$property->id.'/logo.png', $options);
            }
            if (!$image_url) {
                $image_url = url('images/property-logo-default.png');
            }
        }

        return $image_url;
    }
}
