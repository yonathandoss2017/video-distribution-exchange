<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/9/5
 * Time: 14:58.
 */

namespace Tests\Services\Vod;

use Faker\Factory;
use App\Services\Vod\VodService as VodServiceInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FakeVodService implements VodServiceInterface
{
    public function uploadMediaByURL($source_url, $media_params = [])
    {
        // TODO: Implement uploadMediaByURL() method.
    }

    public function getUploadInfoByUrl($jobid)
    {
        // TODO: Implement getUploadInfoByUrl() method.
    }

    public function uploadLocalImage(UploadedFile $file)
    {
        $faker = Factory::create();

        return (object) [
            'ImageId' => $faker->uuid,
            'ImageURL' => $faker->imageUrl(20),
        ];
    }

    public function uploadWebImage($fileUrl)
    {
        // TODO: Implement uploadWebImage() method.
    }

    public function updateVideoInfo($videoId, $video_params = [])
    {
        // TODO: Implement updateVideoInfo() method.
    }

    public function submitAIReview($videoId)
    {
        // TODO: Implement submitAIMediaAuditJob() method.
    }
}
