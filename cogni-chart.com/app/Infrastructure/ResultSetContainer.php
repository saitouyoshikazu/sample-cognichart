<?php

namespace App\Infrastructure;
use App\Infrastructure\ResultSetContainerInterface;

class ResultSetContainer implements ResultSetContainerInterface
{

    private $resultSet;

    public function __construct($resultSet)
    {
        $this->resultSet = $resultSet;
    }

    public function get()
    {
        return $this->resultSet;
    }

}
