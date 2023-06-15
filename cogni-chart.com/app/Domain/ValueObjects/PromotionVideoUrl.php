<?php

namespace App\Domain\ValueObjects;

class PromotionVideoUrl
{

    private $url;

    public function __construct(string $url)
    {
        $url = trim($url);
        if (empty($url)) {
            throw new ValueObjectException("Can't set empty value in PromotionVideoUrl.");
        }
        $this->url = $url;
    }

    public function value()
    {
        return $this->url;
    }

}
