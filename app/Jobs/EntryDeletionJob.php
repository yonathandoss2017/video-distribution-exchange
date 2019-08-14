<?php

namespace App\Jobs;

use Log;
use App\Models\Entry;
use Mockery\Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class EntryDeletionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $entryIds;

    const LOG_TAG = '[Job:Entry:Delete]: ';

    public function __construct(array $entryIds)
    {
        $this->entryIds = $entryIds;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        if (empty($this->entryIds)) {
            return;
        }

        try {
            $entries = Entry::whereIn('id', $this->entryIds)
                ->take(50)
                ->get();

            if ($entries->count() > 0) {
                Log::info(self::LOG_TAG.'Deleting '.$entries->count().' entries out of '.count($this->entryIds).' entries');
                foreach ($entries as $entry) {
                    $entry->delete();
                    unset($this->entryIds[array_search($entry->id, $this->entryIds)]);
                }
                Log::info(self::LOG_TAG.count($this->entryIds).' entries left to be deleted');
                dispatch(new self($this->entryIds));
            }
        } catch (Exception $e) {
            Log::error(self::LOG_TAG.'handle exception '.$e->getMessage());
        }
    }
}
