<?php

namespace App\Domain;

interface ValidationHandlerInterface
{

    public function addError(string $error);

    public function endHandle();

}
