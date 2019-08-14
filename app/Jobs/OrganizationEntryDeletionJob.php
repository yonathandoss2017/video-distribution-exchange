<?php

namespace App\Jobs;

use Mockery\Exception;
use App\Models\Organization;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class OrganizationEntryDeletionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $organization;

    const LOG_TAG = '[Job:Organization_Entry:Delete]: ';

    public function __construct(Organization $organization)
    {
        $this->organization = $organization;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            $entries = $this->organization->entries()->take(50)->get();
            if ($entries->count() > 0) {
                foreach ($entries as $entry) {
                    $entry->delete();
                }
                if ($entries->count() >= 50) {
                    return dispatch(new self($this->organization));
                }
            }
            $this->organization->delete();
        } catch (Exception $e) {
            \Log::error(self::LOG_TAG.'handle exception '.$e->getMessage());
            $this->organization->retry_count = $this->organization->retry_count + 1;
            if (10 == $this->organization->retry_count) {
                $this->organization->status = Organization::STATUS_DELETE_FAILED;
            } else {
                dispatch(new self($this->organization));
            }
            $this->organization->save();
        }
    }
}
