<?php

namespace App\Jobs\Vod\Ali;

use App\Models\Entry;
use Illuminate\Bus\Queueable;
use App\Models\PlatformAlivod;
use App\Services\Vod\VodService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class VideoBatchIngestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $entries;
    public $videoUrls;

    const LOG_TAG = '[vod:jobs:video_batch_ingest]: ';

    /**
     * Create a new job instance.
     */
    public function __construct($entries, $videoUrls)
    {
        $this->entries = $entries;
        $this->videoUrls = $videoUrls;
    }

    /**
     * Execute the job.
     */
    public function handle(VodService $vodService)
    {
        $entries = $this->entries->map(function ($entry) use ($vodService) {
            if ($entry->thumbnail_url) {
                $this->uploadImage($vodService, $entry);
            }

            return $entry;
        });

        $media_params = [];
        foreach ($entries as $entry) {
            $media_params[] = [
                'name' => $entry->name,
                'thumbnail_url' => $entry->thumbnail_url,
                'video_url' => $entry->video_url,
            ];
        }

        $response = $vodService->uploadMediaByURL($this->videoUrls, $media_params);
        \Log::info(self::LOG_TAG.'RequestId is:'.$response->RequestId);
        if (!empty($response->UploadJobs)) {
            foreach ($response->UploadJobs as $uploadJob) {
                $entries->first(function ($entry) use ($uploadJob) {
                    if ($entry->video_url == $uploadJob->SourceURL) {
                        $entry->platformAlivod->job_id = $uploadJob->JobId;
                        $entry->platformAlivod->status = PlatformAlivod::STATUS_UPLOAD_QUEUED;
                        $entry->platformAlivod->save();
                    }
                });
            }
        }
    }

    private function uploadImage($vodService, $entry)
    {
        $response = $vodService->uploadWebImage($entry->thumbnail_url);
        if ($response && $response->ImageId) {
            $entry->platformAlivod->cover_id = $response->ImageId;
            $entry->platformAlivod->save();

            $entry->thumbnail_url = $response->ImageURL;
            Entry::where('id', $entry->id)->update(['thumbnail_url' => $entry->thumbnail_url]);

            return true;
        } else {
            \Log::error(self::LOG_TAG.' error=> entry: '.$entry->id.'. web image upload to vod failed.');
        }

        return false;
    }
}
