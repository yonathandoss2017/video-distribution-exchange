<?php

namespace App\Services;

use App\Models\Organization;
use Illuminate\Http\Request;
use App\Services\Storage\Oss\UrlService;
use App\Services\Storage\StorageService;

class FeaturedImageService
{
    private $storageService;

    public function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    /**
     * Do not use this function to get Playlist or Video Images.
     * Use PlaylistImageService or VideoImageService instead.
     * This will only return images from our IVX S3 Bucket.
     */
    public static function generateImageUrl($path)
    {
        if (!$path) {
            return null;
        }

        return UrlService::getUrl($path);
    }

    public static function getHtml($imagePath = null, $imageUrl = null)
    {
        return view('partials.featured_image', compact('imagePath', 'imageUrl'))->render();
    }

    public static function getValidator()
    {
        return ['imagefile' => 'mimes:jpeg,png|max:10240'];
    }

    private function storeImage(Request $request, $filePath)
    {
        if ($request->hasFile('imagefile')) {
            $imageFile = $this->storageService->store($request->file('imagefile'), $filePath);
            if (200 == $imageFile->getData()->statusCode) {
                return $imageFile->getData()->pathImg;
            } else {
                session()->flash('error', __('manage/cp/common.featured_image_invalid'));

                return 'error';
            }
        }

        return null;
    }

    public function update(Request $request, $oldImage, $filePath = null)
    {
        $imagefile = $this->storeImage($request, $filePath);
        if ($imagefile && 'error' != $imagefile && $oldImage) {
            $this->storageService->delete($oldImage);
        }

        return $imagefile;
    }

    public function store(Request $request, $filePath = null)
    {
        return $this->storeImage($request, $filePath);
    }

    public static function getImageSavePath($params = [])
    {
        if (!empty($params['org_id'])) {
            $newPath = $params['org_id'].'/';
        } else {
            $newPath = Organization::Organization()->id.'/';
        }
        if (!empty($params['property_id'])) {
            $newPath .= $params['property_id'].'/';
        }
        if (!empty($params['playlist_id'])) {
            $newPath .= 'playlist_'.$params['playlist_id'];
        }
        if (!empty($params['entry_id'])) {
            $newPath .= 'entry_'.$params['entry_id'];
        }

        return $newPath;
    }
}
