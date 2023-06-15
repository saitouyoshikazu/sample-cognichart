<?php

namespace App\Domain\AbstractChartTerm\Strategy;
use App\Domain\AbstractChartTerm\AbstractChartTermException;

abstract class AbstractDomAnalyzer implements \Iterator
{

    private $doc;
    protected $encoding = "UTF-8";
    protected $xpath;
    protected $position = 0;

    public function setDocument(string $document)
    {
        $document = trim($document);
        if (empty($document)) {
            throw new AbstractChartTermException("Chart of Publisher was empty.");
        }

        $this->doc = new \DOMDocument("1.0", $this->encoding);
        $this->doc->preserveWhiteSpace = false;
        $this->doc->formatOutput = true;
        libxml_use_internal_errors(true);
        @$this->doc->loadHTML(mb_convert_encoding($document, "HTML-ENTITIES", $this->encoding));
        libxml_clear_errors();
        $this->xpath = new \DOMXPath($this->doc);
        $this->position = 0;
    }

    /**
     * Determine whether to continue the process.
     * If Chart doesn't archive, override this method to already return false.
     * @param   \DateTimeImmutable  $firstDateTime  Datetime started process.
     * @param   \DateInterval       $interval       DateInterval going to continue.
     * @return  true    When continue process.
     *          false   When end process.
     */
    public function isContinue(\DateTimeImmutable $firstDateTime = null, \DateInterval $interval = null)
    {
        if (empty($firstDateTime)) {
            return false;
        }
        if (empty($interval)) {
            return false;
        }
        $processEnd = $firstDateTime->sub($interval);
        $nextEndDateTime = $this->getNextEndDateTime();
        if (empty($nextEndDateTime)) {
            return false;
        }
        return $nextEndDateTime > $processEnd;
    }

    abstract public function getStartDateTime();

    abstract public function getEndDateTime();

    abstract public function getNextStartDateTime();

    abstract public function getNextEndDateTime();

    abstract protected function getParentElement();

    abstract protected function getRanking(\DOMElement $parentElement);

    abstract protected function getChartArtist(\DOMElement $parentElement);

    abstract protected function getChartMusic(\DOMElement $parentElement);

    abstract public function valid();

    public function rewind()
    {
        $this->xpath = new \DOMXPath($this->doc);
        $this->position = 0;
    }

    public function current()
    {
        $parentElement = $this->getParentElement();
        $ranking = trim($this->getRanking($parentElement));
        $chartArtist = trim($this->getChartArtist($parentElement));
        $chartMusic = trim($this->getChartMusic($parentElement));
        if (empty($ranking) || empty($chartMusic)) {
            throw new AbstractChartTermException("Can't set empty string in ranking, chart_music. : {$ranking}, {$chartMusic}");
        }
        $row = [];
        $row['ranking'] = $ranking;
        $row['chart_artist'] = $chartArtist;
        $row['chart_music'] = $chartMusic;
        return $row;
    }

    public function key()
    {
        $parentElement = $this->getParentElement();
        return $this->getRanking($parentElement);
    }

    public function next()
    {
        ++$this->position;
    }

}
