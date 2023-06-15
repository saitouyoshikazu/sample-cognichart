<?php

namespace App\Domain\ValueObjects;

class ITunesBaseUrl
{

    private $iTunesBaseUrl;

    public function __construct(string $iTunesBaseUrl)
    {
        $iTunesBaseUrl = trim($iTunesBaseUrl);
        if (empty($iTunesBaseUrl)) {
            throw new ValueObjectException("Can't set empty value in ITunesBaseUrl.");
        }
        $this->iTunesBaseUrl = $iTunesBaseUrl;
    }

    public function value()
    {
        return $this->iTunesBaseUrl;
    }

}
