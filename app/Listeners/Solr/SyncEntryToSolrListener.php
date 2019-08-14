<?php

namespace App\Listeners\Solr;

use App\Models\Entry;
use App\Services\Solr\SolrService;
use App\Jobs\Solr\SyncEntryToSolrJob;
use App\Jobs\Solr\SyncPlaylistToSolrJob;

class SyncEntryToSolrListener
{
    /**
     * Handle the event.
     *
     * @param $event
     */
    public function handle($event)
    {
        if (Entry::STATUS_READY != $event->entry->status && !$event->entry->indexed_at_marketplace) {
            return;
        }

        $cores = SolrService::getAllCores();
        SyncEntryToSolrJob::dispatch($event->entry->id, $cores);
        foreach ($event->entry->playlists as $playlist) {
            SyncPlaylistToSolrJob::dispatch($playlist);
        }
    }
}
