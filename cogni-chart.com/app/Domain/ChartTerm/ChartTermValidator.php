<?php

namespace App\Domain\ChartTerm;
use App\Domain\ValidationHandlerInterface;

class ChartTermValidator
{

    private $chartTermEntity;
    private $handler;

    public function __construct(ChartTermEntity $chartTermEntity, ValidationHandlerInterface $handler)
    {
        $this->chartTermEntity = $chartTermEntity;
        $this->handler = $handler;
    }

    public function validate()
    {
        $startDate = $this->chartTermEntity->startDate();
        $endDate = $this->chartTermEntity->endDate();

        if (strtotime($startDate->value()) >= strtotime($endDate->value())) {
            $this->handler->addError("Start date of ChartTerm must be before than end date.");
        }
        $this->handler->endHandle();
    }

}
