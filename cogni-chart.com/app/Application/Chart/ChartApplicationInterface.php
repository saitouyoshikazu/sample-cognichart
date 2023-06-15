<?php

namespace App\Application\Chart;
use App\Domain\Chart\ChartRepositoryInterface;
use App\Domain\Chart\ChartFactoryInterface;
use App\Domain\Chart\ChartListRepositoryInterface;
use App\Application\DXO\ChartDXO;

interface ChartApplicationInterface
{

    public function __construct(
        ChartRepositoryInterface $chartRepository,
        ChartFactoryInterface $chartFactory,
        ChartListRepositoryInterface $chartListRepository
    );

    public function list(ChartDXO $chartDXO);

    public function register(ChartDXO $chartDXO);

    public function get(ChartDXO $chartDXO);

    public function modify(ChartDXO $chartDXO);

    public function release(ChartDXO $chartDXO);

    public function rollback(ChartDXO $chartDXO);

    public function delete(ChartDXO $chartDXO);

    public function refreshCachedChartList();

    public function refreshCachedAggregation(ChartDXO $chartDXO);

    public function frontGet(ChartDXO $chartDXO);

}
