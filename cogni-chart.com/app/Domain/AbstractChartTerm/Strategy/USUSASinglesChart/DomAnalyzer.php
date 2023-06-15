<?php

namespace App\Domain\AbstractChartTerm\Strategy\USUSASinglesChart;
use App\Domain\AbstractChartTerm\Strategy\AbstractDomAnalyzer;

class DomAnalyzer extends AbstractDomAnalyzer
{

    private $chartObject = null;

    public function __construct()
    {
        $this->encoding = "UTF-8";
    }

    public function getStartDateTime()
    {
        $endDateTime = $this->getEndDateTime();
        if (empty($endDateTime)) {
            return null;
        }
        $startDateTime = $endDateTime->sub(new \DateInterval("P6D"));
        return $startDateTime;
    }

    public function getEndDateTime()
    {
        $parentElement = $this->getParentElement();
        $endDateTimeString = trim($parentElement->getAttribute("data-chart-date"));
        if (empty($endDateTimeString)) {
            return null;
        }
        $endDateTime = new \DateTimeImmutable("@".strtotime($endDateTimeString));
        return $endDateTime;
    }

    public function getNextStartDateTime()
    {
        $nextEndDateTime = $this->getNextEndDateTime();
        if (empty($nextEndDateTime)) {
            return null;
        }
        $nextStartDateTime = $nextEndDateTime->sub(new \DateInterval("P6D"));
        return $nextStartDateTime;
    }

    public function getNextEndDateTime()
    {
        $parentElement = $this->getParentElement();
        $previousEndDateTimeString = trim($parentElement->getAttribute("data-previous-chart-date"));
        if (empty($previousEndDateTimeString)) {
            return null;
        }
        $nextEndDateTime = new \DateTimeImmutable("@".strtotime($previousEndDateTimeString));
        return $nextEndDateTime;
    }

    public function valid()
    {
        if (empty($this->chartObject)) {
            $this->setChartObject();
        }
        if (empty($this->chartObject[$this->position])) {
            return false;
        }
        return true;
    }

    protected function getParentElement()
    {
        $nodeList = $this->xpath->query("//main[contains(@id, 'main')]/div[contains(@id, 'charts')]");
        $parentElement = $nodeList->item(0);
        return $parentElement;
    }

    protected function getRanking(\DOMElement $parentElement)
    {
        $currentChart = $this->getCurrentChart();
        return trim($currentChart->rank);
    }

    protected function getChartMusic(\DOMElement $parentElement)
    {
        $currentChart = $this->getCurrentChart();
        $chartMusicString = str_replace("&#39;", "'", trim($currentChart->title));
        return $chartMusicString;
    }

    protected function getChartArtist(\DOMElement $parentElement)
    {
        $currentChart = $this->getCurrentChart();
        $chartArtistString = str_replace("&#39;", "'", trim($currentChart->artist_name));
        return $chartArtistString;
    }

    private function getCurrentChart()
    {
        if (empty($this->chartObject)) {
            $this->setChartObject();
        }
        return $this->chartObject[$this->position];
    }

    private function setChartObject()
    {
        $parentElement = $this->getParentElement();
        $chartString = $parentElement->getAttribute("data-charts");
        $this->chartObject = json_decode($chartString);
    }

}
