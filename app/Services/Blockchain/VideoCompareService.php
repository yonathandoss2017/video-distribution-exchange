<?php

namespace App\Services\Blockchain;

use App\Models\Entry;
use GuzzleHttp\Client;
use App\Models\PropertyCP;
use App\Models\VideoCompare;
use Illuminate\Support\Facades\Log;
use App\Services\Storage\Oss\UrlService;

class VideoCompareService
{
    const LOG_TAG = '[services:video_compare]: ';

    public function requestVideoCompare(PropertyCP $property, $request)
    {
        try {
            if (Entry::VIDEO_OSS_URL == $request->video_method) {
                $downloadUrl = $request->video_url;
            } else {
                $downloadUrl = UrlService::getUrl($request->video_path);
            }

            $client = new Client();
            $response = $client->request('POST', config('fingerprint.search_url'), [
                'form_params' => [
                    'organization_id' => $property->organization_id,
                    'download_url' => $downloadUrl,
                    'callback_url' => route('api.fingerprint.compare.notification'),
                ],
            ]);

            if (200 == $response->getStatusCode()) {
                Log::info(self::LOG_TAG.'video request search fingerprint compare response: '.$response->getBody());
                $result = json_decode($response->getBody(), true);
                if ('success' == $result['status']) {
                    VideoCompare::create([
                        'property_id' => $property->id,
                        'title' => $request->title,
                        'video_url' => $downloadUrl,
                        'job_id' => $result['job_id'],
                        'status' => VideoCompare::STATUS_PROCESSING,
                    ]);

                    session()->flash('success', __('manage/cp/video-compare/compare.video_compare_request_submitted_success'));

                    return true;
                } else {
                    session()->flash('error', __('manage/cp/video-compare/compare.video_compare_request_submitted_failed'));
                }
            }
        } catch (\Exception $exception) {
            Log::error(self::LOG_TAG.'handle exception '.$exception->getMessage());

            session()->flash('error', __('manage/cp/video-compare/compare.video_compare_request_submitted_failed'));
        }

        return false;
    }
}
