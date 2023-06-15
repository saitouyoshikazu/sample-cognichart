<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MusicRegistered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $entityIdValue;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string $entityIdValue)
    {
        $this->entityIdValue = $entityIdValue;
    }

    public function entityIdValue()
    {
        return trim($this->entityIdValue);
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
