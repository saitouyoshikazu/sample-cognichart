<?php

namespace App\Console\Commands\AbstractChartTerm;

use Illuminate\Console\Command;
use App\Application\DXO\AbstractChartTermDXO;
use App\Application\AbstractChartTerm\AbstractChartTermApplicationInterface;

class Create extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AbstractChartTerm:create {countryIdValue} {chartNameValue} {targetDateValue?} {intervalValue?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command try to create AbstractChartTerm.';

    private $abstractChartTermApplication;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(AbstractChartTermApplicationInterface $abstractChartTermApplication)
    {
        parent::__construct();
        $this->abstractChartTermApplication = $abstractChartTermApplication;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $countryIdValue  = $this->argument('countryIdValue' );
        $chartNameValue  = $this->argument('chartNameValue' );
        $targetDateValue = $this->argument('targetDateValue');
        $intervalValue   = $this->argument('intervalValue'  );
        $abstractChartTermDXO = new AbstractChartTermDXO();
        $abstractChartTermDXO->create($countryIdValue, $chartNameValue, $targetDateValue, $intervalValue);
        $this->abstractChartTermApplication->create($abstractChartTermDXO);
    }

}
