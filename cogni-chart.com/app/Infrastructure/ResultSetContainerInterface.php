<?php

namespace App\Infrastructure;

interface ResultSetContainerInterface
{

    public function __construct($resultSet);

    public function get();

}
