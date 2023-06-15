<?php

namespace App\Domain\AbstractChartTerm\Strategy\GBUKSinglesChart;
use App\Domain\AbstractChartTerm\Strategy\AbstractDomAnalyzer;

class DomAnalyzer extends AbstractDomAnalyzer
{

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
        $year = $this->xpath->query("//select[contains(@class, 'year-search')]/option[@selected]")->item(0)->getAttribute("value");
        $month = $this->xpath->query("//select[contains(@class, 'month-search')]/option[@selected]")->item(0)->getAttribute("value");
        $day = $this->xpath->query("//select[contains(@class, 'day-search')]/option[@selected]")->item(0)->getAttribute("value");
        $endDateTime = new \DateTimeImmutable("{$year}-{$month}-{$day}");
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
        $endDateTime = $this->getEndDateTime();
        if (empty($endDateTime)) {
            return null;
        }
        $nextEndDateTime = $endDateTime->sub(new \DateInterval("P7D"));
        return $nextEndDateTime;
    }

    public function valid()
    {
        $parentElement = $this->getParentElement();
        if (empty($parentElement)) {
            return false;
        }
        return true;
    }

    protected function getParentElement()
    {
        $nodeList = $this->xpath->query("//section[contains(@class, 'chart')]/table[contains(@class, 'chart-positions')]/tr[.//td/span[contains(@class, 'position')]]");
        $parentElement = $nodeList->item($this->position);
        return $parentElement;
    }

    protected function getRanking(\DOMElement $parentElement)
    {
        $nodeList = $this->xpath->query(".//td/span[contains(@class, 'position')]", $parentElement);
        return trim($nodeList->item(0)->textContent);
    }

    protected function getChartMusic(\DOMElement $parentElement)
    {
        $nodeList = $this->xpath->query(".//td/div[contains(@class, 'track')]/div[contains(@class, 'title-artist')]/div[contains(@class, 'title')]/a", $parentElement);
        $decoded = html_entity_decode($nodeList->item(0)->textContent);
        $chartMusicString = str_replace("&#39;", "'", $decoded);
        return $chartMusicString;
    }

    protected function getChartArtist(\DOMElement $parentElement)
    {
        $nodeList = $this->xpath->query(".//td/div[contains(@class, 'track')]/div[contains(@class, 'title-artist')]/div[contains(@class, 'artist')]/a", $parentElement);
        $decoded = html_entity_decode($nodeList->item(0)->textContent);
        $chartArtistString = str_replace("&#39;", "'", $decoded);
        return $chartArtistString;
    }

}
