<?php

namespace App\Domain\AbstractChartTerm\Strategy;
use App\Domain\AbstractChartTerm\AbstractChartTermException;
use App\Infrastructure\Remote\RemoteInterface;

abstract class AbstractRequestSender
{

    protected $scheme;
    protected $host;
    protected $uri;

    public function setScheme(string $scheme)
    {
        $scheme = trim($scheme);
        if (empty($scheme)) {
            throw new AbstractChartTermException("Can't set empty value in scheme.");
        }
        $this->scheme = $scheme;
        return $this;
    }

    public function setHost(string $host)
    {
        $host = trim($host);
        if (empty($host)) {
            throw new AbstractChartTermException("Can't set empty value in host.");
        }
        $this->host = $host;
        return $this;
    }

    public function setUri(string $uri)
    {
        $uri = trim($uri);
        if (empty($uri)) {
            $uri = "";
        }
        $this->uri = $uri;
        return $this;
    }

    public function send(RemoteInterface $remote, \DateTimeImmutable $endDateTime = null)
    {
        $remoteResponse = $this->execute($remote, $endDateTime);
        if ($remoteResponse->getStatus() !== 200) {
            return null;
        }
        return $remoteResponse->getBody();
    }

    abstract protected function execute(RemoteInterface $remote, \DateTimeImmutable $endDateTime = null);

}
