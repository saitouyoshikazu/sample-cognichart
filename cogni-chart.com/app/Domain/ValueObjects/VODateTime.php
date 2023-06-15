<?php

namespace App\Domain\ValueObjects;

class VODateTime
{

    private $dateTime;

    public function __construct(string $dateTimeString)
    {
        $this->dateTime = new \DateTimeImmutable($dateTimeString);
    }

    public function datetime()
    {
        return $this->dateTime->format('Y-m-d H:i:s');
    }

}
