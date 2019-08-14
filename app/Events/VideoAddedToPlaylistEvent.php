<?php

namespace App\Events;

use App\Models\Entry;
use App\Models\Playlist;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class VideoAddedToPlaylistEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $playlist;
    public $entry;

    /**
     * Create a new event instance.
     *
     * @param Entry $entry
     * @param $playlist
     */
    public function __construct(Entry $entry, Playlist $playlist)
    {
        $this->playlist = $playlist;
        $this->entry = $entry;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
