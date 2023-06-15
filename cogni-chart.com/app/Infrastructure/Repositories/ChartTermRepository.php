<?php

namespace App\Infrastructure\Repositories;
use App\Domain\DomainRepository;
use App\Domain\ChartTerm\ChartTermRepositoryInterface;
use App\Infrastructure\RedisDAO\RedisDAOInterface;
use App\Domain\ChartTerm\ChartTermFactoryInterface;
use App\Domain\ChartTerm\ChartTermSpecification;
use App\Domain\EntityId;
use App\Domain\ChartTerm\ChartTermBusinessId;
use App\Domain\ChartTerm\ChartTermAggregation;
use App\Infrastructure\Eloquents\ChartTerm;
use App\Infrastructure\Eloquents\ProvisionedChartTerm;
use App\Infrastructure\Eloquents\ChartRanking;

class ChartTermRepository extends DomainRepository implements ChartTermRepositoryInterface
{

    private $chartTermFactory;

    public function __construct(RedisDAOInterface $redisDAO, ChartTermFactoryInterface $chartTermFactory)
    {
        parent::__construct($redisDAO);
        $this->chartTermFactory = $chartTermFactory;
    }

    public function findProvision(EntityId $id)
    {
        $row = ProvisionedChartTerm::find($id->value());
        if (empty($row)) {
            return null;
        }
        $chartTermEntity = $this->chartTermFactory->create(
            $row->id,
            $row->chart_id,
            $row->start_date,
            $row->end_date
        );
        return $chartTermEntity;
    }

    public function findRelease(EntityId $id)
    {
        $row = ChartTerm::find($id->value());
        if (empty($row)) {
            return null;
        }

        $chartTermEntity = $this->chartTermFactory->create(
            $row->id,
            $row->chart_id,
            $row->start_date,
            $row->end_date
        );
        return $chartTermEntity;
    }

    public function getProvision(ChartTermBusinessId $chartTermBusinessId, EntityId $excludeId = null)
    {
        $provisionedChartTerm = ProvisionedChartTerm::businessId($chartTermBusinessId->chartId()->value(), $chartTermBusinessId->endDate()->value());
        if (!empty($excludeId)) {
            $provisionedChartTerm->excludeId($excludeId->value());
        }
        $row = $provisionedChartTerm->first();
        if (empty($row)) {
            return null;
        }
        $chartTermEntity = $this->chartTermFactory->create(
            $row->id,
            $row->chart_id,
            $row->start_date,
            $row->end_date
        );
        return $chartTermEntity;
    }

    public function getRelease(ChartTermBusinessId $chartTermBusinessId, EntityId $excludeId = null)
    {
        $chartTerm = ChartTerm::businessId($chartTermBusinessId->chartId()->value(), $chartTermBusinessId->endDate()->value());
        if (!empty($excludeId)) {
            $chartTerm->excludeId($excludeId->value());
        }
        $row = $chartTerm->first();
        if (empty($row)) {
            return null;
        }
        $chartTermEntity = $this->chartTermFactory->create(
            $row->id,
            $row->chart_id,
            $row->start_date,
            $row->end_date
        );
        return $chartTermEntity;
    }

    public function findAggregationProvision(EntityId $id)
    {
        $chartTermEntity = $this->findProvision($id);
        if (empty($chartTermEntity)) {
            return null;
        }
        $chartTermAggregation = $this->chartTermFactory->toAggregation($chartTermEntity);
        return $this->addChartRanking($chartTermAggregation);
    }

    public function findAggregationRelease(EntityId $id)
    {
        $chartTermEntity = $this->findRelease($id);
        if (empty($chartTermEntity)) {
            return null;
        }
        $chartTermAggregation = $this->chartTermFactory->toAggregation($chartTermEntity);
        return $this->addChartRanking($chartTermAggregation);
    }

    public function getAggregationProvision(ChartTermBusinessId $chartTermBusinessId)
    {
        $chartTermEntity = $this->getProvision($chartTermBusinessId);
        if (empty($chartTermEntity)) {
            return null;
        }
        $chartTermAggregation = $this->chartTermFactory->toAggregation($chartTermEntity);
        return $this->addChartRanking($chartTermAggregation);
    }

    public function getAggregationRelease(ChartTermBusinessId $chartTermBusinessId)
    {
        $chartTermEntity = $this->getRelease($chartTermBusinessId);
        if (empty($chartTermEntity)) {
            return null;
        }
        $chartTermAggregation = $this->chartTermFactory->toAggregation($chartTermEntity);
        return $this->addChartRanking($chartTermAggregation);
    }

    public function getAggregationWithCache(ChartTermBusinessId $chartTermBusinessId, ChartTermSpecification $chartTermSpecification)
    {
        return $chartTermSpecification->getAggregationWithCache($chartTermBusinessId, $this);
    }

    public function refreshCachedAggregation(EntityId $entityId, ChartTermBusinessId $chartTermBusinessId, ChartTermSpecification $chartTermSpecification)
    {
        $chartTermSpecification->refreshCachedAggregation($entityId, $chartTermBusinessId, $this);
    }

    public function register(ChartTermAggregation $chartTermAggregation, ChartTermSpecification $chartTermSpecification)
    {
        $chartTermSpecification->register($chartTermAggregation, $this);
        $parameters = [
            'id'            =>  $chartTermAggregation->id()->value(),
            'chart_id'      =>  $chartTermAggregation->chartId()->value(),
            'start_date'    =>  $chartTermAggregation->startDate()->value(),
            'end_date'      =>  $chartTermAggregation->endDate()->value()
        ];
        $provisionedChartTerm = new ProvisionedChartTerm();
        if (!$provisionedChartTerm->fill($parameters)->save()) {
            return false;
        }
        if (!$this->deleteChartRanking($chartTermAggregation->id())) {
            return false;
        }
        return $this->registerChartRanking($chartTermAggregation);
    }

    public function modifyProvision(ChartTermAggregation $chartTermAggregation, ChartTermSpecification $chartTermSpecification)
    {
        $chartTermSpecification->modifyProvision($chartTermAggregation, $this);
        $parameters = [
            'chart_id'      =>  $chartTermAggregation->chartId()->value(),
            'start_date'    =>  $chartTermAggregation->startDate()->value(),
            'end_date'      =>  $chartTermAggregation->endDate()->value()
        ];
        $result = ProvisionedChartTerm::find($chartTermAggregation->id()->value())->fill($parameters)->save();
        if ($result !== true) {
            return false;
        }
        if (!$this->deleteChartRanking($chartTermAggregation->id())) {
            return false;
        }
        return $this->registerChartRanking($chartTermAggregation);
    }

    public function delete(EntityId $id, ChartTermSpecification $chartTermSpecification)
    {
        $chartTermSpecification->delete($id, $this);
        $result = ProvisionedChartTerm::destroy($id->value());
        if ($result !== 1) {
            return false;
        }
        return $this->deleteChartRanking($id);
    }

    public function release(EntityId $id, ChartTermSpecification $chartTermSpecification)
    {
        $releaseTarget = $chartTermSpecification->release($id, $this);
        $result = ProvisionedChartTerm::destroy($releaseTarget->id()->value());
        if ($result !== 1) {
            return false;
        }
        $parameters = [
            'id'            =>  $releaseTarget->id()->value(),
            'chart_id'      =>  $releaseTarget->chartId()->value(),
            'start_date'    =>  $releaseTarget->startDate()->value(),
            'end_date'      =>  $releaseTarget->endDate()->value()
        ];
        $chartTerm = new ChartTerm();
        return $chartTerm->fill($parameters)->save();
    }

    public function modifyRelease(ChartTermAggregation $chartTermAggregation, ChartTermSpecification $chartTermSpecification)
    {
        $chartTermSpecification->modifyRelease($chartTermAggregation, $this);
        $parameters = [
            'chart_id'      =>  $chartTermAggregation->chartId()->value(),
            'start_date'    =>  $chartTermAggregation->startDate()->value(),
            'end_date'      =>  $chartTermAggregation->endDate()->value()
        ];
        $result = ChartTerm::find($chartTermAggregation->id()->value())->fill($parameters)->save();
        if ($result !== true) {
            return false;
        }
        if (!$this->deleteChartRanking($chartTermAggregation->id())) {
            return false;
        }
        return $this->registerChartRanking($chartTermAggregation);
    }

    public function rollback(EntityId $id, ChartTermSpecification $chartTermSpecification)
    {
        $rollbackTarget = $chartTermSpecification->rollback($id, $this);
        $result = ChartTerm::destroy($rollbackTarget->id()->value());
        if ($result !== 1) {
            return false;
        }
        $parameters = [
            'id'            =>  $rollbackTarget->id()->value(),
            'chart_id'      =>  $rollbackTarget->chartId()->value(),
            'start_date'    =>  $rollbackTarget->startDate()->value(),
            'end_date'      =>  $rollbackTarget->endDate()->value()
        ];
        $provisionedChartTerm = new ProvisionedChartTerm();
        return $provisionedChartTerm->fill($parameters)->save();
    }

    protected function idExisting(EntityId $id) {
        $row = ChartTerm::find($id->value());
        if (!empty($row)) {
            return true;
        }
        $row = ProvisionedChartTerm::find($id->value());
        if (!empty($row)) {
            return true;
        }
        return false;
    }

    private function addChartRanking(ChartTermAggregation $chartTermAggregation)
    {
        $rows = ChartRanking::where(['chart_term_id' => $chartTermAggregation->id()->value()])->orderBy('ranking', 'asc')->get();
        if (empty($rows)) {
            return $chartTermAggregation;
        }
        foreach ($rows AS $row) {
            $this->chartTermFactory->addChartRanking(
                $chartTermAggregation,
                $row->ranking,
                $row->chart_ranking_item_id
            );
        }
        return $chartTermAggregation;
    }

    private function deleteChartRanking(EntityId $chartTermId)
    {
        $rows = ChartRanking::where(['chart_term_id' => $chartTermId->value()])->get();
        if (empty($rows)) {
            return true;
        }
        if ($rows->count() === 0) {
            return true;
        }
        $result = ChartRanking::where(['chart_term_id' => $chartTermId->value()])->delete();
        if ($result === 0) {
            return false;
        }
        return true;
    }

    private function registerChartRanking(ChartTermAggregation $chartTermAggregation)
    {
        $chartRankings = $chartTermAggregation->chartRankings();
        if (empty($chartRankings)) {
            return true;
        }
        foreach ($chartRankings AS $ranking) {
            $parameters = [
                'chart_term_id'         =>  $chartTermAggregation->id()->value(),
                'ranking'               =>  $ranking->ranking(),
                'chart_ranking_item_id' =>  $ranking->chartRankingItemId()->value()
            ];
            $chartRanking = new ChartRanking();
            if (!$chartRanking->fill($parameters)->save()) {
                return false;
            }
        }
        return true;
    }

}
