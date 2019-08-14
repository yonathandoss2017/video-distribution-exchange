<?php

namespace App\Services\Serve;

use App\Models\AiReviewVideoResult;
use App\Services\Storage\Oss\UrlService;

class AiReviewImageService
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
    public static function getImageUrl(AiReviewVideoResult $aiReviewVideoResult, $timestamp = null, $width = 300)
    {
        // Timestamp is used a version control to expire caches
        $timestamp = $timestamp ?? $aiReviewVideoResult->updated_at->timestamp;

        return route('serve.image.ai-review.result', ['aiReviewVideoResult' => $aiReviewVideoResult->id, 'timestamp' => $timestamp, 'width' => $width]);
    }

    /**
     * Do not access this function directly. Access through the
     * /serve/image route instead. ONLY use this if you are
     * using it to generate images for APIs.
     */
    public static function getImage(AiReviewVideoResult $aiReviewVideoResult, $width = 300)
    {
        return UrlService::getUrl($aiReviewVideoResult->image_path, ['width' => $width]);
    }
}
