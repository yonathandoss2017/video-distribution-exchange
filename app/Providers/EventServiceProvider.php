<?php

namespace App\Providers;

use App\Events\VideoUpdated;
use App\Events\VideoPublished;
use App\Events\EntryUpdatedEvent;
use App\Events\PlaylistDeletedEvent;
use App\Events\PlaylistUpdatedEvent;
use App\Events\PropertyCreatedEvent;
use App\Events\PlaylistUpdatingEvent;
use App\Events\VideoAddedToPlaylistEvent;
use App\Events\PlaylistWhitelistUpdatedEvent;
use App\Listeners\Solr\SyncEntryToSolrListener;
use App\Listeners\Solr\SyncPlaylistToSolrListener;
use App\Listeners\SyncPlaylistEntriesToSolrListener;
use App\Listeners\PlaylistWhitelistedSyncSolrListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        EntryUpdatedEvent::class => [
            SyncEntryToSolrListener::class,
        ],
        PlaylistUpdatingEvent::class => [
            SyncPlaylistEntriesToSolrListener::class,
        ],
        PlaylistUpdatedEvent::class => [
            SyncPlaylistToSolrListener::class,
        ],
        PlaylistDeletedEvent::class => [
        ],
        PlaylistWhitelistUpdatedEvent::class => [
            PlaylistWhitelistedSyncSolrListener::class,
        ],
        PropertyCreatedEvent::class => [
        ],
        VideoAddedToPlaylistEvent::class => [
            SyncEntryToSolrListener::class,
            SyncPlaylistToSolrListener::class,
        ],
        VideoPublished::class => [
        ],
        VideoUpdated::class => [
        ],
        PropertyCPUpdatedEvent::class => [
            PropertyCPUpdatedListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot()
    {
        parent::boot();
    }
}
