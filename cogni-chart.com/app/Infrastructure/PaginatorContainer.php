<?php

namespace App\Infrastructure;
use App\Infrastructure\PaginatorContainerInterface;

class PaginatorContainer implements PaginatorContainerInterface
{

    private $paginator;

    public function __construct($paginator)
    {
        $this->paginator = $paginator;
    }

    public function get()
    {
        return $this->paginator;
    }

}
