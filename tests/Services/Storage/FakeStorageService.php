<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/9/5
 * Time: 14:58.
 */

namespace Tests\Services\Storage;

use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Support\Facades\Storage;
use App\Services\Storage\StorageService;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FakeStorageService implements StorageService
{
    public function store(UploadedFile $file, $filePath)
    {
        $pathImg = empty($filePath) ? 'images/'.Carbon::now()->format('Y/m/d') : $filePath;
        $newFilename = md5($file->getClientOriginalName().time().rand(1, 10000)).'.'.$file->getClientOriginalExtension();
        $object = $pathImg.'/'.$newFilename;

        $result = Storage::disk('storage')->put($object, $file);

        if ($result) {
            return response()->json(['statusCode' => 200, 'pathImg' => $object]);
        }

        return response()->json(['statusCode' => 404, 'pathImg' => null]);
    }

    public function storeContent($key, $content)
    {
        // TODO: Implement storeContent() method.
    }

    public function multiStore($fromPath, $toPath)
    {
        // TODO: Implement multiStore() method.
    }

    public function delete($object)
    {
        return Storage::disk('storage')->delete($object);
    }

    public function exist($object)
    {
        return Storage::disk('storage')->exists($object);
    }

    public function move($fromObject, $toObject)
    {
        if ($this->exist($fromObject)) {
            return true;
        }

        return false;
    }

    public function copy($fromObject, $toObject)
    {
        if ($this->exist($fromObject)) {
            return true;
        }

        return false;
    }

    public function import($thirdPartySource, $fromSource, $toSource)
    {
        // TODO: Implement import() method.
    }

    public function getSize($object)
    {
        return Storage::disk('storage')->size($object);
    }

    public function getInfo($object)
    {
        return Storage::disk('storage')->size($object);
    }

    public function requestUploadPolicy()
    {
        $faker = Factory::create();

        return [
            'accessid' => $faker->uuid,
            'host' => $faker->url,
            'policy' => $faker->uuid,
            'signature' => $faker->uuid,
            'expire' => $faker->unixTime,
            'key' => $faker->word, //这个参数是设置用户上传指定的前缀
        ];
    }

    public function parsePath($request)
    {
        return [
            'bucket' => 'bucket',
            'path' => 'tmp/'.$request->video_name,
        ];
    }

    public function isValidUrl($url)
    {
        return true;
    }

    public function CalculateDiskSpace($organization, $dirPath)
    {
        // TODO: Implement CalculateDiskSpace() method.
    }
}
