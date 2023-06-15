<?php

namespace App\Domain\ChartTerm;

interface ChartTermFactoryInterface
{

    /**
     * Create ChartTermEntity.
     * @param   string  $idValue            Value of ChartTermEntity id.
     * @param   string  $chartIdValue       Value of ChartEntity id.
     * @param   string  $startDateValue     Start date of ChartTerm.
     * @param   string  $endDateValue       End date of ChartTerm.
     * @return  ChartTermEntity     When ChartTermEntity was correctly created.
     *          null                When failed to create ChartTermEntity.
     * @throws  ValueObjectException
     */
    public function create(
        string $idValue,
        string $chartIdValue,
        string $startDateValue,
        string $endDateValue
    );

    /**
     * Convert ChartTermEntity to ChartTermAggregation.
     * @param   ChartTermEntity     $chartTermEntity    ChartTermEntity.
     * @return  ChartTermAggregation    ChartTermAggregation.
     */
    public function toAggregation(ChartTermEntity $chartTermEntity);

    /**
     * Add ranking of ChartTerm to ChartTermAggregation.
     * @param   ChartTermAggregation    &$chartTermAggregation      ChartTermAggregation.
     * @param   int                     $ranking                    Ranking of ChartRankingItem.
     * @param   string                  $chartRankingItemIdValue    The id of ChartRankingItem.
     * @throws  ChartTermException
     */
    public function addChartRanking(
        ChartTermAggregation    &$chartTermAggregation,
        int                     $ranking,
        string                  $chartRankingItemIdValue
    );

}
