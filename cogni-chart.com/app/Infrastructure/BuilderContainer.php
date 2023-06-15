<?php

namespace App\Infrastructure;
use App\Infrastructure\BuilderContainerInterface;

class BuilderContainer implements BuilderContainerInterface
{

    private $builders;

    public function __construct()
    {
        $this->builders = [];
    }

    public function set(string $key, $builder)
    {
        $key = trim($key);
        if (empty($key)) {
            return;
        }
        $this->builders[$key] = $builder;
    }

    public function get(string $key)
    {
        $key = trim($key);
        if (empty($key)) {
            return null;
        }
        if (empty($this->builders[$key])) {
            return null;
        }
        return $this->builders[$key];
    }

}
