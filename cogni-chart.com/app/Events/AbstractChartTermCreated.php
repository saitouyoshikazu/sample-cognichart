<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Domain\ChartTerm\ChartTermBusinessId;

class AbstractChartTermCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $chartIdValue;
    private $endDateValue;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string $chartIdValue, string $endDateValue)
    {
        $this->chartIdValue = $chartIdValue;
        $this->endDateValue = $endDateValue;
    }

    public function chartIdValue()
    {
        return $this->chartIdValue;
    }

    public function endDateValue()
    {
        return $this->endDateValue;
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
