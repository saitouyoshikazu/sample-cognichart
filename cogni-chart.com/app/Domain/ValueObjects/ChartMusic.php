<?php

namespace App\Domain\ValueObjects;

class ChartMusic
{

    private $chartMusic;

    public function __construct(string $chartMusic)
    {
        $chartMusic = trim($chartMusic);
        if (empty($chartMusic)) {
            throw new ValueObjectException("Can't set empty value in ChartMusic.");
        }
        $this->chartMusic = $chartMusic;
    }

    public function value()
    {
        return $this->chartMusic;
    }

}
