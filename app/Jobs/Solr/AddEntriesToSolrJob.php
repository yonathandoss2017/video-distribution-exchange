<?php

namespace App\Jobs\Solr;

use Carbon\Carbon;
use App\Models\Entry;
use Illuminate\Bus\Queueable;
use App\Services\Solr\SolrService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\Solr\SolrMarketplaceSyncService;

class AddEntriesToSolrJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const MAX_DOCS_TO_ADD = 500;

    private $entryId;
    private $core;

    /**
     * Create a new job instance.
     *
     * @param $entryId
     * @param $core
     */
    public function __construct($entryId, $core)
    {
        $this->entryId = $entryId;
        $this->core = $core;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        if (false == env('SOLR_SYNC_ACTIVE', false)) {
            return;
        }

        $entries = Entry::where('id', '>', $this->entryId)
            ->withPlatformVideos()
            ->with(['content_provider', 'metadata', 'playlists'])
            ->limit(self::MAX_DOCS_TO_ADD)
            ->get();

        if ($entries->count() > 0) {
            $lastEntryId = $entries->last()->id;
            if (SolrService::CORE_MARKETPLACE == $this->core) {
                (new SolrMarketplaceSyncService())->entries($entries);
            }

            self::dispatch($lastEntryId, $this->core);
            //->delay(Carbon::now()->addSeconds(3));
        }
    }
}
