<?php

namespace App\Events;

use App\Models\Entry;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;

class VideoPublished
{
    use InteractsWithSockets, SerializesModels;

    public $entry;

    public function __construct(Entry $entry)
    {
        $this->entry = $entry;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
