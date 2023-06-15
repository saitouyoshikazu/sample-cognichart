<?php

namespace App\Domain\Country;

class CountryId
{

    private $id;

    public function __construct(string $id)
    {
        $id = strtoupper($id);
        $this->id = $id;
    }

    public function value()
    {
        return $this->id;
    }

}
