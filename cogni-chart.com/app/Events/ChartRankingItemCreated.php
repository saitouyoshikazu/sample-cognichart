<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ChartRankingItemCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $chartRankingItemIdValue;
    private $chartArtistValue;
    private $chartMusicValue;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string $chartRankingItemIdValue, string $chartArtistValue, string $chartMusicValue)
    {
        $this->chartRankingItemIdValue = $chartRankingItemIdValue;
        $this->chartArtistValue = $chartArtistValue;
        $this->chartMusicValue = $chartMusicValue;
    }

    public function chartRankingItemIdValue()
    {
        return $this->chartRankingItemIdValue;
    }

    public function chartArtistValue()
    {
        return $this->chartArtistValue;
    }

    public function chartMusicValue()
    {
        return $this->chartMusicValue;
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
