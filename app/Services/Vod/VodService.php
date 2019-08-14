<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/9/5
 * Time: 14:31.
 */

namespace App\Services\Vod;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface VodService
{
    public function uploadMediaByURL($source_url, $media_params = []);

    public function getUploadInfoByUrl($jobid);

    public function uploadLocalImage(UploadedFile $file);

    public function uploadWebImage($fileUrl);

    public function updateVideoInfo($videoId, $video_params = []);

    public function submitAIReview($videoId);
}
