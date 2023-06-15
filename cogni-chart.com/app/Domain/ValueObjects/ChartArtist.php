<?php

namespace App\Domain\ValueObjects;

class ChartArtist
{

    private $chartArtist;

    public function __construct(string $chartArtist)
    {
        $chartArtist = trim($chartArtist);
        $this->chartArtist = $chartArtist;
    }

    public function value()
    {
        if (empty($this->chartArtist)) {
            return '';
        }
        return $this->chartArtist;
    }

}
