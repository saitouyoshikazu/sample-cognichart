<?php

namespace App\Domain\Chart;
use App\Domain\Entity;
use App\Domain\EntityId;
use App\Domain\Country\CountryId;
use App\Domain\ValueObjects\ChartName;

class ChartEntity extends Entity
{

    private $countryId;
    private $countryName;
    private $chartName;
    private $scheme;
    private $host;
    private $uri;
    private $originalChartName;
    private $pageTitle;

    public function __construct(EntityId $id, CountryId $countryId, ChartName $chartName)
    {
        parent::__construct($id);
        $this->setCountryId($countryId)->setChartName($chartName);
    }

    public function setCountryId(CountryId $countryId)
    {
        $this->countryId = $countryId;
        $this->setBusinessId();
        return $this;
    }

    public function countryId()
    {
        return $this->countryId;
    }

    public function setChartName(ChartName $chartName)
    {
        $this->chartName = $chartName;
        $this->setBusinessId();
        return $this;
    }

    public function chartName()
    {
        return $this->chartName;
    }

    protected function setBusinessId()
    {
        if (empty($this->countryId) || empty($this->chartName)) {
            $this->businessId = null;
            return;
        }
        if (empty($this->businessId)) {
            $this->businessId = new ChartBusinessId($this->countryId, $this->chartName);
            return;
        }
        $this->businessId
            ->setCountryId($this->countryId)
            ->setChartName($this->chartName);
    }

    public function setCountryName(string $countryName)
    {
        if (empty($countryName)) {
            throw new ChartException("Can't set empty value in name of country.");
        }
        $this->countryName = $countryName;
        return $this;
    }

    public function countryName()
    {
        return $this->countryName;
    }

    public function setScheme(string $scheme)
    {
        $scheme = trim($scheme);
        if (empty($scheme)) {
            throw new ChartException("Can't set empty value in scheme.");
        }
        $this->scheme = $scheme;
        return $this;
    }

    public function scheme()
    {
        return $this->scheme;
    }

    public function setHost(string $host)
    {
        $host = trim($host);
        if (empty($host)) {
            throw new ChartException("Can't set empty value in host.");
        }
        $this->host = $host;
        return $this;
    }

    public function host()
    {
        return $this->host;
    }

    public function setUri(string $uri = null)
    {
        $this->uri = trim($uri);
        return $this;
    }

    public function uri()
    {
        return $this->uri;
    }

    public function setOriginalChartName(ChartName $originalChartName = null)
    {
        $this->originalChartName = $originalChartName;
        return $this;
    }

    public function originalChartName()
    {
        return $this->originalChartName;
    }

    public function setPageTitle(string $pageTitle)
    {
        $this->pageTitle = $pageTitle;
        return $this;
    }

    public function pageTitle()
    {
        return $this->pageTitle;
    }

    public function label()
    {
        return $this->countryName() . " - " . $this->chartName()->value();
    }

    public function strategyName() {
        $base = trim($this->businessId()->value());
        if (empty($base)) {
            return null;
        }
        $replaced = preg_replace("/[^_a-zA-Z0-9]/", "", $base);
        if (empty($replaced)) {
            return null;
        }
        $strategyName = preg_replace("/^[0-9]/", "", $replaced);
        if (empty($strategyName)) {
            return null;
        }
        return $strategyName;
    }

/*
May be, this will be deleted because we may use equals() instead of this.
    public function isSelected(ChartSummary $chartSummary = null)
    {
        if (empty($chartSummary)) {
            return false;
        }
        if ($this->getId()->equals($chartSummary->chartId())) {
            return true;
        }
        return false;
    }
*/

}

