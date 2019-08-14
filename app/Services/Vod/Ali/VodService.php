<?php

namespace App\Services\Vod\Ali;

use Ramsey\Uuid\Uuid;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use App\Services\Storage\Oss\AliOSS;
use AlibabaCloud\Client\AlibabaCloud;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use App\Services\Vod\VodService as VodServiceInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use AlibabaCloud\Client\Exception\ClientException as AliClientException;

class VodService implements VodServiceInterface
{
    const VOD_LOG = 'vod_log';
    const VOD_VERSION = '2017-03-21';
    const PLAYAUTH_TIMEOUT = 3000;  //VOD播放认证信息过期时间,单位秒

    private $keyId = null;
    private $keySecret = null;
    private $regionId = null;

    public function __construct()
    {
        $this->keyId = config('alivod.key_id');
        $this->keySecret = config('alivod.key_secret');
        $this->regionId = config('alivod.region_id');
        AlibabaCloud::accessKeyClient($this->keyId, $this->keySecret)->regionId($this->regionId)->asDefaultClient();
    }

    /**
     * @param $source_url
     * @param array $media_params can be [name, thumbnail_url] , "name" must provide
     *
     * @return bool|mixed|\SimpleXMLElement
     */
    public function uploadMediaByURL($source_url, $media_params = [], $extra_data = [])
    {
        if (!$source_url) {
            return false;
        }

        if (is_array($source_url)) {
            $source_url = implode(',', $source_url);
        }

        $request = ['UploadURLs' => $source_url];
        if (config('alivod.transcode_templates')['standard']) {
            $request['TemplateGroupId'] = config('alivod.transcode_templates')['standard'];
        }

        $uploadMetadataList = [];
        if (count($media_params) != count($media_params, COUNT_RECURSIVE)) {
            //list array
            foreach ($media_params as $media_param) {
                $uploadMetadata = [];
                $uploadMetadata['SourceURL'] = $media_param['video_url'];
                $uploadMetadata['Title'] = $media_param['name'];
                if ($media_param['thumbnail_url']) {
                    $uploadMetadata['CoverURL'] = $media_param['thumbnail_url'];
                }
                $uploadMetadataList[] = $uploadMetadata;
            }
        } else {
            $uploadMetadata['SourceURL'] = $source_url;
            $uploadMetadata['Title'] = $media_params['name'];
            if ($media_params['thumbnail_url']) {
                $uploadMetadata['CoverURL'] = $media_params['thumbnail_url'];
            }
            $uploadMetadataList[] = $uploadMetadata;
        }

        if (!empty($uploadMetadataList)) {
            $request['UploadMetadatas'] = json_encode($uploadMetadataList);
        }

        if (!empty($extra_data['UserData'])) {
            $request['UserData'] = json_encode($extra_data['UserData']);
        }

        try {
            return AlibabaCloud::rpc()
                ->product('vod')
                ->version(self::VOD_VERSION)
                ->action('UploadMediaByURL')
                ->method('POST')
                ->options(['query' => $request])
                ->request();
        } catch (AliClientException $exception) {
            Log::error(self::VOD_LOG.'=>'.$exception->getErrorMessage());
        } catch (ServerException $exception) {
            Log::error(self::VOD_LOG.'=>'.$exception->getErrorMessage());
        }

        return false;
    }

    public function getUploadInfoByUrl($jobId)
    {
        $request = ['JobIds' => $jobId];

        try {
            return AlibabaCloud::rpc()
                ->product('vod')
                ->version(self::VOD_VERSION)
                ->action('GetURLUploadInfos')
                ->method('POST')
                ->options(['query' => $request])
                ->request();
        } catch (AliClientException $exception) {
            Log::error(self::VOD_LOG.'=>'.$exception->getErrorMessage());
        } catch (ServerException $exception) {
            Log::error(self::VOD_LOG.'=>'.$exception->getErrorMessage());
        }

        return false;
    }

    /**
     * @param $videoId
     * @param array $video_params can be [name, thumbnail_url]
     *
     * @return bool|mixed|\SimpleXMLElement
     */
    public function updateVideoInfo($videoId, $video_params = [])
    {
        if (!$videoId) {
            return false;
        }

        $request = ['VideoId' => $videoId];
        if ($video_params['name']) {
            $request['Title'] = $video_params['name'];
        }
        if ($video_params['thumbnail_url']) {
            $request['CoverURL'] = $video_params['thumbnail_url'];
        }

        try {
            return AlibabaCloud::rpc()
                ->product('vod')
                ->version(self::VOD_VERSION)
                ->action('UpdateVideoInfo')
                ->method('POST')
                ->options(['query' => $request])
                ->request();
        } catch (AliClientException $exception) {
            Log::error(self::VOD_LOG.'=>'.$exception->getErrorMessage());
        } catch (ServerException $exception) {
            Log::error(self::VOD_LOG.'=>'.$exception->getErrorMessage());
        }

        return false;
    }

    /**
     * @param UploadedFile $file
     *
     * @return bool|mixed|\SimpleXMLElement
     */
    public function uploadLocalImage(UploadedFile $file)
    {
        $request = [
            'ImageType' => 'default',
            'ImageExt' => $file->getClientOriginalExtension(),
            'OriginalFileName' => basename($file->getClientOriginalName()),
        ];

        try {
            $uploadInfo = AlibabaCloud::rpc()
                ->product('vod')
                ->version(self::VOD_VERSION)
                ->action('CreateUploadImage')
                ->method('POST')
                ->options(['query' => $request])
                ->request();
            if ($this->uploadOssObject($file->getRealPath(), $uploadInfo)) {
                return $uploadInfo;
            }
        } catch (AliClientException $exception) {
            Log::error(self::VOD_LOG.'=>'.$exception->getErrorMessage());
        } catch (ServerException $exception) {
            Log::error(self::VOD_LOG.'=>'.$exception->getErrorMessage());
        }

        return false;
    }

    public function uploadWebImage($imageUrl)
    {
        $fileName = self::getFilename($imageUrl);
        $extension = self::getFileExtension($fileName);
        $support_extensions = ['png', 'jpg', 'jpeg', 'gif'];
        if (!in_array(strtolower($extension), $support_extensions)) {
            Log::error(self::VOD_LOG.'=>The suffix format of uploaded pictures is not supported.');

            return false;
        }

        $imagePath = $this->downWebFile($imageUrl);
        if (!$imagePath) {
            return false;
        }

        $request = [
            'ImageType' => 'default',
            'ImageExt' => $extension,
            'OriginalFileName' => basename($fileName),
        ];

        $handle = false;
        try {
            $uploadInfo = AlibabaCloud::rpc()
                ->product('vod')
                ->version(self::VOD_VERSION)
                ->action('CreateUploadImage')
                ->method('POST')
                ->options(['query' => $request])
                ->request();
            if ($this->uploadOssObject($imagePath, $uploadInfo)) {
                $handle = true;
            }
        } catch (AliClientException $exception) {
            Log::error(self::VOD_LOG.'=>'.$exception->getErrorMessage());
        } catch (ServerException $exception) {
            Log::error(self::VOD_LOG.'=>'.$exception->getErrorMessage());
        }

        if ($handle) {
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            return $uploadInfo;
        }

        return $handle;
    }

    public static function getFilename($fileUrl)
    {
        $fileUrl = urldecode($fileUrl);
        $pos = strrpos($fileUrl, '?');
        $briefPath = $fileUrl;
        if (false !== $pos) {
            $briefPath = substr($fileUrl, 0, $pos);
        }

        return $briefPath;
    }

    public static function getFileExtension($fileName)
    {
        return pathinfo($fileName, PATHINFO_EXTENSION);
    }

    private function downWebFile($fileUrl)
    {
        if (false == Storage::exists('temp')) {
            Storage::makeDirectory('temp');
        }

        try {
            $downloadLocalFile = storage_path('app/temp').'/'.Uuid::uuid1()->getHex().'.'.self::getFileExtension(self::getFilename($fileUrl));
            $client = new Client(['verify' => false]);
            $response = $client->get($fileUrl, ['save_to' => $downloadLocalFile]);
            if (200 == $response->getStatusCode()) {
                return $downloadLocalFile;
            }
        } catch (ClientException $exception) {
            Log::error(self::VOD_LOG.'=>'.$exception->getMessage());
        }

        return null;
    }

    private function uploadOssObject($filePath, $uploadInfo)
    {
        $auth = json_decode(base64_decode($uploadInfo->UploadAuth));
        $addressInfo = json_decode(base64_decode($uploadInfo->UploadAddress));
        $ossClient = new AliOSS($auth->AccessKeyId, $auth->AccessKeySecret, $addressInfo->Endpoint, false, $auth->SecurityToken);
        $ossClient->setBucket($addressInfo->Bucket);

        return $ossClient->multiUpload($addressInfo->FileName, $filePath);
    }

    /**
     * @param $videoId
     *
     * @return bool|mixed|\SimpleXMLElement
     */
    public function getVideoPlayAuth($videoId)
    {
        $request = [
            'VideoId' => $videoId,
            'AuthInfoTimeout' => self::PLAYAUTH_TIMEOUT,
        ];

        try {
            return AlibabaCloud::rpc()
                ->product('vod')
                ->version(self::VOD_VERSION)
                ->action('GetVideoPlayAuth')
                ->method('POST')
                ->options(['query' => $request])
                ->request();
        } catch (AliClientException $exception) {
            Log::error(self::VOD_LOG.'=>'.$exception->getErrorMessage());
        } catch (ServerException $exception) {
            Log::error(self::VOD_LOG.'=>'.$exception->getErrorMessage());
        }

        return false;
    }

    public function getMediaAuditResultDetail($media_id, $page_no)
    {
        $request = ['MediaId' => $media_id, 'PageNo' => $page_no];

        try {
            return AlibabaCloud::rpc()
                ->product('vod')
                ->version(self::VOD_VERSION)
                ->action('GetMediaAuditResultDetail')
                ->method('POST')
                ->options(['query' => $request])
                ->request();
        } catch (AliClientException $exception) {
            Log::error(self::VOD_LOG.'=>'.$exception->getErrorMessage());
        } catch (ServerException $exception) {
            Log::error(self::VOD_LOG.'=>'.$exception->getErrorMessage());
        }

        return false;
    }

    /**
     * @param $videoId PlatformAlivod->video_id
     *
     * @return \AlibabaCloud\Client\Result\Result|bool {RequestId, MediaId, JobId}
     */
    public function submitAIReview($videoId)
    {
        if (!$videoId) {
            return false;
        }

        $request = ['MediaId' => $videoId];
        if (config('alivod.ai_review_template')) {
            $request['TemplateId'] = config('alivod.ai_review_template');
        }

        try {
            return AlibabaCloud::rpc()
                ->product('vod')
                ->version(self::VOD_VERSION)
                ->action('SubmitAIMediaAuditJob')
                ->method('POST')
                ->options(['query' => $request])
                ->request();
        } catch (AliClientException $exception) {
            Log::error(self::VOD_LOG.'=>'.$exception->getErrorMessage());
        } catch (ServerException $exception) {
            Log::error(self::VOD_LOG.'=>'.$exception->getErrorMessage());
        }

        return false;
    }
}
