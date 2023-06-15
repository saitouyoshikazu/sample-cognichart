<?php

namespace App\Domain\ValueObjects;

class MusicTitle
{

    private $musicTitle;

    public function __construct(string $musicTitle)
    {
        $musicTitle = trim($musicTitle);
        if (empty($musicTitle)) {
            throw new ValueObjectException("Can't set empty value in MusicTitle.");
        }
        $this->musicTitle = $musicTitle;
    }

    public function value()
    {
        return $this->musicTitle;
    }

}
