<?php

namespace App\Jobs;

use Mockery\Exception;
use App\Models\Property;
use App\Models\PropertyCP;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PropertyEntryDeletionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $property;

    const LOG_TAG = '[Job:Property_Entry:Delete]: ';

    public function __construct(PropertyCP $property)
    {
        $this->property = $property;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            $entries = $this->property->entries()->take(50)->get();
            if ($entries->count() > 0) {
                foreach ($entries as $entry) {
                    $entry->delete();
                }
                if ($entries->count() >= 50) {
                    return dispatch(new self($this->property));
                }
            }
            $this->property->delete();
        } catch (Exception $e) {
            \Log::error(self::LOG_TAG.'handle exception '.$e->getMessage());
            $this->property->retry_count = $this->property->retry_count + 1;
            if (10 == $this->property->retry_count) {
                $this->property->status = Property::STATUS_DELETE_FAILED;
            } else {
                dispatch(new self($this->property));
            }
            $this->property->save();
        }
    }
}
