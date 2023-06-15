<?php

namespace App\Domain;
use App\Domain\DomainPaginatorInterface;

class DomainPaginator implements DomainPaginatorInterface
{

    private $entities;
    private $paginator;

    public function __construct(array $entities, $paginator)
    {
        $this->entities = $entities;
        $this->paginator = $paginator;
    }

    public function getEntities()
    {
        return $this->entities;
    }

    public function getPaginator()
    {
        return $this->paginator;
    }

}
