<?php

namespace App\Events;

use App\Models\Playlist;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class PlaylistWhitelistUpdatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $playlist;

    public function __construct(Playlist $playlist)
    {
        $this->playlist = $playlist;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
