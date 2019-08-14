<?php

namespace App\Services;

use App\Models\Entry;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class VideoService
{
    private $imageService;

    public function __construct(FeaturedImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public static function getEntryProperty($property_id, $entry_id)
    {
        return Entry::with('playlists', 'metadata')
                        ->with(['properties' => function ($query) use ($property_id) {
                            $query->where('property_id', $property_id);
                        }])
                        ->find($entry_id);
    }

    public static function getEntryData($video)
    {
        if (count($video->properties) > 0) {
            $properties = $video->properties->first()->pivot;
            $video->name = $properties->title;
            $video->description = $properties->description;
            $video->image_path = $properties->image_path;
        }

        return $video;
    }

    public function updateVideo(Request $request, $property_id, $entry_id)
    {
        //$video = Entry::with('properties')->findOrFail($entry_id);
        $video = self::getEntryProperty($property_id, $entry_id);
        $imageFile = $this->imageService->update($request, $video->properties->first()->pivot->image_path ?? null, FeaturedImageService::getImageSavePath([
            'property_id' => $property_id,
            'entry_id' => $entry_id,
        ]));
        $data = [
            'title' => $request->title,
            'description' => $request->description,
        ];

        if ($imageFile) {
            $data['image_path'] = $imageFile;
        }

        $video->properties()->syncWithoutDetaching([
            $property_id => $data,
        ]);
    }

    public function calculatePlatformsCount(Collection $videos)
    {
        $count = 0;
        foreach ($videos as $video) {
            $count = $this->calculatePlatformCount($video, $count);
        }

        return $count;
    }

    public function calculatePlatformCount(Entry $video, $count = 0): int
    {
        $count += $video->platform_youtube_count;
        $count += $video->platform_ivideostream_count;
        $count += $video->platform_dailymotion_count;
        $count += $video->platform_hungama_count;

        return $count;
    }
}
