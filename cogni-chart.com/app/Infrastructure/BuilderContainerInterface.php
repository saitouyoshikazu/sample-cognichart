<?php

namespace App\Infrastructure;

interface BuilderContainerInterface
{

    public function __construct();

    public function set(string $key, $builder);

    public function get(string $key);

}
