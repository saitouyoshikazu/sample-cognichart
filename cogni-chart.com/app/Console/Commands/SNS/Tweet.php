<?php

namespace App\Console\Commands\SNS;

use Illuminate\Console\Command;
use App\Application\Sns\SnsApplicationInterface;
use App\Application\DXO\SnsDXO;

class Tweet extends Command
{

    private $snsApplication;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SNS:tweet {countryIdValue} {chartNameValue} {endDateValue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        SnsApplicationInterface $snsApplication
    ) {
        parent::__construct();
        $this->snsApplication = $snsApplication;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $countryIdValue = $this->argument('countryIdValue');
        $chartNameValue = $this->argument('chartNameValue');
        $endDateValue = $this->argument('endDateValue');
        $snsDXO = new SnsDXO();
        $snsDXO->publishReleasedMessage($countryIdValue, $chartNameValue, $endDateValue);
        $this->snsApplication->publishReleasedMessage($snsDXO);
    }
}
