<?php

namespace Tests\Unit\Events;

use Tests\TestCase;
use App\Models\Playlist;
use App\Events\PlaylistUpdatedEvent;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlaylistUpdatedEventTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function event_fired_when_playlist_created()
    {
        Event::fake();
        factory(Playlist::class)->create();
        Event::assertDispatched(PlaylistUpdatedEvent::class);
    }

    /**
     * @test
     */
    public function event_fired_when_playlist_updated()
    {
        Event::fake();
        $playlist = factory(Playlist::class)->create();
        $playlist->update(['name' => 123]);
        Event::assertDispatched(PlaylistUpdatedEvent::class);
    }

    /**
     * @test
     */
    public function event_fired_when_playlist_saved()
    {
        Event::fake();
        $playlist = factory(Playlist::class)->create();
        $playlist->name = 123;
        $playlist->save();
        Event::assertDispatched(PlaylistUpdatedEvent::class);
    }

    /**
     * @test
     */
    public function event_fired_when_playlist_deleted()
    {
        Event::fake();
        $playlist = factory(Playlist::class)->create();
        $playlist->delete();
        Event::assertDispatched(PlaylistUpdatedEvent::class);
    }
}
