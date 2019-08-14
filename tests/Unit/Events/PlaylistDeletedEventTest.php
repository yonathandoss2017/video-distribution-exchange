<?php

namespace Tests\Unit\Events;

use Tests\TestCase;
use App\Models\Playlist;
use App\Events\PlaylistDeletedEvent;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlaylistDeletedEventTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function call_playlist_deleted_event_when_playlist_deleted()
    {
        Event::fake();

        $playlist = factory(Playlist::class)->create();
        $playlist->delete();

        Event::assertDispatched(PlaylistDeletedEvent::class, function ($e) use ($playlist) {
            return $e->playlist->id === $playlist->id;
        });
    }
}
