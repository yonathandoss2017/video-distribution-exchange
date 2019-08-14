<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/7/31
 * Time: 14:01.
 */

namespace App\Services\Storage\Oss;

use OSS\OssClient;
use OSS\Core\OssException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AliOSS
{
    const OSS_LOG = 'oss_log';

    const EXPIRE_TIMEOUT = 86400;       //file expired timeout 1d

    private $client;
    private $keyId = null;
    private $keySecret = null;
    private $endpoint = null;
    private $location = null;
    private $bucket = null;

    public function __construct($keyId = null, $keySecret = null, $endpoint = null, $isCName = false, $securityToken = null)
    {
        $this->keyId = $keyId ?? config('oss.key_id');
        $this->keySecret = $keySecret ?? config('oss.key_secret');
        $this->endpoint = $endpoint ?? config('oss.internal_endpoint');
        if ('local' == app()->environment()) {
            //we need change internal endpoint to outer endpoint for local environment
            $this->endpoint = str_replace('-internal', '', $this->endpoint);
        }
        if (!$this->isSameRegion($this->endpoint)) {
            $this->endpoint = str_replace('-internal', '', $this->endpoint);
        }

        $this->client = new OssClient($this->keyId, $this->keySecret, $this->endpoint, $isCName, $securityToken);
    }

    public function checkConnection()
    {
        return $this->listBuckets();
    }

    /**
     * upload file.
     */
    public function upload($filename, $filepath)
    {
        if (is_null($this->bucket)) {
            return false;
        }
        try {
            return $this->client->uploadFile($this->bucket, $filename, $filepath);
        } catch (OssException $exception) {
            Log::error(self::OSS_LOG.'=>'.$exception->getMessage());

            return false;
        }
    }

    /**
     *upload content.
     */
    public function uploadContent($key, $content)
    {
        if (is_null($this->bucket)) {
            return false;
        }
        try {
            return $this->client->putObject($this->bucket, $key, $content);
        } catch (OssException $exception) {
            Log::error(self::OSS_LOG.'=>'.$exception->getMessage());

            return false;
        }
    }

    /**
     * multipart upload file.
     */
    public function multiUpload($key, $fromPath, $options = [])
    {
        if (is_null($this->bucket)) {
            return false;
        }
        try {
            return $this->client->multiuploadFile($this->bucket, $key, $fromPath, $options);
        } catch (OssException $exception) {
            Log::error(self::OSS_LOG.'=>'.$exception->getMessage());

            return false;
        }
    }

    /**
     * check object whether exist in Oss server.
     */
    public function doesObjectExist($key, $bucket = '')
    {
        if (is_null($this->bucket) && empty($bucket)) {
            return false;
        }
        try {
            $bucket = empty($bucket) ? $this->bucket : $bucket;

            return $this->client->doesObjectExist($bucket, $key);
        } catch (OssException $exception) {
            Log::error(self::OSS_LOG.'=>'.$exception->getMessage());

            return false;
        }
    }

    /**
     * list all objects from bucket.
     */
    public function listObjects($options = [])
    {
        if (is_null($this->bucket)) {
            return false;
        }
        try {
            return $this->client->listObjects($this->bucket, $options);
        } catch (OssException $exception) {
            Log::error(self::OSS_LOG.'=>'.$exception->getMessage());

            return false;
        }
    }

    /**
     * list all buckets.
     */
    public function listBuckets($options = [])
    {
        try {
            return $this->client->listBuckets($options)->getBucketList();
        } catch (OssException $exception) {
            Log::error(self::OSS_LOG.'=>'.$exception->getMessage());

            return false;
        }
    }

    public function getObject($key, $bucket = '', $options = [])
    {
        try {
            if (empty($bucket)) {
                $bucket = $this->getBucket();
            }

            return $this->client->getObject($bucket, $key, $options);
        } catch (OssException $exception) {
            Log::error(self::OSS_LOG.'=>'.$exception->getMessage());

            return false;
        }
    }

    /**
     * get object meta message.
     */
    public function getObjectMeta($key, $bucket = '')
    {
        if (is_null($this->bucket) && empty($bucket)) {
            return false;
        }
        try {
            if (empty($bucket)) {
                $bucket = $this->bucket;
            }

            return $this->client->getObjectMeta($bucket, $key);
        } catch (OssException $exception) {
            Log::error(self::OSS_LOG.'=>'.$exception->getMessage());

            return false;
        }
    }

    /**
     * get the object sign url.
     */
    public function signUrl($key, $options = [])
    {
        if (is_null($this->bucket)) {
            return false;
        }
        try {
            $request = Request::capture();
            $this->client->setUseSSL($request->secure());

            return $this->client->signUrl($this->bucket, $key, self::EXPIRE_TIMEOUT, 'GET', $options);
        } catch (OssException $exception) {
            Log::error(self::OSS_LOG.'=>'.$exception->getMessage());

            return false;
        }
    }

    /**
     * 拷贝一个在OSS上已经存在的object成另外一个object.
     */
    public function copyObject($fromObject, $toObject, $fromBucket = '', $toBucket = '')
    {
        try {
            if (empty($fromBucket)) {
                $fromBucket = $this->bucket;
            }
            if (empty($toBucket)) {
                $toBucket = $this->bucket;
            }
            if (empty($fromBucket) || empty($toBucket)) {
                return false;
            }

            return $this->client->copyObject($fromBucket, $fromObject, $toBucket, $toObject);
        } catch (OssException $exception) {
            Log::error(self::OSS_LOG.'=>'.$exception->getMessage());

            return false;
        }
    }

    /**
     * 删除某个Object.
     */
    public function deleteObject($object, $bucket = '')
    {
        if (is_null($this->bucket) && empty($bucket)) {
            return false;
        }
        try {
            $bucket = empty($bucket) ? $this->bucket : $bucket;

            return $this->client->deleteObject($bucket, $object);
        } catch (OssException $exception) {
            Log::error(self::OSS_LOG.'=>'.$exception->getMessage());

            return false;
        }
    }

    /**
     * 初始化一个分块上传事件, 获取upload id.
     *
     * @param $object
     *
     * @return bool|string
     */
    public function initiateMultipartUpload($object)
    {
        if (is_null($this->bucket)) {
            return false;
        }
        try {
            return $this->client->initiateMultipartUpload($this->bucket, $object);
        } catch (OssException $exception) {
            Log::error(self::OSS_LOG.'=>'.$exception->getMessage());

            return false;
        }
    }

    /**
     * 计算文件可以分成多少个part.
     *
     * @param $uploadFileSize
     * @param $partSize
     *
     * @return array|bool
     */
    public function generateMultiuploadParts($uploadFileSize, $partSize)
    {
        try {
            return $this->client->generateMultiuploadParts($uploadFileSize, $partSize);
        } catch (OssException $exception) {
            Log::error(self::OSS_LOG.'=>'.$exception->getMessage());

            return false;
        }
    }

    /**
     * 分块上传拷贝.
     *
     * @param $fromBucket
     * @param $fromObject
     * @param $toBucket
     * @param $toObject
     * @param $partNumber
     * @param $uploadId
     *
     * @return bool|null
     */
    public function uploadPartCopy($fromBucket, $fromObject, $toBucket, $toObject, $partNumber, $uploadId, $options)
    {
        if (empty($fromBucket) || empty($toBucket)) {
            return false;
        }
        try {
            return $this->client->uploadPartCopy($fromBucket, $fromObject, $toBucket, $toObject, $partNumber, $uploadId, $options);
        } catch (OssException $exception) {
            Log::error(self::OSS_LOG.'=>'.$exception->getMessage());

            return false;
        }
    }

    /**
     * 检测完成上传.
     *
     * @param $object
     * @param $uploadId
     * @param $listParts
     *
     * @return bool|null
     */
    public function completeMultipartUpload($object, $uploadId, $listParts)
    {
        if (is_null($this->bucket)) {
            return false;
        }
        try {
            return $this->client->completeMultipartUpload($this->bucket, $object, $uploadId, $listParts);
        } catch (OssException $exception) {
            Log::error(self::OSS_LOG.'=>'.$exception->getMessage());

            return false;
        }
    }

    /**
     * @return \Illuminate\Config\Repository|mixed|null
     */
    public function getKeyId()
    {
        return $this->keyId;
    }

    /**
     * @return \Illuminate\Config\Repository|mixed|null
     */
    public function getKeySecret()
    {
        return $this->keySecret;
    }

    /**
     * @return \Illuminate\Config\Repository|mixed|null
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return \Illuminate\Config\Repository|mixed|null
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @return \Illuminate\Config\Repository|mixed|null
     */
    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * @param $bucket
     */
    public function setBucket($bucket)
    {
        $this->bucket = $bucket;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return config('oss.cname_endpoint');
    }

    /**
     * @param $endpoint
     *
     * @return bool
     */
    private function isSameRegion($endpoint)
    {
        if (starts_with($endpoint, config('oss.location'))) {
            return true;
        }

        return false;
    }
}
