<?php

namespace App\Domain\Chart;
use App\Domain\Country\CountryRepositoryInterface;
use App\Domain\ChartTerm\ChartTermList;

interface ChartFactoryInterface
{

    /**
     * Constructor.
     * @param   CountryRepositoryInterface  $countryRepository  CountryRepository.
     */
    public function __construct(CountryRepositoryInterface $countryRepository);

    /**
     * Create ChartEntity.
     * @param   string  $idValue                    The id of chart.
     * @param   string  $countryIdValue             The id of country.
     * @param   string  $chartNameValue             The name of chart.
     * @param   string  $scheme                     Scheme of chart publisher.
     * @param   string  $host                       Name of host server or domain name of chart publisher.
     * @param   string  $uri                        Uri following to $host of chart publisher.
     * @param   string  $originalChartNameValue     The name of original chart.
     * @param   string  $pageTitleValue             The title of Chart page.
     * @return  ChartEntity When ChartEntity was correctly created.
     *          null        When failed to create ChartEntity.
     */
    public function create(
        string  $idValue,
        string  $countryIdValue,
        string  $chartNameValue,
        string  $scheme,
        string  $host,
        string  $uri,
        string  $originalChartNameValue = null,
        string  $pageTitleValue
    );

    /**
     * Create ChartAggregation from ChartEntity.
     * @param   ChartEntity     $chartEntity        ChartEntity.
     * @param   ChartTermList   $chartTermList      ChartTermList.
     * @return  ChartAggregation    When ChartAggregation was correctly created.
     *          null                When failed to create ChartAggregation.
     */
    public function toAggregation(
        ChartEntity $chartEntity,
        ChartTermList $chartTermList = null
    );

}
