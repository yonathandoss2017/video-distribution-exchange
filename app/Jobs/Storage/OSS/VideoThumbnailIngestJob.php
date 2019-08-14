<?php

namespace App\Jobs\Storage\OSS;

use App\Models\Entry;
use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use App\Services\Storage\StorageService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class VideoThumbnailIngestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $sourceBucket;
    private $sourcePath;

    public $entry;
    public $aliOssAuth = null;
    public $timeout = 600;          //任务运行的超时时间10分钟

    const LOG_TAG = '[oss:jobs:video_thumbnail_ingest]: ';

    /**
     * Create a new job instance.
     */
    public function __construct(Entry $entry, $aliOssAuth, $sourceBucket, $sourcePath)
    {
        $this->entry = $entry;
        $this->aliOssAuth = $aliOssAuth;
        $this->sourceBucket = $sourceBucket;
        $this->sourcePath = $sourcePath;
    }

    /**
     * Execute the job.
     */
    public function handle(StorageService $storageService)
    {
        try {
            $thumbnail_path = $this->getThumbnailPath();
            $this->aliOssAuth->bucket = $this->sourceBucket;
            $res = $storageService->import($this->aliOssAuth, $this->sourcePath, $thumbnail_path);
            if ($res) {
                $this->entry->image_path = $thumbnail_path;
                $this->entry->save();
            }
        } catch (\Exception $exception) {
            \Log::error(self::LOG_TAG.' error=> '.$exception->getMessage()."\n".$exception->getTraceAsString());
        }
    }

    private function getThumbnailPath()
    {
        $filename = md5(pathinfo($this->sourcePath)['filename']);
        $extension = pathinfo($this->sourcePath)['extension'];
        if (false !== strpos($extension, '?')) {
            $extArr = explode('?', $extension);
            $extension = $extArr[0];
        }
        $organization_id = Property::find($this->entry->property_id)->organization_id;
        $thumbnail_path = $organization_id.'/'.$this->entry->property_id.'/entry_'.$this->entry->id.'/'.$filename.'.'.$extension;

        return $thumbnail_path;
    }
}
