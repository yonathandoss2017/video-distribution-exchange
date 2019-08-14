<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/9/5
 * Time: 14:31.
 */

namespace App\Services\Storage;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface StorageService
{
    public function store(UploadedFile $uploadedFile, $filePath);

    public function storeContent($key, $content);

    public function multiStore($fromPath, $toPath);

    public function delete($source);

    public function exist($source);

    public function move($fromSource, $toSource);

    public function copy($fromSource, $toSource);

    public function import($thirdPartySource, $fromSource, $toSource);

    public function getInfo($source);

    public function getSize($source);

    public function CalculateDiskSpace($organization, $dirPath);
}
