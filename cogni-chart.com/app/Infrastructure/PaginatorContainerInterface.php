<?php

namespace App\Infrastructure;

interface PaginatorContainerInterface
{

    public function __construct($paginator);

    public function get();

}
