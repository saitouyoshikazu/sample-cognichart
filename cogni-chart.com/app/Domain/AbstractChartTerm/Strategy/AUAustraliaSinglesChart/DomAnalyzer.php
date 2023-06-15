<?php

namespace App\Domain\AbstractChartTerm\Strategy\AUAustraliaSinglesChart;
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
        $endDateTime = new \DateTimeImmutable(date("Y-m-d", strtotime("next Monday")));
        return $endDateTime;
    }

    public function getNextStartDateTime()
    {
        return null;
    }

    public function getNextEndDateTime()
    {
        return null;
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
        $nodeList = $this->xpath->query("//table[@id='tbChartItems']/tbody/tr");
        $parentElement = $nodeList->item($this->position);
        return $parentElement;
    }

    protected function getRanking(\DOMElement $parentElement)
    {
        $nodeList = $this->xpath->query(".//td[contains(@class, 'ranking')]/span", $parentElement);
        return trim($nodeList->item(0)->textContent);
    }

    protected function getChartMusic(\DOMElement $parentElement)
    {
        $nodeList = $this->xpath->query(".//td[contains(@class, 'title-artist')]/div[contains(@class, 'item-title')]", $parentElement);
        $decoded = html_entity_decode($nodeList->item(0)->textContent);
        $chartMusicString = str_replace("&#39;", "'", $decoded);
        return $chartMusicString;
    }

    protected function getChartArtist(\DOMElement $parentElement)
    {
        $nodeList = $this->xpath->query(".//td[contains(@class, 'title-artist')]/div[contains(@class, 'artist-name')]", $parentElement);
        $decoded = html_entity_decode($nodeList->item(0)->textContent);
        $chartArtistString = str_replace("&#39;", "'", $decoded);
        return $chartArtistString;
    }

}
