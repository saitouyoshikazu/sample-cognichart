<?php

namespace App\Domain\Chart;
use App\Domain\ValueObjects\ChartName;
use App\Domain\EntityId;
use App\Domain\Country\CountryRepositoryInterface;
use App\Domain\Country\CountryId;
use App\Domain\Country\CountrySpecification;
use App\Domain\ChartTerm\ChartTermList;

class ChartFactory implements ChartFactoryInterface
{

    private $countryRepository;

    public function __construct(CountryRepositoryInterface $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    public function create(
        string  $idValue,
        string  $countryIdValue,
        string  $chartNameValue,
        string  $scheme,
        string  $host,
        string  $uri,
        string  $originalChartNameValue = null,
        string  $pageTitleValue
    ) {
        $countrySpecification = new CountrySpecification();
        $countryId = new CountryId($countryIdValue);
        $countryEntity = $this->countryRepository->findWithCache($countryId, $countrySpecification);
        if (empty($countryEntity)) {
            return null;
        }
        $entityId = new EntityId($idValue);
        $chartName = new ChartName($chartNameValue);
        $originalChartNameValue = trim($originalChartNameValue);
        $originalChartName = !empty($originalChartNameValue) ? new ChartName($originalChartNameValue) : null;
        $pageTitleValue = trim($pageTitleValue);
        $chartEntity = new ChartEntity($entityId, $countryId, $chartName);
        $chartEntity
            ->setCountryName($countryEntity->getCountryName())
            ->setScheme($scheme)
            ->setHost($host)
            ->setUri($uri)
            ->setOriginalChartName($originalChartName)
            ->setPageTitle($pageTitleValue);
        return $chartEntity;
    }

    public function toAggregation(
        ChartEntity $chartEntity,
        ChartTermList $chartTermList = null
    ) {
        $chartAggregation = new ChartAggregation(
            $chartEntity->id(),
            $chartEntity->countryId(),
            $chartEntity->chartName()
        );
        $chartAggregation
            ->setCountryName($chartEntity->countryName())
            ->setScheme($chartEntity->scheme())
            ->setHost($chartEntity->host())
            ->setUri($chartEntity->uri())
            ->setOriginalChartName($chartEntity->originalChartName())
            ->setPageTitle($chartEntity->pageTitle());
        if (!empty($chartTermList)) {
            $chartAggregation->setChartTermList($chartTermList);
        }
        return $chartAggregation;
    }

}
