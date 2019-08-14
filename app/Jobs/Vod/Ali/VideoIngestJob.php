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

class VideoIngestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $entry;
    public $videoUrl;

    const LOG_TAG = '[vod:jobs:video_ingest]: ';

    /**
     * Create a new job instance.
     */
    public function __construct(Entry $entry, $videoUrl)
    {
        $this->entry = $entry;
        $this->videoUrl = $videoUrl;
    }

    /**
     * Execute the job.
     */
    public function handle(VodService $vodService)
    {
        try {
            $media_params = [
                'name' => $this->entry->name,
                'thumbnail_url' => $this->entry->thumbnail_url,
            ];
            $response = $vodService->uploadMediaByURL($this->videoUrl, $media_params);
            if (!empty($response->UploadJobs)) {
                $this->entry->platformAlivod->job_id = $response->UploadJobs[0]->JobId;
                $this->entry->platformAlivod->status = PlatformAlivod::STATUS_UPLOAD_QUEUED;
                $this->entry->platformAlivod->save();
            } else {
                $this->handleException();
            }
        } catch (\Exception $exception) {
            $this->handleException();
            \Log::error(self::LOG_TAG.' error=> '.$exception->getMessage()."\n".$exception->getTraceAsString());
        }
    }

    private function handleException()
    {
        $this->entry->status = Entry::STATUS_ERROR;
        $this->entry->save();
        $this->entry->platformAlivod->status = PlatformAlivod::STATUS_ERROR;
        $this->entry->platformAlivod->save();
    }
}
