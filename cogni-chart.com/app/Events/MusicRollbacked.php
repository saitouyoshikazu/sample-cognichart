<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MusicRollbacked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $entityIdValue;
    private $oldITunesArtistIdValue;
    private $oldMusicTitleValue;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string $entityIdValue, string $oldITunesArtistIdValue, string $oldMusicTitleValue)
    {
        $this->entityIdValue = $entityIdValue;
        $this->oldITunesArtistIdValue = $oldITunesArtistIdValue;
        $this->oldMusicTitleValue = $oldMusicTitleValue;
    }

    public function entityIdValue()
    {
        return trim($this->entityIdValue);
    }

    public function oldITunesArtistIdValue()
    {
        return trim($this->oldITunesArtistIdValue);
    }

    public function oldMusicTitleValue()
    {
        return trim($this->oldMusicTitleValue);
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
