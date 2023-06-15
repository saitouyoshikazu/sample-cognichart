<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ArtistModified
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $entityIdValue;
    private $oldITunesArtistIdValue;
    private $iTunesArtistIdValue;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string $entityIdValue, string $oldITunesArtistIdValue, string $iTunesArtistIdValue)
    {
        $this->entityIdValue = $entityIdValue;
        $this->oldITunesArtistIdValue = $oldITunesArtistIdValue;
        $this->iTunesArtistIdValue = $iTunesArtistIdValue;
    }

    public function entityIdValue()
    {
        return trim($this->entityIdValue);
    }

    public function oldITunesArtistIdValue()
    {
        return trim($this->oldITunesArtistIdValue);
    }

    public function iTunesArtistIdValue()
    {
        return trim($this->iTunesArtistIdValue);
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
