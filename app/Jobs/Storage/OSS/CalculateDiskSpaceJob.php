<?php

namespace App\Jobs\Storage\OSS;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use App\Services\Storage\Oss\AliOSS;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CalculateDiskSpaceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const MAX_KEYS = 1000;

    private $orgId;
    private $scanFolder;
    private $scanIndex;

    /**
     * Create a new job instance.
     */
    public function __construct($orgId, $scanFolder, $scanIndex = '')
    {
        $this->orgId = $orgId;
        $this->scanFolder = $scanFolder;
        $this->scanIndex = $scanIndex;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $options = [
            'delimiter' => '/',
            'prefix' => $this->scanFolder,
            'max-keys' => self::MAX_KEYS,
            'marker' => $this->scanIndex,
        ];
        $oss = new AliOSS();
        $oss->setBucket(config('oss.bucket'));
        $result = $oss->listObjects($options);
        $objectList = $result->getObjectList();
        $prefixList = $result->getPrefixList();
        $nextMarker = $result->getNextMarker();
        if (!empty($objectList)) {
            $storage_size = 0;
            foreach ($objectList as $objectInfo) {
                $storage_size += $objectInfo->getSize();
            }
            if ($storage_size > 0) {
                DB::table('organizations')->where('id', $this->orgId)->increment('storage_size_in_byte', $storage_size);
            }
        }
        if (!empty($prefixList)) {
            foreach ($prefixList as $prefixInfo) {
                dispatch(new self($this->orgId, $prefixInfo->getPrefix()));
            }
        }
        if (!empty($nextMarker)) {
            dispatch(new self($this->orgId, $this->scanFolder, $nextMarker));
        }
    }
}
