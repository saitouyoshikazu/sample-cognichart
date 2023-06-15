<?php

namespace App\Infrastructure\Remote\FileGetContents;

class StreamContextOption
{

    private $wrapper;

    private $option;

    private $value;

    public function __construct(string $wrapper, string $option, string $value)
    {
        $this->wrapper = $wrapper;
        $this->option = $option;
        $this->value = $value;
    }

    public function getWrapper()
    {
        return $this->wrapper;
    }

    public function getOption()
    {
        return $this->option;
    }

    public function getValue()
    {
        return $this->value;
    }

}
