<?php

namespace App\Domain\AbstractChartTerm\Strategy\GBScotlandSinglesChart;
use App\Domain\AbstractChartTerm\Strategy\AbstractRequestSender;
use App\Infrastructure\Remote\RemoteInterface;
use App\Infrastructure\Remote\Scheme;

class RequestSender extends AbstractRequestSender
{

    protected function execute(RemoteInterface $remote, \DateTimeImmutable $endDateTime = null)
    {
        $scheme = new Scheme($this->scheme);
        $uri = $this->uri;
        if (!empty($endDateTime)) {
            $uri .= '/' . $endDateTime->format('Ymd') . '/41';
        }
        return $remote->sendGet(
            $scheme,
            $this->host,
            $uri
        );
    }

}
