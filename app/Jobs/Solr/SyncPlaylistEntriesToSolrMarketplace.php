<?php

namespace App\Jobs\Solr;

use App\Models\Playlist;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\Solr\SolrMarketplaceSyncService;

class SyncPlaylistEntriesToSolrMarketplace implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const MAX_ENTRIES = 500;

    public $playlist;
    public $lastProcessingId;

    /**
     * Create a new job instance.
     *
     * @param Playlist $playlist
     * @param $lastProcessingId
     */
    public function __construct(Playlist $playlist, $lastProcessingId = 0)
    {
        $this->playlist = $playlist;
        $this->lastProcessingId = $lastProcessingId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        if (false == env('SOLR_SYNC_ACTIVE', false)) {
            return;
        }

        $playlistWithEntries = $this->playlist->loadEntries(self::MAX_ENTRIES, $this->lastProcessingId);
        $entries = $playlistWithEntries->entries;

        if (!$entries->count()) {
            return;
        }

        (new SolrMarketplaceSyncService())->entries($entries, $this->playlist);

        self::dispatch($this->playlist, $entries->last()->id);
    }
}
