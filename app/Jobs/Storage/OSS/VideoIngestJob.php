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

class VideoIngestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $sourceBucket;
    private $sourcePath;
    private $needSnapshot;

    public $entry;
    public $aliOssAuth = null;
    public $timeout = 600;          //任务运行的超时时间10分钟

    const LOG_TAG = '[oss:jobs:video_ingest]: ';

    /**
     * Create a new job instance.
     */
    public function __construct(Entry $entry, $aliOssAuth, $sourceBucket, $sourcePath, $needSnapshot)
    {
        $this->entry = $entry;
        $this->aliOssAuth = $aliOssAuth;
        $this->sourceBucket = $sourceBucket;
        $this->sourcePath = $sourcePath;
        $this->needSnapshot = $needSnapshot;
    }

    /**
     * Execute the job.
     */
    public function handle(StorageService $storageService)
    {
        try {
            $video_path = $this->getVideoPath();
            if (!is_null($this->aliOssAuth)) {
                $this->aliOssAuth->bucket = $this->sourceBucket;
                $storageService->import($this->aliOssAuth, $this->sourcePath, $video_path);
            } else {
                $storageService->copy($this->sourcePath, $video_path);
            }
        } catch (\Exception $exception) {
            $this->handleException();
            \Log::error(self::LOG_TAG.' error=> '.$exception->getMessage()."\n".$exception->getTraceAsString());
        }
    }

    private function getVideoPath()
    {
        $filename = md5(pathinfo($this->sourcePath)['filename']);
        $extension = pathinfo($this->sourcePath)['extension'];
        if (false !== strpos($extension, '?')) {
            $extArr = explode('?', $extension);
            $extension = $extArr[0];
        }
        $organization_id = Property::find($this->entry->property_id)->organization_id;
        $video_path = $organization_id.'/'.$this->entry->property_id.'/entry_'.$this->entry->id.'/'.$filename.'.'.$extension;

        return $video_path;
    }

    private function handleException()
    {
        $this->entry->status = Entry::STATUS_ERROR;
        $this->entry->save();
    }
}
