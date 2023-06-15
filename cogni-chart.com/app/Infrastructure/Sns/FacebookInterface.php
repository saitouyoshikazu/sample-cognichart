<?php

namespace App\Infrastructure\Sns;
use App\Infrastructure\Remote\RemoteInterface;

interface FacebookInterface
{

    public function __construct(
        RemoteInterface $remote
    );

    public function post(string $message);

}
