<?php

namespace App\Listeners;

use App\Models\Playlist;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\PlaylistWhitelistUpdatedEvent;
use App\Services\Solr\SolrMarketplaceSyncService;

class PlaylistWhitelistedSyncSolrListener implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param PlaylistWhitelistUpdatedEvent $event
     */
    public function handle(PlaylistWhitelistUpdatedEvent $event)
    {
        if (false == env('SOLR_SYNC_ACTIVE', false)) {
            return;
        }

        $playlist = Playlist::where('id', $event->playlist->id)
            ->withCount([
                'entries',
            ])
            ->withTrashed()
            ->first();

        //sync marketplace
        (new SolrMarketplaceSyncService())->playlist($playlist);
    }
}
