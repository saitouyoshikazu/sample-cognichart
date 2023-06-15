<?php

namespace App\Domain;

class ThrowException implements ValidationHandlerInterface
{

    private $exceptionName;
    private $errors;

    public function __construct(string $exceptionName)
    {
        $this->setExceptionName($exceptionName);
        $this->errors = [];
    }

    private function setExceptionName(string $exceptionName)
    {
        $exceptionName = trim($exceptionName);
        if (empty($exceptionName)) {
            throw new DomainLayerException("Can't set empty value in exceptionName.");
        }
        if (!class_exists($exceptionName)) {
            throw new DomainLayerException("Exception class dosen't exist. : {$exceptionName}");
        }
        if (!is_subclass_of($exceptionName, \Exception::class)) {
            throw new DomainLayerException("{$exceptionName} isn't subclass of Exception.");
        }
        $this->exceptionName = $exceptionName;
    }

    public function addError(string $error)
    {
        $this->errors[] = $error;
        return $this;
    }

    public function endHandle()
    {
        if (empty($this->errors)) {
            return;
        }
        $message = trim(implode("\n", $this->errors));
        if (empty($message)) {
            return;
        }
        $refException = new \ReflectionClass($this->exceptionName);
        $exception = $refException->newInstance($message);
        throw $exception;
    }

}
