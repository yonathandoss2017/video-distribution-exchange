<?php

namespace App\Jobs\Vod\Ali;

use Carbon\Carbon;
use App\Models\Entry;
use Ramsey\Uuid\Uuid;
use App\Models\Property;
use Illuminate\Bus\Queueable;
use App\Models\AiReviewVideoResult;
use App\Services\Vod\Ali\VodService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\Storage\Oss\StorageService;

class ReviewVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $review_id;
    protected $entry_id;
    protected $media_id;

    const LOG_TAG = '[vod:jobs:review_video]: ';

    /**
     * Create a new job instance.
     */
    public function __construct($review_id, $entry_id, $media_id)
    {
        $this->review_id = $review_id;
        $this->entry_id = $entry_id;
        $this->media_id = $media_id;
    }

    /**
     * Execute the job.
     */
    public function handle(VodService $vod_service, StorageService $storage_service)
    {
        $property_id = Entry::where('id', $this->entry_id)->value('property_id');
        $organization_id = Property::where('id', $property_id)->value('organization_id');
        $dst_path = $organization_id.'/'.$property_id.'/entry_'.$this->entry_id.'/';
        $max_page = 1;

        for ($page_no = 1; $page_no < $max_page + 1; ++$page_no) {
            $media_info = $vod_service->getMediaAuditResultDetail($this->media_id, $page_no);
            $review_video_results = [];
            if (!isset($media_info['MediaAuditResultDetail']['List'])) {
                break;
            }
            //Get the max page no by the total number of MediaAuditResultDetail.
            $max_page = ceil($media_info['MediaAuditResultDetail']['Total'] / 20);
            $date_time = Carbon::now();
            foreach ($media_info['MediaAuditResultDetail']['List'] as $item) {
                $pathinfo = pathinfo(parse_url($item['Url'])['path']);
                $image_path = $dst_path.Uuid::uuid1()->getHex().'.'.$pathinfo['extension'];
                if (!$storage_service->storeContent($image_path, file_get_contents($item['Url']))) {
                    $image_path = '';
                    \Log::error(self::LOG_TAG.'save image failed: dst path[.'.$dst_path.'.] image url['.$item['Url'].']');
                }

                $review_video_result = ['review_id' => $this->review_id, 'timestamp' => $item['Timestamp'], 'image_path' => $image_path];
                if (isset($item['TerrorismLabel'])) {
                    $review_video_result['terrorism_label'] = $item['TerrorismLabel'];
                }
                if (isset($item['TerrorismScore'])) {
                    $review_video_result['terrorism_score'] = $item['TerrorismScore'];
                }
                if (isset($item['PornLabel'])) {
                    $review_video_result['porn_label'] = $item['PornLabel'];
                }
                if (isset($item['PornScore'])) {
                    $review_video_result['porn_score'] = $item['PornScore'];
                }

                $review_video_result['created_at'] = $date_time;
                $review_video_result['updated_at'] = $date_time;

                $review_video_results[] = $review_video_result;
            }

            if (count($review_video_results) > 0) {
                AiReviewVideoResult::insert($review_video_results);
            }
        }
    }
}
