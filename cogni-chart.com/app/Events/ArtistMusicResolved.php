<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ArtistMusicResolved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $chartRankingItemIdValue;
    private $apiNameValue;
    private $resolvedArtistValue;
    private $resolvedMusicValue;
    private $resolvedArtistIdValue;
    private $resolvedITunesBaseUrlValue;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        string $chartRankingItemIdValue,
        string $apiNameValue,
        string $resolvedArtistValue,
        string $resolvedMusicValue,
        string $resolvedArtistIdValue = null,
        string $resolvedITunesBaseUrlValue = null
    ) {
        $this->chartRankingItemIdValue      =   $chartRankingItemIdValue;
        $this->apiNameValue                 =   $apiNameValue;
        $this->resolvedArtistValue          =   $resolvedArtistValue;
        $this->resolvedMusicValue           =   $resolvedMusicValue;
        $this->resolvedArtistIdValue        =   $resolvedArtistIdValue;
        $this->resolvedITunesBaseUrlValue   =   $resolvedITunesBaseUrlValue;
    }

    public function chartRankingItemIdValue()
    {
        return $this->chartRankingItemIdValue;
    }

    public function apiNameValue()
    {
        return $this->apiNameValue;
    }

    public function resolvedArtistValue()
    {
        return $this->resolvedArtistValue;
    }

    public function resolvedMusicValue()
    {
        return $this->resolvedMusicValue;
    }

    public function resolvedArtistIdValue()
    {
        return $this->resolvedArtistIdValue;
    }

    public function resolvedITunesBaseUrlValue()
    {
        return $this->resolvedITunesBaseUrlValue;
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
