<?php

namespace App\Domain;
use App\Infrastructure\PaginatorContainerInterface;

interface DomainPaginatorInterface
{

    public function __construct(array $entities, $paginator);

    public function getEntities();

    public function getPaginator();

}
