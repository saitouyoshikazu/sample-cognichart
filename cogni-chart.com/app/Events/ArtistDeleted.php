<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ArtistDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $entityIdValue;
    private $oldITunesArtistIdValue;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string $entityIdValue, string $oldITunesArtistIdValue)
    {
        $this->entityIdValue = $entityIdValue;
        $this->oldITunesArtistIdValue = $oldITunesArtistIdValue;
    }

    public function entityIdValue()
    {
        return trim($this->entityIdValue);
    }

    public function oldITunesArtistIdValue()
    {
        return trim($this->oldITunesArtistIdValue);
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
