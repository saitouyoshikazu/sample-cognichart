<?php

namespace App\Application\ChartRankingItem;
use App\Domain\ChartRankingItem\ChartRankingItemRepositoryInterface;
use App\Domain\ChartRankingItem\ChartRankingItemFactoryInterface;
use App\Application\DXO\ChartRankingItemDXO;

interface ChartRankingItemApplicationInterface
{

    public function __construct(
        ChartRankingItemRepositoryInterface $chartRankingItemRepository,
        ChartRankingItemFactoryInterface $chartRankingItemFactory
    );

    public function exists(ChartRankingItemDXO $chartRankingItemDXO);

    public function find(ChartRankingItemDXO $chartRankingItemDXO);

    public function get(ChartRankingItemDXO $chartRankingItemDXO);

    public function register(ChartRankingItemDXO $chartRankingItemDXO);

    public function modify(ChartRankingItemDXO $chartRankingItemDXO);

    public function refreshCachedEntity(ChartRankingItemDXO $chartRankingItemDXO);

    public function detachArtist(ChartRankingItemDXO $chartRankingItemDXO);

    public function detachMusic(ChartRankingItemDXO $chartRankingItemDXO);

    public function notAttachedPaginator(ChartRankingItemDXO $chartRankingItemDXO);

}
