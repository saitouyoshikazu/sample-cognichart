<?php

namespace App\Infrastructure\Repositories;
use App\Domain\Chart\ChartListRepositoryInterface;
use App\Infrastructure\RedisDAO\RedisDAOInterface;
use App\Domain\Chart\ChartFactoryInterface;
use App\Domain\ValueObjects\Phase;
use App\Domain\Chart\ChartListSpecification;
use App\Domain\Chart\ChartList;
use App\Infrastructure\Eloquents\Chart;
use App\Infrastructure\Eloquents\ProvisionedChart;

class ChartListRepository implements ChartListRepositoryInterface
{

    private $redisDAO;
    private $chartFactory;

    public function __construct(RedisDAOInterface $redisDAO, ChartFactoryInterface $chartFactory)
    {
        $this->redisDAO = $redisDAO;
        $this->chartFactory = $chartFactory;
    }

    public function chartList(Phase $phase, ChartListSpecification $chartListSpecification)
    {
        return $chartListSpecification->chartList($phase, $this);
    }

    public function chartListWithCache(ChartListSpecification $chartListSpecification)
    {
        return $chartListSpecification->chartListWithCache($this);
    }

    public function refreshCachedChartList(ChartListSpecification $chartListSpecification)
    {
        $chartListSpecification->refreshCachedChartList($this);
    }

    function releasedChartList()
    {
        $rows = Chart::orderBy('display_position', 'asc')->get();
        if (empty($rows)) {
            return null;
        }
        $chartList = null;
        foreach ($rows AS $row) {
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
            if (!empty($chartEntity)) {
                if (empty($chartList)) {
                    $chartList = new ChartList(new Phase(Phase::released));
                }
                $chartList->append($chartEntity);
            }
        }
        return $chartList;
    }

    function provisionedChartList()
    {
        $rows = ProvisionedChart::orderBy('display_position', 'asc')->get();
        if (empty($rows)) {
            return null;
        }
        $chartList = null;
        foreach ($rows AS $row) {
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
            if (!empty($chartEntity)) {
                if (empty($chartList)) {
                    $chartList = new ChartList(new Phase(Phase::provisioned));
                }
                $chartList->append($chartEntity);
            }
        }
        return $chartList;
    }

    function storeCacheChartList(ChartList $chartList)
    {
        return $this->redisDAO->set(
            $this->chartListCacheKey(),
            serialize($chartList)
        );
    }

    function cachedChartList()
    {
        $cache = $this->redisDAO->get($this->chartListCacheKey());
        if (empty($cache)) {
            return null;
        }
        return unserialize($cache);
    }

    function deleteCachedChartList()
    {
        return $this->redisDAO->del($this->chartListCacheKey());
    }

    private function chartListCacheKey()
    {
        return ChartList::class;
    }

}
