<?php

namespace App\Domain\ValueObjects;

class ChartTermDate
{

    private $date;

    public function __construct(string $chartTermDateValue)
    {
        $this->setChartTermDate($chartTermDateValue);
    }

    public function setChartTermDate(string $chartTermDateValue)
    {
        $chartTermDateValue = trim($chartTermDateValue);
        if (empty($chartTermDateValue)) {
            throw new ValueObjectException("Can't set empty value in date of ChartTerm.");
        }
        if (!preg_match("/\A[0-9]{1,4}-[0-9]{1,2}-[0-9]{1,2}\z/", $chartTermDateValue)) {
            throw new ValueObjectException("Can't set invalid date value in date of ChartTerm. : {$chartTermDateValue}");
        }
        $expValues = explode('-', $chartTermDateValue);
        $year = $expValues[0];
        $month = $expValues[1];
        $day = $expValues[2];
        if (!checkdate($month, $day, $year)) {
            throw new ValueObjectException("Can't set invalid date value in date of ChartTerm. : {$chartTermDateValue}");
        }
        $this->date = new \DatetimeImmutable($chartTermDateValue);
    }

    public function value()
    {
        return $this->date->format('Y-m-d');
    }

    public function getDate()
    {
        return $this->date;
    }

    public function equals(ChartTermDate $chartTermDate)
    {
        if ($this->date == $chartTermDate->getDate()) {
            return true;
        }
        return false;
    }

}
