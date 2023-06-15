<?php

namespace App\Domain\ValueObjects;

class ThumbnailUrl
{

    private $url;

    public function __construct(string $url)
    {
        $url = trim($url);
        if (empty($url)) {
            throw new ValueObjectException("Can't set empty value in ThumbnailUrl.");
        }
        $this->url = $url;
    }

    public function value()
    {
        return $this->url;
    }

}
