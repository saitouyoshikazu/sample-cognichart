<?php

namespace App\Application\DXO;
use App\Application\DXO\Traits\TraitPhase;
use App\Application\DXO\Traits\TraitEntityId;
use App\Application\DXO\Traits\TraitCountryId;
use App\Application\DXO\Traits\TraitChartName;
use App\Domain\Chart\ChartBusinessId;
use App\Domain\ValueObjects\ChartName;

class ChartDXO
{

    use TraitPhase, TraitEntityId, TraitCountryId, TraitChartName;

    private $schemeValue;
    private $hostValue;
    private $uriValue;
    private $originalChartNameValue;
    private $pageTitleValue;

    public function list(string $phaseValue)
    {
        $this->phaseValue = $phaseValue;
    }

    public function register(
        string $countryIdValue,
        string $chartNameValue,
        string $schemeValue,
        string $hostValue,
        string $uriValue = null,
        string $originalChartNameValue = null,
        string $pageTitleValue
    ) {
        $this->countryIdValue = $countryIdValue;
        $this->schemeValue = $schemeValue;
        $this->chartNameValue = $chartNameValue;
        $this->hostValue = $hostValue;
        $this->uriValue = $uriValue;
        $this->originalChartNameValue = $originalChartNameValue;
        $this->pageTitleValue = $pageTitleValue;
    }

    public function get(string $phaseValue, string $countryIdValue, string $chartNameValue)
    {
        $this->phaseValue = $phaseValue;
        $this->countryIdValue = $countryIdValue;
        $this->chartNameValue = $chartNameValue;
    }

    public function modify(
        string $phaseValue,
        string $entityIdValue,
        string $countryIdValue,
        string $chartNameValue,
        string $schemeValue,
        string $hostValue,
        string $uriValue = null,
        string $originalChartNameValue = null,
        string $pageTitleValue
    ) {
        $this->phaseValue = $phaseValue;
        $this->entityIdValue = $entityIdValue;
        $this->countryIdValue = $countryIdValue;
        $this->chartNameValue = $chartNameValue;
        $this->schemeValue = $schemeValue;
        $this->hostValue = $hostValue;
        $this->uriValue = $uriValue;
        $this->originalChartNameValue = $originalChartNameValue;
        $this->pageTitleValue = $pageTitleValue;
    }

    public function release(string $entityIdValue)
    {
        $this->entityIdValue = $entityIdValue;
    }

    public function rollback(string $entityIdValue)
    {
        $this->entityIdValue = $entityIdValue;
    }

    public function delete(string $entityIdValue)
    {
        $this->entityIdValue = $entityIdValue;
    }

    public function refreshCachedAggregation(string $entityIdValue, string $countryIdValue, string $chartNameValue)
    {
        $this->entityIdValue = $entityIdValue;
        $this->countryIdValue = $countryIdValue;
        $this->chartNameValue = $chartNameValue;
    }

    public function frontGet(string $countryIdValue, string $chartNameValue)
    {
        $this->countryIdValue = $countryIdValue;
        $this->chartNameValue = $chartNameValue;
    }

    public function getBusinessId()
    {
        $countryId = $this->getCountryId();
        $chartName = $this->getChartName();
        if (empty($countryId) || empty($chartName)) {
            return null;
        }
        return new ChartBusinessId($countryId, $chartName);
    }

    public function getScheme()
    {
        return trim($this->schemeValue);
    }

    public function getHost()
    {
        return trim($this->hostValue);
    }

    public function getUri()
    {
        return trim($this->uriValue);
    }

    public function getOriginalChartName()
    {
        $originalChartNameValue = trim($this->originalChartNameValue);
        if (empty($originalChartNameValue)) {
            return null;
        }
        return new ChartName($originalChartNameValue);
    }

    public function getPageTitle()
    {
        return trim($this->pageTitleValue);
    }

}
