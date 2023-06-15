<?php

namespace App\Listeners;

use App\Events\AbstractChartTermCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Application\AbstractChartTerm\AbstractChartTermApplicationInterface;
use App\Application\DXO\AbstractChartTermDXO;

class AbstractChartTermCreatedListener
{

    private $abstractChartTermApplication;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(AbstractChartTermApplicationInterface $abstractChartTermApplication)
    {
        $this->abstractChartTermApplication = $abstractChartTermApplication;
    }

    /**
     * Handle the event.
     *
     * @param  AbstractChartTermCreated  $event
     * @return void
     */
    public function handle(AbstractChartTermCreated $event)
    {
        $abstractChartTermDXO = new AbstractChartTermDXO();
        $abstractChartTermDXO->import($event->chartIdValue(), $event->endDateValue());
        $this->abstractChartTermApplication->import($abstractChartTermDXO);
    }

}
