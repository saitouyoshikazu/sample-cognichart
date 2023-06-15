<?php

namespace App\Domain\ChartTerm;
use App\Domain\EntityId;
use App\Domain\ValueObjects\Phase;

interface ChartTermListRepositoryInterface
{

    /**
     * Constructor.
     * @param   ChartTermFactoryInterface   $chartTermFactory   ChartTermFactory.
     */
    public function __construct(ChartTermFactoryInterface $chartTermFactory);

    /**
     * Get ChartTermList depending on $phase.
     * @param   EntityId    $chartId    Entity id of Chart.
     * @param   Phase       $phase      Phase.
     * @return  ChartTermList   When all of ChartTerm correctly searched.
     *          null            When there is no ChartTerm or when failed to search ChartTerm.
     */
    public function chartTermList(EntityId $chartId, Phase $phase);

    /**
     * Get ChartTermList that is released.
     * @param   EntityId        $chartId    Entity id of Chart.
     * @return  ChartTermList   When all of ChartTerm correctly searched.
     *          null            When there is no ChartTerm or when failed to search ChartTerm.
     */
    public function releasedChartTermList(EntityId $chartId);

    /**
     * Get ChartTermList that is provisioned.
     * @param   EntityId        $chartId    Entity id of Chart.
     * @return  ChartTermList   When all of ChartTerm correctly searched.
     *          null            When there is no ChartTerm or when failed to search ChartTerm.
     */
    public function provisionedChartTermList(EntityId $chartId);

}
