<?php

namespace App\Infrastructure\Repositories;
use App\Domain\AbstractChartTerm\AbstractChartTermRepositoryInterface;
use App\Infrastructure\Storage\AbstractChartTermStorageInterface;
use App\Domain\ChartTerm\ChartTermBusinessId;
use App\Domain\AbstractChartTerm\AbstractChartTerm;

class AbstractChartTermRepository implements AbstractChartTermRepositoryInterface
{

    private $abstractChartTermStorage;

    public function __construct(AbstractChartTermStorageInterface $abstractChartTermStorage)
    {
        $this->abstractChartTermStorage = $abstractChartTermStorage;
    }

    public function get(ChartTermBusinessId $businessId)
    {
        $fileName = $this->fileName($businessId);
        $serialized = $this->abstractChartTermStorage->get($fileName);
        if (empty($serialized)) {
            return null;
        }
        return unserialize($serialized);
    }

    public function register(AbstractChartTerm $abstractChartTerm)
    {
        $fileName = $this->fileName($abstractChartTerm->businessId());
        $serialized = serialize($abstractChartTerm);
        return $this->abstractChartTermStorage->put($fileName, $serialized);
    }

    public function delete(ChartTermBusinessId $businessId)
    {
        $fileName = $this->fileName($businessId);
        return $this->abstractChartTermStorage->delete($fileName);
    }

    public function exists(ChartTermBusinessId $businessId)
    {
        $fileName = $this->fileName($businessId);
        return $this->abstractChartTermStorage->exists($fileName);
    }

    public function latest(ChartTermBusinessId $businessId)
    {
        $abstractChartTermFileNames = $this->abstractChartTermStorage->files();
        if (empty($abstractChartTermFileNames)) {
            return null;
        }
        $matched = [];
        foreach ($abstractChartTermFileNames AS $abstractChartTermFileName) {
            $regex = "/^{$businessId->chartId()->value()}-/";
            if (preg_match($regex, $abstractChartTermFileName)) {
                $matched[] = $abstractChartTermFileName;
            }
        }
        if (empty($matched)) {
            return null;
        }
        rsort($matched);
        $fileName = $matched[0];
        $serialized = $this->abstractChartTermStorage->get($fileName);
        if (empty($serialized)) {
            return null;
        }
        return unserialize($serialized);
    }

    private function fileName(ChartTermBusinessId $businessId)
    {
        return $businessId->value() . ".log";
    }

}
