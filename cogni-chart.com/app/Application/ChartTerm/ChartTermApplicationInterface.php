<?php

namespace App\Application\ChartTerm;
use App\Domain\Chart\ChartRepositoryInterface;
use App\Domain\ChartTerm\ChartTermListRepositoryInterface;
use App\Domain\ChartTerm\ChartTermRepositoryInterface;
use App\Domain\ChartTerm\ChartTermFactoryInterface;
use App\Application\ChartRankingItem\ChartRankingItemApplicationInterface;
use App\Application\AbstractArtistMusic\AbstractArtistMusicApplicationInterface;
use App\Application\DXO\ChartTermDXO;

interface ChartTermApplicationInterface
{

    public function __construct(
        ChartRepositoryInterface $chartRepository,
        ChartTermListRepositoryInterface $chartTermListRepository,
        ChartTermRepositoryInterface $chartTermRepository,
        ChartTermFactoryInterface $chartTermFactory,
        ChartRankingItemApplicationInterface $chartRankingItemApplication,
        AbstractArtistMusicApplicationInterface $abstractArtistMusicApplication
    );

    public function list(ChartTermDXO $chartTermDXO);

    public function aggregation(ChartTermDXO $chartTermDXO);

    public function masterAggregation(ChartTermDXO $chartTermDXO);

    public function register(ChartTermDXO $chartTermDXO);

    public function delete(ChartTermDXO $chartTermDXO);

    public function release(ChartTermDXO $chartTermDXO);

    public function rollback(ChartTermDXO $chartTermDXO);

    public function refreshCachedAggregation(ChartTermDXO $chartTermDXO);

    public function resolve(ChartTermDXO $chartTermDXO);

}
