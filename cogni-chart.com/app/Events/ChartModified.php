<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ChartModified
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $entityIdValue;
    private $oldCountryIdValue;
    private $oldChartNameValue;

    /**
     * Create a new event instance.
     *
     *  @param  string  $entityIdValue      The id of Chart.
     *  @param  string  $oldCountryIdValue  The id of Country before changed.
     *  @param  string  $oldChartNameValue  The name of Chart before changed.
     *  @return void
     */
    public function __construct(
        string $entityIdValue,
        string $oldCountryIdValue,
        string $oldChartNameValue
    ) {
        $this->entityIdValue = $entityIdValue;
        $this->oldCountryIdValue = $oldCountryIdValue;
        $this->oldChartNameValue = $oldChartNameValue;
    }

    public function entityIdValue()
    {
        return $this->entityIdValue;
    }

    public function oldCountryIdValue()
    {
        return $this->oldCountryIdValue;
    }

    public function oldChartNameValue()
    {
        return $this->oldChartNameValue;
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
