<?php

namespace App\Infrastructure\Sns;

interface TwitterInterface
{

    public function post(string $message);

}
