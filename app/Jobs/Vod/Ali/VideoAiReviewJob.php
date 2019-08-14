<?php

namespace App\Jobs\Vod\Ali;

use Illuminate\Bus\Queueable;
use App\Services\Vod\VodService;
use App\Models\EntryAiReviewResult;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class VideoAiReviewJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $entries;

    const LOG_TAG = '[vod:jobs:video_ai_review]: ';

    /**
     * Create a new job instance.
     */
    public function __construct($entries)
    {
        $this->entries = $entries;
    }

    /**
     * Execute the job.
     */
    public function handle(VodService $vodService)
    {
        foreach ($this->entries as $entry) {
            \Log::info(self::LOG_TAG.'entry:'.$entry->id.'is begining ai review.');
            $response = $vodService->submitAIReview($entry->platformAlivod->video_id);
            $save_datas = ['jobid' => '', 'ali_status' => EntryAiReviewResult::STATUS_FAIL];
            if ($response && $response->JobId) {
                $save_datas = [
                    'jobid' => $response->JobId,
                    'ali_status' => EntryAiReviewResult::STATUS_PROCESSING,
                ];
            }
            $entry->aiReviewResult()->updateOrCreate([
                'entry_id' => $entry->id,
            ], $save_datas);
        }
    }
}
