<?php

namespace App\Listeners\Solr;

use App\Jobs\Solr\SyncPlaylistToSolrJob;

class SyncPlaylistToSolrListener
{
    /**
     * Handle the event.
     *
     * @param object $event
     */
    public function handle($event)
    {
        SyncPlaylistToSolrJob::dispatch($event->playlist);
    }
}
