<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ChartReleased
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $entityIdValue;
    private $countryIdValue;
    private $chartNameValue;

    /**
     * Create a new event instance.
     *
     *  @param  string  $entityIdValue      The id of ChartEntity.
     *  @param  string  $countryIdValue     The id of Country before changed.
     *  @param  string  $chartNameValue     The name of Chart before changed.
     *  @return void
     */
    public function __construct(string $entityIdValue, string $countryIdValue, string $chartNameValue)
    {
        $this->entityIdValue = $entityIdValue;
        $this->countryIdValue = $countryIdValue;
        $this->chartNameValue = $chartNameValue;
    }

    public function entityIdValue()
    {
        return $this->entityIdValue;
    }

    public function countryIdValue()
    {
        return $this->countryIdValue;
    }

    public function chartNameValue()
    {
        return $this->chartNameValue;
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
