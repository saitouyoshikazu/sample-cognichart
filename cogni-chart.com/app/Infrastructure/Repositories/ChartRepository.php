<?php

namespace App\Infrastructure\Repositories;
use App\Domain\DomainRepository;
use App\Domain\Chart\ChartRepositoryInterface;
use App\Infrastructure\RedisDAO\RedisDAOInterface;
use App\Domain\Chart\ChartFactoryInterface;
use App\Domain\ChartTerm\ChartTermListRepositoryInterface;
use App\Domain\ValueObjects\Phase;
use App\Domain\EntityId;
use App\Domain\Chart\ChartSpecification;
use App\Domain\Chart\ChartBusinessId;
use App\Domain\Chart\ChartEntity;
use App\Infrastructure\Eloquents\Chart;
use App\Infrastructure\Eloquents\ProvisionedChart;

class ChartRepository extends DomainRepository implements ChartRepositoryInterface
{

    private $chartFactory;
    private $chartTermListRepository;

    public function __construct(
        RedisDAOInterface $redisDAO,
        ChartFactoryInterface $chartFactory,
        ChartTermListRepositoryInterface $chartTermListRepository
    ) {
        parent::__construct($redisDAO);
        $this->chartFactory = $chartFactory;
        $this->chartTermListRepository = $chartTermListRepository;
    }

    public function findProvision(EntityId $id)
    {
        $row = ProvisionedChart::find($id->value());
        if (empty($row)) {
            return null;
        }
        $chartEntity = $this->chartFactory->create(
            $row->id,
            $row->country_id,
            $row->chart_name,
            $row->scheme,
            $row->host,
            $row->uri,
            $row->original_chart_name,
            $row->page_title
        );
        return $chartEntity;
    }

    public function findRelease(EntityId $id)
    {
        $row = Chart::find($id->value());
        if (empty($row)) {
            return null;
        }

        $chartEntity = $this->chartFactory->create(
            $row->id,
            $row->country_id,
            $row->chart_name,
            $row->scheme,
            $row->host,
            $row->uri,
            $row->original_chart_name,
            $row->page_title
        );
        return $chartEntity;
    }

    public function getProvision(ChartBusinessId $chartBusinessId, EntityId $excludeId = null)
    {
        $provisionedChart = ProvisionedChart::businessId($chartBusinessId->countryId()->value(), $chartBusinessId->chartName()->value());
        if (!empty($excludeId)) {
            $provisionedChart->excludeId($excludeId->value());
        }
        $row = $provisionedChart->first();
        if (empty($row)) {
            return null;
        }
        $chartEntity = $this->chartFactory->create(
            $row->id,
            $row->country_id,
            $row->chart_name,
            $row->scheme,
            $row->host,
            $row->uri,
            $row->original_chart_name,
            $row->page_title
        );
        return $chartEntity;
    }

    public function getRelease(ChartBusinessId $chartBusinessId, EntityId $excludeId = null)
    {
        $chart = Chart::businessId($chartBusinessId->countryId()->value(), $chartBusinessId->chartName()->value());
        if (!empty($excludeId)) {
            $chart->excludeId($excludeId->value());
        }
        $row = $chart->first();
        if (empty($row)) {
            return null;
        }
        $chartEntity = $this->chartFactory->create(
            $row->id,
            $row->country_id,
            $row->chart_name,
            $row->scheme,
            $row->host,
            $row->uri,
            $row->original_chart_name,
            $row->page_title
        );
        return $chartEntity;
    }

    public function findAggregationProvision(EntityId $id, Phase $chartTermPhase)
    {
        $chartEntity = $this->findProvision($id);
        if (empty($chartEntity)) {
            return null;
        }
        $chartTermList = $this->chartTermListRepository->chartTermList($id, $chartTermPhase);
        return $this->chartFactory->toAggregation($chartEntity, $chartTermList);
    }

    public function findAggregationRelease(EntityId $id, Phase $chartTermPhase)
    {
        $chartEntity = $this->findRelease($id);
        if (empty($chartEntity)) {
            return null;
        }
        $chartTermList = $this->chartTermListRepository->chartTermList($id, $chartTermPhase);
        return $this->chartFactory->toAggregation($chartEntity, $chartTermList);
    }

    public function getAggregationProvision(ChartBusinessId $chartBusinessId, Phase $chartTermPhase)
    {
        $chartEntity = $this->getProvision($chartBusinessId);
        if (empty($chartEntity)) {
            return null;
        }
        $chartTermList = $this->chartTermListRepository->chartTermList($chartEntity->id(), $chartTermPhase);
        return $this->chartFactory->toAggregation($chartEntity, $chartTermList);
    }

    public function getAggregationRelease(ChartBusinessId $chartBusinessId, Phase $chartTermPhase)
    {
        $chartEntity = $this->getRelease($chartBusinessId);
        if (empty($chartEntity)) {
            return null;
        }
        $chartTermList = $this->chartTermListRepository->chartTermList($chartEntity->id(), $chartTermPhase);
        return $this->chartFactory->toAggregation($chartEntity, $chartTermList);
    }

    public function getAggregationWithCache(ChartBusinessId $chartBusinessId, ChartSpecification $chartSpecification)
    {
        return $chartSpecification->getAggregationWithCache($chartBusinessId, $this);
    }

    public function refreshCachedAggregation(EntityId $entityId, ChartBusinessId $businessId, ChartSpecification $chartSpecification)
    {
        $chartSpecification->refreshCachedAggregation($entityId, $businessId, $this);
    }

    public function register(ChartEntity $chartEntity, ChartSpecification $chartSpecification)
    {
        $chartSpecification->register($chartEntity, $this);
        $displayPosition = ProvisionedChart::max('display_position') + 1;
        $originalChartNameValue = "";
        if (!empty($chartEntity->originalChartName())) {
            $originalChartNameValue = $chartEntity->originalChartName()->value();
        }
        $parameters = [
            'id'                    =>  $chartEntity->id()->value(),
            'country_id'            =>  $chartEntity->countryId()->value(),
            'display_position'      =>  $displayPosition,
            'chart_name'            =>  $chartEntity->chartName()->value(),
            'scheme'                =>  $chartEntity->scheme(),
            'host'                  =>  $chartEntity->host(),
            'uri'                   =>  $chartEntity->uri(),
            'original_chart_name'   =>  $originalChartNameValue,
            'page_title'            =>  $chartEntity->pageTitle()
        ];
        $provisionedChart = new ProvisionedChart();
        return $provisionedChart->fill($parameters)->save();
    }

    public function modifyProvision(ChartEntity $chartEntity, ChartSpecification $chartSpecification)
    {
        $chartSpecification->modifyProvision($chartEntity, $this);
        $originalChartNameValue = "";
        if (!empty($chartEntity->originalChartName())) {
            $originalChartNameValue = $chartEntity->originalChartName()->value();
        }
        $parameters = [
            'country_id'            =>  $chartEntity->countryId()->value(),
            'chart_name'            =>  $chartEntity->chartName()->value(),
            'scheme'                =>  $chartEntity->scheme(),
            'host'                  =>  $chartEntity->host(),
            'uri'                   =>  $chartEntity->uri(),
            'original_chart_name'   =>  $originalChartNameValue,
            'page_title'            =>  $chartEntity->pageTitle()
        ];
        return ProvisionedChart::find($chartEntity->id()->value())->fill($parameters)->save();
    }

    public function delete(EntityId $id, ChartSpecification $chartSpecification)
    {
        $chartSpecification->delete($id, $this);
        $result = ProvisionedChart::destroy($id->value());
        if ($result !== 1) {
            return false;
        }
        return true;
    }

    public function release(EntityId $id, ChartSpecification $chartSpecification)
    {
        $releaseTarget = $chartSpecification->release($id, $this);
        if (!$this->delete($releaseTarget->id(), $chartSpecification)) {
            return false;
        }
        $displayPosition = Chart::max('display_position') + 1;
        $originalChartNameValue = "";
        if (!empty($releaseTarget->originalChartName())) {
            $originalChartNameValue = $releaseTarget->originalChartName()->value();
        }
        $parameters = [
            'id'                    =>  $releaseTarget->id()->value(),
            'country_id'            =>  $releaseTarget->countryId()->value(),
            'display_position'      =>  $displayPosition,
            'chart_name'            =>  $releaseTarget->chartName()->value(),
            'scheme'                =>  $releaseTarget->scheme(),
            'host'                  =>  $releaseTarget->host(),
            'uri'                   =>  $releaseTarget->uri(),
            'original_chart_name'   =>  $originalChartNameValue,
            'page_title'            =>  $releaseTarget->pageTitle()
        ];
        $chart = new Chart();
        return $chart->fill($parameters)->save();
    }

    public function modifyRelease(ChartEntity $chartEntity, ChartSpecification $chartSpecification)
    {
        $chartSpecification->modifyRelease($chartEntity, $this);
        $originalChartNameValue = "";
        if (!empty($chartEntity->originalChartName())) {
            $originalChartNameValue = $chartEntity->originalChartName()->value();
        }
        $parameters = [
            'country_id'            =>  $chartEntity->countryId()->value(),
            'chart_name'            =>  $chartEntity->chartName()->value(),
            'scheme'                =>  $chartEntity->scheme(),
            'host'                  =>  $chartEntity->host(),
            'uri'                   =>  $chartEntity->uri(),
            'original_chart_name'   =>  $originalChartNameValue,
            'page_title'            =>  $chartEntity->pageTitle()
        ];
        return Chart::find($chartEntity->id()->value())->fill($parameters)->save();
    }

    public function rollback(EntityId $id, ChartSpecification $chartSpecification)
    {
        $rollbackTarget = $chartSpecification->rollback($id, $this);
        $result = Chart::destroy($rollbackTarget->id()->value());
        if ($result !== 1) {
            return false;
        }
        return $this->register($rollbackTarget, $chartSpecification);
    }

    protected function idExisting(EntityId $id) {
        $row = Chart::find($id->value());
        if (!empty($row)) {
            return true;
        }
        $row = ProvisionedChart::find($id->value());
        if (!empty($row)) {
            return true;
        }
        return false;
    }

}
