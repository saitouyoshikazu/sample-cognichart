<?php

namespace App\Application\Sns;
use App\Domain\Chart\ChartRepositoryInterface;
use App\Domain\ChartTerm\ChartTermRepositoryInterface;
use App\Domain\ChartRankingItem\ChartRankingItemRepositoryInterface;
use App\Domain\Artist\ArtistRepositoryInterface;
use App\Domain\Music\MusicRepositoryInterface;
use App\Infrastructure\Sns\TwitterInterface;
use App\Infrastructure\Sns\FacebookInterface;
use App\Application\DXO\SnsDXO;

interface SnsApplicationInterface
{

    public function __construct(
        ChartRepositoryInterface $chartRepository,
        ChartTermRepositoryInterface $chartTermRepository,
        ChartRankingItemRepositoryInterface $chartRankingItemRepository,
        ArtistRepositoryInterface $artistRepository,
        MusicRepositoryInterface $musicRepository,
        TwitterInterface $twitter,
        FacebookInterface $facebook
    );

    public function publishReleasedMessage(SnsDXO $snsDXO);

}
