<?php

namespace App\Providers;

use App\Models\PropertyCP;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class PropertyCPUpdatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $property;

    /**
     * Create a new event instance.
     */
    public function __construct(PropertyCP $property)
    {
        $this->property = $property;
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
