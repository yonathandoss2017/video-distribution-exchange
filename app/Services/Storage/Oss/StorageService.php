<?php

namespace App\Services\Storage\Oss;

use Carbon\Carbon;
use OSS\OssClient;
use Ramsey\Uuid\Uuid;
use App\Models\Organization;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Services\Storage\StorageService as StorageServiceInterface;

class StorageService implements StorageServiceInterface
{
    private $oss;

    const PIECESIZE = 500 * 1024 * 1024;     //每次分片上传的文件大小为500M
    const LOG_TAG = '[storage:oss]: ';

    public function __construct()
    {
        $this->oss = new AliOSS();
        $this->oss->setBucket(config('oss.bucket'));
    }

    /**
     * @param UploadedFile $file
     * @param string       $filePath
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UploadedFile $file, $filePath = '')
    {
        $pathImg = empty($filePath) ? 'images/'.Carbon::now()->format('Y/m/d') : $filePath;
        $newFilename = md5($file->getClientOriginalName().time().rand(1, 10000)).'.'.$file->getClientOriginalExtension();
        $object = $pathImg.'/'.$newFilename;

        $result = $this->oss->upload($object, $file->getPathname());

        if ($result) {
            $this->CalculateSingleFileDiskSpace($object);

            return response()->json(['statusCode' => 200, 'pathImg' => $object]);
        }

        return response()->json(['statusCode' => 404, 'pathImg' => null]);
    }

    /**
     * @param $key
     * @param $content
     *
     * @return bool|null
     */
    public function storeContent($key, $content)
    {
        return $this->oss->uploadContent($key, $content);
    }

    /**
     * @param $fromSourcePath
     * @param $targetSourcePath
     *
     * @return bool|null
     */
    public function multiStore($fromSourcePath, $targetSourcePath)
    {
        return $this->oss->multiUpload($targetSourcePath, $fromSourcePath, [OssClient::OSS_PART_SIZE => self::PIECESIZE]);
    }

    /**
     * @param $source
     *
     * @return bool
     */
    public function delete($source)
    {
        $data = $this->parseSourcePath($source);
        if ($this->exist($source)) {
            $size = $this->getSize($source);
            $delete_result = $this->oss->deleteObject($data['object'], $data['bucket']);
            if ($delete_result) {
                $this->CalculateSingleFileDiskSpace($data['object'], true, $size);

                return true;
            }

            return false;
        }

        return true;
    }

    /**
     * @param $source
     *
     * @return bool
     */
    public function exist($source)
    {
        $source = $this->parseSourcePath($source);

        return $this->oss->doesObjectExist($source['object'], $source['bucket']);
    }

    /**
     * @param $fromSource
     * @param $toSource
     *
     * @return bool
     */
    public function move($fromSource, $toSource)
    {
        $fromSourceArr = $this->parseSourcePath($fromSource);
        $toObject = $this->getTargetObject($fromSourceArr['object'], $toSource);
        $copy_result = $this->oss->copyObject($fromSourceArr['object'], $toObject, $fromSourceArr['bucket']);
        if ($copy_result) {
            $delete_result = $this->delete($fromSource);
            if ($delete_result) {
                $this->CalculateSingleFileDiskSpace($toObject);

                return true;
            }

            return false;
        } else {
            return false;
        }
    }

    /**
     * @param $fromSource
     * @param $toSource
     *
     * @return bool|string
     */
    public function copy($fromSource, $toSource)
    {
        $video_size = $this->getSize($fromSource);

        $fromSource = $this->parseSourcePath($fromSource);
        $toObject = $this->getTargetObject($fromSource['object'], $toSource);

        //大于1G的文件需要分片拷贝
        if ($video_size / (1024 * 1024 * 1024) <= 1) {
            $copy_result = $this->oss->copyObject($fromSource['object'], $toObject, $fromSource['bucket']);
        } else {
            $uploadId = $this->oss->initiateMultipartUpload($toObject);
            \Log::info(self::LOG_TAG.'uploadId => '.$uploadId);

            $pieces = $this->oss->generateMultiuploadParts($video_size, self::PIECESIZE);

            $upload_parts = [];

            foreach ($pieces as $i => $piece) {
                \Log::info(self::LOG_TAG.'i => '.$i.'; seekTo => '.$piece['seekTo'].'; length => '.$piece['length']);
                $options = [
                    'start' => (int) $piece['seekTo'],
                    'end' => (int) $piece['length'] + (int) $piece['seekTo'] - 1,
                ];
                $eTag = $this->oss->uploadPartCopy($fromSource['bucket'], $fromSource['object'], $this->oss->getBucket(), $toObject, $i + 1, $uploadId, $options);
                \Log::info(self::LOG_TAG.'eTag => '.$eTag);
                $upload_parts[] = [
                    'PartNumber' => $i + 1,
                    'ETag' => $eTag,
                ];
            }

            $copy_result = $this->oss->completeMultipartUpload($toObject, $uploadId, $upload_parts);
        }

        if ($copy_result) {
            return true;
        } else {
            return false;
        }
    }

    public function import($aliOssAuth, $fromSource, $toSource)
    {
        $oauth = $aliOssAuth;
        if (is_null($oauth)) {
            return false;
        }

        if (false == Storage::exists('temp')) {
            Storage::makeDirectory('temp');
        }

        $downloadLocalFile = storage_path('app/temp').'/'.Uuid::uuid1()->getHex().'.'.pathinfo($fromSource)['extension'];

        $oss = new AliOSS($oauth->api_key, $oauth->api_secret, $oauth->oss_internal_endpoint);
        $res = $oss->getObject($fromSource, $oauth->bucket, [
            OssClient::OSS_FILE_DOWNLOAD => $downloadLocalFile,
        ]);

        $flag = false;
        if (false !== $res) {
            $res = $this->multiStore($downloadLocalFile, $toSource);
            if ($res) {
                $flag = true;
            }
        }

        if (file_exists($downloadLocalFile)) {
            $stat = unlink($downloadLocalFile);
            if (true !== $stat) {
                throw new \ErrorException($downloadLocalFile.' video file delete failed.');
            }
        } else {
            throw new \ErrorException($downloadLocalFile.' video file not exists.');
        }

        return $flag;
    }

    /**
     * @param $source
     *
     * @return int
     */
    public function getSize($source)
    {
        $source = $this->parseSourcePath($source);
        $result = $this->oss->getObjectMeta($source['object'], $source['bucket']);
        if ($result) {
            return $result['content-length'];      //byte
        }

        return 0;
    }

    /**
     * @param $source
     *
     * @return array
     */
    public function getInfo($source)
    {
        $source = $this->parseSourcePath($source);

        return $this->oss->getObjectMeta($source['object'], $source['bucket']);
    }

    /**
     * @param null $organization
     */
    public function CalculateDiskSpace($organization = null, $dirPath = null)
    {
        if (empty($organization)) {
            $organization = Organization::Organization();
        }
        $orgId = empty($organization) ? 0 : $organization->id;
        \Log::info(self::LOG_TAG.'orgId => '.$orgId);
        Artisan::call('storage:getSize', ['organization' => $orgId, 'dirPath' => $dirPath]);
    }

    /**
     * @return array
     */
    public function requestUploadPolicy()
    {
        $key = $this->oss->getKeySecret();
        $end = Carbon::now()->addHour();
        $expiration = $end->toIso8601ZuluString(); //设置该policy超时时间是1h. 即这个policy过了这个有效时间，将不能访问

        $dir = 'tmp/';

        //最大文件大小.用户可以自己设置,最大设置为100G
        $condition = [0 => 'content-length-range', 1 => 0, 2 => 1024 * 1024 * 1024 * 100];
        $conditions[] = $condition;

        //表示用户上传的数据,必须是以$dir开始, 不然上传会失败,这一步不是必须项,只是为了安全起见,防止用户通过policy上传到别人的目录
        /* $start = [0 => 'starts-with', 1 => '$key', 2 => $dir];
        $conditions[] = $start; */

        $arr = ['expiration' => $expiration, 'conditions' => $conditions];

        $policy = json_encode($arr);
        $base64_policy = base64_encode($policy);
        $string_to_sign = $base64_policy;
        $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $key, true));

        return [
            'accessid' => $this->oss->getKeyId(),
            'host' => $this->oss->getHost(),
            'policy' => $base64_policy,
            'signature' => $signature,
            'expire' => $end->timestamp,
            'key' => $dir, //这个参数是设置用户上传指定的前缀
        ];
    }

    /**
     * @param $url
     *
     * @return bool
     */
    public function isValidUrl($url)
    {
        if (false === strpos(strtolower($url), $this->oss->getEndpoint())) {
            return false;
        }

        return true;
    }

    /**
     * @param $source
     *
     * @return array
     */
    private function parseSourcePath($source)
    {
        $source = urldecode($source);
        $bucket = $this->oss->getBucket();
        if (preg_match("/^(http:\/\/|https:\/\/).*$/", $source)) {
            $urlArr = explode('/', $source);
            $strArr = explode('.', $urlArr[2]);
            $bucket = $strArr[0];

            $object = '';
            for ($i = 3; $i < count($urlArr); ++$i) {
                $object .= '/'.$urlArr[$i];
            }

            $object = ltrim($object, '/');
            if (false !== strpos($object, '?')) {
                $pathArr = explode('?', $object);
                $object = $pathArr[0];
            }
        } else {
            $object = $source;
        }
        \Log::info(self::LOG_TAG.'bucket => '.$bucket.';object=>'.$object);

        return [
            'bucket' => $bucket,
            'object' => $object,
        ];
    }

    /**
     * @param $fromObject
     * @param $toSource
     *
     * @return string
     */
    private function getTargetObject($fromObject, $toSource)
    {
        if (isset($toSource['target_dir'])) {
            $pathArr = explode('/', $fromObject);
            $toObject = $toSource['target_dir'].$pathArr[count($pathArr) - 1];
        } elseif (isset($toSource['target_path'])) {
            $toObject = $toSource['target_path'];
        } else {
            $toObject = $toSource;
        }

        return $toObject;
    }

    private function CalculateSingleFileDiskSpace($filePath, $isDelete = false, $size = 0)
    {
        if (starts_with($filePath, 'tmp/')) {
            return;
        }
        $organization = Organization::Organization();
        if (is_null($organization)) {
            return;
        }
        if ($size <= 0) {
            $size = $this->getSize($filePath);
        }
        if ($size > 0) {
            if ($isDelete) {
                $organization->storage_size_in_byte -= $size;
            } else {
                $organization->storage_size_in_byte += $size;
            }
            $organization->save();
        }
    }
}
