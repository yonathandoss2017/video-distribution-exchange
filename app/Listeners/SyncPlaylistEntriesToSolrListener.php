<?php

namespace App\Listeners;

use App\Events\PlaylistUpdatingEvent;
use App\Jobs\Solr\SyncPlaylistEntriesToSolrMarketplace;

class SyncPlaylistEntriesToSolrListener
{
    /**
     * Handle the event.
     *
     * @param PlaylistUpdatingEvent $event
     */
    public function handle(PlaylistUpdatingEvent $event)
    {
        $playlist = $event->playlist;
        if ($playlist->isDirty(['genre', 'publish', 'publish_start_date', 'publish_end_date', 'publish_status'])) {
            SyncPlaylistEntriesToSolrMarketplace::dispatch($playlist);
        }
    }
}
