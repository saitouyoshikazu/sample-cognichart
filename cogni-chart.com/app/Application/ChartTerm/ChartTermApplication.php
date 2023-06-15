<?php

namespace App\Application\ChartTerm;
use App\Domain\Chart\ChartRepositoryInterface;
use App\Domain\ChartTerm\ChartTermListRepositoryInterface;
use App\Domain\ChartTerm\ChartTermRepositoryInterface;
use App\Domain\ChartTerm\ChartTermFactoryInterface;
use App\Application\ChartRankingItem\ChartRankingItemApplicationInterface;
use App\Application\AbstractArtistMusic\AbstractArtistMusicApplicationInterface;
use DB;
use Event;
use App\Application\DXO\ChartTermDXO;
use App\Application\DXO\ChartRankingItemDXO;
use App\Application\DXO\AbstractArtistMusicDXO;
use App\Domain\ChartTerm\ChartTermSpecification;
use App\Domain\ChartTerm\ChartTermException;
use App\Events\ChartTermReleased;
use App\Events\ChartTermRollbacked;
use Log;

class ChartTermApplication implements ChartTermApplicationInterface
{

    private $chartRepository;
    private $chartTermListRepository;
    private $chartTermRepository;
    private $chartTermFactory;
    private $chartRankingItemApplication;
    private $abstractArtistMusicApplication;

    public function __construct(
        ChartRepositoryInterface $chartRepository,
        ChartTermListRepositoryInterface $chartTermListRepository,
        ChartTermRepositoryInterface $chartTermRepository,
        ChartTermFactoryInterface $chartTermFactory,
        ChartRankingItemApplicationInterface $chartRankingItemApplication,
        AbstractArtistMusicApplicationInterface $abstractArtistMusicApplication
    ) {
        $this->chartRepository = $chartRepository;
        $this->chartTermListRepository = $chartTermListRepository;
        $this->chartTermRepository = $chartTermRepository;
        $this->chartTermFactory = $chartTermFactory;
        $this->chartRankingItemApplication = $chartRankingItemApplication;
        $this->abstractArtistMusicApplication = $abstractArtistMusicApplication;
    }

    public function list(ChartTermDXO $chartTermDXO)
    {
        $chartId = $chartTermDXO->getChartId();
        $phase = $chartTermDXO->getPhase();
        if (empty($chartId) || empty($phase)) {
            return null;
        }
        return $this->chartTermListRepository->chartTermList($chartId, $phase);
    }

    public function aggregation(ChartTermDXO $chartTermDXO)
    {
        $chartTermBusinessId = $chartTermDXO->getBusinessId();
        if (empty($chartTermBusinessId)) {
            return null;
        }
        return $this->chartTermRepository->getAggregationWithCache($chartTermBusinessId, new ChartTermSpecification());
    }

    public function masterAggregation(ChartTermDXO $chartTermDXO)
    {
        $phase = $chartTermDXO->getPhase();
        $chartTermBusinessId = $chartTermDXO->getBusinessId();
        if (empty($phase) || empty($chartTermBusinessId)) {
            return null;
        }
        if ($phase->isReleased()) {
            return $this->chartTermRepository->getAggregationRelease($chartTermBusinessId);
        } else if ($phase->isProvisioned()) {
            return $this->chartTermRepository->getAggregationProvision($chartTermBusinessId);
        }
        return null;
    }

    public function register(ChartTermDXO $chartTermDXO)
    {
        $chartId = $chartTermDXO->getChartId();
        $startDate = $chartTermDXO->getStartDate();
        $endDate = $chartTermDXO->getEndDate();
        if (empty($chartId) || empty($startDate) || empty($endDate)) {
            return false;
        }
        $chartRankingRows = $chartTermDXO->getChartRankings();
        if (empty($chartRankingRows)) {
            throw new ChartTermException("Rankings of ChartTerm was empty. : {$chartId->value()}, {$endDate->value()}");
        }
        $chartTermEntity = $this->chartTermFactory->create(
            $this->chartTermRepository->createId()->value(),
            $chartId->value(),
            $startDate->value(),
            $endDate->value()
        );
        $chartTermAggregation = $this->chartTermFactory->toAggregation($chartTermEntity);
        foreach ($chartRankingRows AS $chartRankingRow) {
            $this->chartTermFactory->addChartRanking(
                $chartTermAggregation,
                $chartRankingRow->ranking,
                $chartRankingRow->chart_ranking_item_id
            );
        }

        DB::beginTransaction();
        try {
            $result = $this->chartTermRepository->register($chartTermAggregation, new ChartTermSpecification());
            if ($result === false) {
                DB::rollback();
                return false;
            }
        } catch(\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();
        return true;
    }

    public function delete(ChartTermDXO $chartTermDXO)
    {
        $entityId = $chartTermDXO->getEntityId();
        if (empty($entityId)) {
            return false;
        }
        DB::beginTransaction();
        try {
            $result = $this->chartTermRepository->delete($entityId, new ChartTermSpecification());
            if ($result === false) {
                DB::rollback();
                return false;
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();
        return true;
    }

    public function release(ChartTermDXO $chartTermDXO)
    {
        $entityId = $chartTermDXO->getEntityId();
        $publishReleasedMessage = $chartTermDXO->getPublishReleasedMessage();
        if (empty($entityId)) {
            return false;
        }

        $releasedEntity = null;
        DB::beginTransaction();
        try {
            $result = $this->chartTermRepository->release($entityId, new ChartTermSpecification());
            if ($result === false) {
                DB::rollback();
                return false;
            }
            $releasedEntity = $this->chartTermRepository->findRelease($entityId);
            if (empty($releasedEntity)) {
                DB::rollback();
                return false;
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();

        $chartEntity = $this->chartRepository->findRelease($releasedEntity->chartId());
        $countryIdValue = null;
        $chartNameValue = null;
        if (!empty($chartEntity)) {
            $countryIdValue = $chartEntity->countryId()->value();
            $chartNameValue = $chartEntity->chartName()->value();
        }
        Event::dispatch(
            new ChartTermReleased(
                $releasedEntity->id()->value(),
                $releasedEntity->chartId()->value(),
                $releasedEntity->endDate()->value(),
                $countryIdValue,
                $chartNameValue,
                $publishReleasedMessage
            )
        );
        return true;
    }

    public function rollback(ChartTermDXO $chartTermDXO)
    {
        $entityId = $chartTermDXO->getEntityId();
        if (empty($entityId)) {
            return false;
        }

        $rollbackedEntity = null;
        DB::beginTransaction();
        try {
            $result = $this->chartTermRepository->rollback($entityId, new ChartTermSpecification());
            if ($result === false) {
                DB::rollback();
                return false;
            }
            $rollbackedEntity = $this->chartTermRepository->findProvision($entityId);
            if (empty($rollbackedEntity)) {
                DB::rollback();
                return false;
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();

        $chartEntity = $this->chartRepository->findRelease($rollbackedEntity->chartId());
        $countryIdValue = null;
        $chartNameValue = null;
        if (!empty($chartEntity)) {
            $countryIdValue = $chartEntity->countryId()->value();
            $chartNameValue = $chartEntity->chartName()->value();
        }
        Event::dispatch(
            new ChartTermRollbacked(
                $rollbackedEntity->id()->value(),
                $rollbackedEntity->chartId()->value(),
                $rollbackedEntity->endDate()->value(),
                $countryIdValue,
                $chartNameValue
            )
        );
        return true;
    }

    public function refreshCachedAggregation(ChartTermDXO $chartTermDXO)
    {
        $entityId = $chartTermDXO->getEntityId();
        $chartTermBusinessId = $chartTermDXO->getBusinessId();
        if (empty($entityId) || empty($chartTermBusinessId)) {
            return false;
        }
        $this->chartTermRepository->refreshCachedAggregation($entityId, $chartTermBusinessId, new ChartTermSpecification());
        return true;
    }

    public function resolve(ChartTermDXO $chartTermDXO)
    {
        $phase = $chartTermDXO->getPhase();
        $entityId = $chartTermDXO->getEntityId();
        if (empty($phase) || empty($entityId)) {
            return false;
        }
        $chartTermAggregation = null;
        if ($phase->isProvisioned()) {
            $chartTermAggregation = $this->chartTermRepository->findAggregationProvision($entityId);
        } else if ($phase->isReleased()) {
            $chartTermAggregation = $this->chartTermRepository->findAggregationRelease($entityId);
        }
        if (empty($chartTermAggregation)) {
            return false;
        }
        $chartRankings = $chartTermAggregation->chartRankings();
        if (empty($chartRankings)) {
            return false;
        }

        Log::info('Resolving ChartRankingItems is started.');

        foreach ($chartRankings AS $chartRanking) {
            $chartRankingItemDXO = new ChartRankingItemDXO();
            $chartRankingItemDXO->find($chartRanking->chartRankingItemId()->value());
            $chartRankingItemEntity = $this->chartRankingItemApplication->find($chartRankingItemDXO);
            if (empty($chartRankingItemEntity)) {
                continue;
            }
            if ($chartRankingItemEntity->isResolved()) {
                continue;
            }

            Log::info($chartRankingItemEntity->id()->value());

            $abstractArtistMusicDXO = new AbstractArtistMusicDXO();
            $abstractArtistMusicDXO->prepare(
                $chartRankingItemEntity->id()->value(),
                $chartRankingItemEntity->chartArtist()->value(),
                $chartRankingItemEntity->chartMusic()->value()
            );
            if ($this->abstractArtistMusicApplication->prepare($abstractArtistMusicDXO) === false) {
                continue;
            }
            $abstractArtistMusicDXO = new AbstractArtistMusicDXO();
            $abstractArtistMusicDXO->resolve(
                $chartRankingItemEntity->id()->value(),
                $chartRankingItemEntity->chartArtist()->value(),
                $chartRankingItemEntity->chartMusic()->value()
            );
            $this->abstractArtistMusicApplication->resolve($abstractArtistMusicDXO);
            set_time_limit(ini_get('max_execution_time'));
            sleep(10);
        }

        Log::info('Resolving ChartRankingItems is end.');

        return true;
    }

}
