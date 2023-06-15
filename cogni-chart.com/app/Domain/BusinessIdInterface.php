<?php

namespace App\Domain;

interface BusinessIdInterface
{

    /**
     * Get value of id depending on business.
     * @return  string  Value of id depending on business.
     */
    public function value();

}
