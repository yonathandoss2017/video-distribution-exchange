<?php

namespace App\Jobs\Solr;

use App\Models\Entry;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\Solr\SolrMarketplaceSyncService;

class SyncEntryToSolrJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var
     */
    public $entryId;
    public $cores;

    /**
     * Create a new job instance.
     *
     * @param $entryId
     * @param $cores as array
     */
    public function __construct($entryId, $cores)
    {
        $this->entryId = $entryId;
        $this->cores = $cores;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        if (false == env('SOLR_SYNC_ACTIVE', false)) {
            return;
        }

        Log::info("Sync entry to solr [entry_id][{$this->entryId}]");

        $cores = $this->cores;
        foreach ($cores as $core) {
            $entry = Entry::withTrashed()->findOrFail($this->entryId);
            $entry->load(['playlists', 'platformAlivod']);

            $doc = null;
            if ('marketplace' == $core) {
                if ($entry->hasPlatform(Entry::PLATFORM_ALIVOD) || $entry->trashed()) {
                    Log::info("Sync entry to solr marketplace [entry_id][{$this->entryId}]");
                    (new SolrMarketplaceSyncService())->entry($entry);
                }
            }
        }
    }
}
