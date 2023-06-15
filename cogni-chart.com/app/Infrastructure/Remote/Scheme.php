<?php

namespace App\Infrastructure\Remote;

class Scheme
{

    const http = 'http';

    const https = 'https';

    private $scheme;

    public function __construct(string $scheme)
    {
        if (!defined("self::{$scheme}")) {
            throw new RemoteException("Invalid value of scheme. : " . self::class . "::{$scheme} doesn't exist.");
        }
        $this->scheme = $scheme;
    }

    public function value()
    {
        return $this->scheme;
    }

}
