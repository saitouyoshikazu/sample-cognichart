<?php

namespace App\Infrastructure\Remote;

class RemoteResponse
{

    private $status;

    private $headers;

    private $body;

    public function __construct(int $status, array $headers, $body)
    {
        $this->status = $status;
        $this->headers = $headers;
        $this->body = $body;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getBody()
    {
        return $this->body;
    }

}
