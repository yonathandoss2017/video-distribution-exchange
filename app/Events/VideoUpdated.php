<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class VideoUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $entry_id;
    public $publishedAtChanged;

    /**
     * Create a new event instance.
     */
    public function __construct($entry_id, $publishedAtChanged)
    {
        $this->entry_id = $entry_id;
        $this->publishedAtChanged = $publishedAtChanged;
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
