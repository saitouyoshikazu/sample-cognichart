<?php

namespace App\Domain\AbstractChartTerm\Strategy\SESwedenSinglesChart;
use App\Domain\AbstractChartTerm\Strategy\AbstractDomAnalyzer;

class DomAnalyzer extends AbstractDomAnalyzer
{

    private $seMonths = [
        "januari",
        "februari",
        "mars",
        "april",
        "maj",
        "juni",
        "juli",
        "augusti",
        "september",
        "oktober",
        "november",
        "december"
    ];

    private $enMonths = [
        "january",
        "february",
        "march",
        "april",
        "may",
        "june",
        "july",
        "august",
        "september",
        "october",
        "november",
        "december"
    ];

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
        if (empty($this->chartObject)) {
            $this->getParentElement();
        }
        $baseDateStr = $this->chartObject->text;
        $dateStr = trim(preg_replace("/^Vecka.*,/", "", $baseDateStr));
        $dateStr = str_replace($this->seMonths, $this->enMonths, $dateStr);
        $endDateTimeString = date("Y-m-d", strtotime($dateStr));
        $endDateTime = new \DateTimeImmutable($endDateTimeString);
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
        if (empty($this->chartObject)) {
            $this->getParentElement();
        }
        $baseStr = $this->chartObject->prevper->text;
        $dateStr = trim(preg_replace("/^Vecka.*,/", "", $baseStr));
        $dateStr = str_replace($this->seMonths, $this->enMonths, $dateStr);
        $endDateTimeString = date("Y-m-d", strtotime($dateStr));
        $nextEndDateTime = new \DateTimeImmutable($endDateTimeString);
        return $nextEndDateTime;
    }

    public function valid()
    {
        if (empty($this->chartObject)) {
            $this->getParentElement();
        }
        if (empty($this->chartObject->chart[$this->position])) {
            return false;
        }
        return true;
    }

    protected function getParentElement()
    {
        $nodeList = $this->xpath->query("//body/script");
        $parentElement = $nodeList->item(0);

        if (empty($this->chartObject)) {
            $scriptString = $parentElement->textContent;
            $chartString = str_replace("window.__PRELOADED_STATE__ = ", "", $scriptString);
            $baseArray = json_decode($chartString);
            $this->chartObject = $baseArray->chart->currentChart;
        }
        return $parentElement;
    }

    protected function getRanking(\DOMElement $parentElement)
    {
        if (empty($this->chartObject)) {
            $this->getParentElement();
        }
        if (empty($this->chartObject->chart[$this->position])) {
            return null;
        }
        return $this->chartObject->chart[$this->position]->rowid;
    }

    protected function getChartMusic(\DOMElement $parentElement)
    {
        if (empty($this->chartObject)) {
            $this->getParentElement();
        }
        if (empty($this->chartObject->chart[$this->position])) {
            return null;
        }
        $decoded = html_entity_decode($this->chartObject->chart[$this->position]->tit);
        $chartMusicString = str_replace("&#39;", "'", $decoded);
        return $chartMusicString;
    }

    protected function getChartArtist(\DOMElement $parentElement)
    {
        if (empty($this->chartObject)) {
            $this->getParentElement();
        }
        if (empty($this->chartObject->chart[$this->position])) {
            return null;
        }
        $decoded = html_entity_decode($this->chartObject->chart[$this->position]->arso);
        $chartArtistString = str_replace("&#39;", "'", $decoded);
        return $chartArtistString;
    }

}
