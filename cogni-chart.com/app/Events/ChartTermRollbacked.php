<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ChartTermRollbacked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $entityIdValue;
    private $chartIdValue;
    private $endDateValue;
    private $countryIdValue;
    private $chartNameValue;

    /**
     * Create a new event instance.
     *
     *  @param  string  $entityIdValue      The id of ChartTermEntity.
     *  @param  string  $chartIdValue       The id of Chart.
     *  @param  string  $endDateValue       End date of ChartTerm.
     *  @param  string  $countryIdValue     The id of Country.
     *  @param  string  $chartNameValue     Name of Chart.
     *  @return void
     */
    public function __construct(
        string $entityIdValue,
        string $chartIdValue,
        string $endDateValue,
        string $countryIdValue = null,
        string $chartNameValue = null
    ) {
        $this->entityIdValue = $entityIdValue;
        $this->chartIdValue = $chartIdValue;
        $this->endDateValue = $endDateValue;
        $this->countryIdValue = $countryIdValue;
        $this->chartNameValue = $chartNameValue;
    }

    public function entityIdValue()
    {
        return trim($this->entityIdValue);
    }

    public function chartIdValue()
    {
        return trim($this->chartIdValue);
    }

    public function endDateValue()
    {
        return trim($this->endDateValue);
    }

    public function countryIdValue()
    {
        return trim($this->countryIdValue);
    }

    public function chartNameValue()
    {
        return trim($this->chartNameValue);
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
