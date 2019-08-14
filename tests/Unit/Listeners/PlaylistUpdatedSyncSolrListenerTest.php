<?php

namespace Tests\Unit\Listeners;

use Tests\TestCase;
use App\Models\Playlist;
use App\Events\PlaylistUpdatedEvent;
use App\Jobs\Solr\SyncPlaylistToSolrJob;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlaylistUpdatedSyncSolrListenerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function call_job_when_listener_called()
    {
        $this->expectsJobs(SyncPlaylistToSolrJob::class);
        $playlist = factory(Playlist::class)->create();
        event(new PlaylistUpdatedEvent($playlist));
    }
}
