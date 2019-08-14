<?php

namespace App\Events;

use App\Models\Entry;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class EntryUpdatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Entry
     */
    public $entry;

    /**
     * Create a new event instance.
     *
     * @internal param Entry $entry
     *
     * @param Entry $entry
     *
     * @internal param $entry_id
     */
    public function __construct(Entry $entry)
    {
        $this->entry = $entry;
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
