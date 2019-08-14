<?php

namespace App\Services\Serve;

use App\Models\Entry;
use App\Models\Property;
use App\Models\PlatformAlivod;
use App\Services\Vod\Ali\VodService;

class VideoPlayerService
{
    public static function player(Property $property, Entry $entry, $container, $autoplay = 'true')
    {
        if (!is_null($entry->platformAlivod) && PlatformAlivod::STATUS_TRANSCODE_COMPLETE == $entry->platformAlivod->status) {
            $return_data = [
                'container' => $container,
                'videoId' => $entry->platformAlivod->video_id,
                'autoplay' => $autoplay,
                'playauth' => '',
            ];
            $vod = new VodService();
            $response = $vod->getVideoPlayAuth($entry->platformAlivod->video_id);
            if ($response && $response->PlayAuth) {
                $return_data['playauth'] = $response->PlayAuth;
            }

            return view('video_player.javascript.alivod', $return_data);
        } else {
            return;
        }
    }
}
