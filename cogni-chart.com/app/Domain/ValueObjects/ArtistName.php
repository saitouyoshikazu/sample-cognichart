<?php

namespace App\Domain\ValueObjects;

class ArtistName
{

    private $artistName;

    public function __construct(string $artistName)
    {
        $artistName = trim($artistName);
        if (empty($artistName)) {
            throw new ValueObjectException("Can't set empty value in ArtistName.");
        }
        $this->artistName = $artistName;
    }

    public function value()
    {
        return $this->artistName;
    }

}
