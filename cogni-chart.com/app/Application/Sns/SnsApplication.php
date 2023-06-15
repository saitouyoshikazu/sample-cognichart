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
use App\Domain\Chart\ChartSpecification;
use App\Domain\ChartTerm\ChartTermSpecification;
use View;
use Config;
use Log;

class SnsApplication implements SnsApplicationInterface
{

    private $chartRepository;
    private $chartTermRepository;
    private $chartRankingItemRepository;
    private $artistRepository;
    private $musicRepository;
    private $twitter;
    private $facebook;

    public function __construct(
        ChartRepositoryInterface $chartRepository,
        ChartTermRepositoryInterface $chartTermRepository,
        ChartRankingItemRepositoryInterface $chartRankingItemRepository,
        ArtistRepositoryInterface $artistRepository,
        MusicRepositoryInterface $musicRepository,
        TwitterInterface $twitter,
        FacebookInterface $facebook
    ) {
        $this->chartRepository = $chartRepository;
        $this->chartTermRepository = $chartTermRepository;
        $this->chartRankingItemRepository = $chartRankingItemRepository;
        $this->artistRepository = $artistRepository;
        $this->musicRepository = $musicRepository;
        $this->twitter = $twitter;
        $this->facebook = $facebook;
    }

    public function publishReleasedMessage(SnsDXO $snsDXO)
    {
        $chartBusinessId = $snsDXO->getChartBusinessId();
        $endDate = $snsDXO->getEndDate();
        if (empty($chartBusinessId) || empty($endDate)) {
            return false;
        }
        $chartAggregation = $this->chartRepository->getAggregationWithCache($chartBusinessId, new ChartSpecification());
        if (empty($chartAggregation)) {
            return true;
        }
        $chartTermList = $chartAggregation->chartTermList();
        $chartTermEntities = $chartTermList->chartTermEntities();
        $latestChartTermEntity = $chartTermEntities[0];
        if (!$latestChartTermEntity->endDate()->equals($endDate)) {
            return true;
        }
        $chartTermAggregation = $this->chartTermRepository->getAggregationWithCache(
            $latestChartTermEntity->businessId(),
            new ChartTermSpecification()
        );
        if (empty($chartTermAggregation)) {
            return true;
        }
        $chartRankings = $chartTermAggregation->chartRankings();
        $publishTo = intVal(Config::get('services.twitter.publish_to'));
        $ranking = 1;
        $rankingInformations = [];
        while ($ranking <= $publishTo) {
            $chartRanking = $chartRankings[$ranking];
            $chartRankingItemEntity = $this->chartRankingItemRepository->find($chartRanking->chartRankingItemId());
            $artistName = $chartRankingItemEntity->chartArtist()->value();
            if (!empty($chartRankingItemEntity->artistId())) {
                $artistEntity = $this->artistRepository->findRelease($chartRankingItemEntity->artistId());
                if (!empty($artistEntity)) {
                    $artistName = $artistEntity->artistName()->value();
                }
            }
            $musicTitle = $chartRankingItemEntity->chartMusic()->value();
            $rankingInformations[] = [
                'ranking' => $ranking,
                'artistName' => $artistName,
                'musicTitle' => $musicTitle
            ];
            $ranking++;
        }
        $tweet = View::make(
            'www.sns.twitter.release',
            [
                'chartAggregation' => $chartAggregation,
                'chartTermAggregation' => $chartTermAggregation,
                'rankingInformations' => $rankingInformations
            ]
        )->render();
        $this->twitter->post($tweet);
/*
        $facebookPost = View::make(
            'www.sns.facebook.release',
            [
                'chartAggregation' => $chartAggregation,
                'chartTermAggregation' => $chartTermAggregation,
                'rankingInformations' => $rankingInformations
            ]
        )->render();
        $this->facebook->post($facebookPost);
 */
    }

}
