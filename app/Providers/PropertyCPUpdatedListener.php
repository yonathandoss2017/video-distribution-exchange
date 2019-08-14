<?php

namespace App\Providers;

use App\Services\Solr\SolrService;
use App\Jobs\Solr\SyncEntryToSolrJob;
use App\Jobs\Solr\SyncPlaylistToSolrJob;

class PropertyCPUpdatedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param PropertyCPUpdatedEvent $event
     */
    public function handle(PropertyCPUpdatedEvent $event)
    {
        $cores = SolrService::getAllCores();
        foreach ($event->property->entries as $entry) {
            SyncEntryToSolrJob::dispatch($entry->id, $cores);
        }
        foreach ($event->property->playlists as $playlist) {
            SyncPlaylistToSolrJob::dispatch($playlist);
        }
    }
}
