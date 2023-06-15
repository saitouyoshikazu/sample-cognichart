<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ChartRankingItemModified
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $entityIdValue;
    private $chartArtistValue;
    private $chartMusicValue;

    /**
     * Create a new event instance.
     * @param   string  $entityIdValue      The id of ChartRankingItem.
     * @param   string  $chartArtistValue   Value of ChartArtist.
     * @param   string  $chartMusicValue    Value of ChartMusic.
     * @return void
     */
    public function __construct(string  $entityIdValue, string $chartArtistValue, string $chartMusicValue)
    {
        $this->entityIdValue = $entityIdValue;
        $this->chartArtistValue = $chartArtistValue;
        $this->chartMusicValue = $chartMusicValue;
    }

    public function entityIdValue()
    {
        return trim($this->entityIdValue);
    }

    public function chartArtistValue()
    {
        return trim($this->chartArtistValue);
    }

    public function chartMusicValue()
    {
        return trim($this->chartMusicValue);
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
