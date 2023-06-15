<?php

namespace App\Application\Chart;
use App\Domain\Chart\ChartRepositoryInterface;
use App\Domain\Chart\ChartFactoryInterface;
use App\Domain\Chart\ChartListRepositoryInterface;
use DB;
use Event;
use App\Application\DXO\ChartDXO;
use App\Domain\Chart\ChartSpecification;
use App\Domain\Chart\ChartListSpecification;
use App\Events\ChartReleased;
use App\Events\ChartModified;
use App\Events\ChartRollbacked;

class ChartApplication implements ChartApplicationInterface
{

    private $chartRepository;
    private $chartFactory;
    private $chartListRepository;

    public function __construct(
        ChartRepositoryInterface $chartRepository,
        ChartFactoryInterface $chartFactory,
        ChartListRepositoryInterface $chartListRepository
    ) {
        $this->chartRepository = $chartRepository;
        $this->chartFactory = $chartFactory;
        $this->chartListRepository = $chartListRepository;
    }

    public function list(ChartDXO $chartDXO)
    {
        $phase = $chartDXO->getPhase();
        if (empty($phase)) {
            return null;
        }
        return $this->chartListRepository->chartList($phase, new ChartListSpecification());
    }

    public function register(ChartDXO $chartDXO)
    {
        $countryId = $chartDXO->getCountryId();
        $chartName = $chartDXO->getChartName();
        $scheme = $chartDXO->getScheme();
        $host = $chartDXO->getHost();
        $originalChartName = $chartDXO->getOriginalChartName();
        $pageTitle = $chartDXO->getPageTitle();
        if (empty($countryId) || empty($chartName) || empty($scheme) || empty($host)) {
            return false;
        }
        $uri = $chartDXO->getUri();

        $originalChartNameValue = null;
        if (!empty($originalChartName)) {
            $originalChartNameValue = $originalChartName->value();
        }
        $chartEntity = $this->chartFactory->create(
            $this->chartRepository->createId()->value(),
            $countryId->value(),
            $chartName->value(),
            $scheme,
            $host,
            $uri,
            $originalChartNameValue,
            $pageTitle
        );
        if (empty($chartEntity)) {
            return false;
        }

        DB::beginTransaction();
        try {
            $result = $this->chartRepository->register($chartEntity, new ChartSpecification());
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

    public function get(ChartDXO $chartDXO)
    {
        $phase = $chartDXO->getPhase();
        $chartBusinessId = $chartDXO->getBusinessId();
        if (empty($phase) || empty($chartBusinessId)) {
            return null;
        }
        if ($phase->isReleased()) {
            return $this->chartRepository->getRelease($chartBusinessId);
        } else if ($phase->isProvisioned()) {
            return $this->chartRepository->getProvision($chartBusinessId);
        }
        return null;
    }

    public function modify(ChartDXO $chartDXO)
    {
        $phase = $chartDXO->getPhase();
        $entityId = $chartDXO->getEntityId();
        $countryId = $chartDXO->getCountryId();
        $chartName = $chartDXO->getChartName();
        $scheme = $chartDXO->getScheme();
        $host = $chartDXO->getHost();
        $originalChartName = $chartDXO->getOriginalChartName();
        $pageTitle = $chartDXO->getPageTitle();
        if (empty($phase) || empty($entityId) || empty($countryId) || empty($chartName) || empty($scheme) || empty($host)) {
            return false;
        }
        $uri = $chartDXO->getUri();

        $chartEntity = null;
        if ($phase->isReleased()) {
            $chartEntity = $this->chartRepository->findRelease($entityId);
        } else if ($phase->isProvisioned()) {
            $chartEntity = $this->chartRepository->findProvision($entityId);
        }
        if (empty($chartEntity)) {
            return false;
        }

        $oldCountryId = $chartEntity->countryId();
        $oldChartName = $chartEntity->chartName();
        $chartEntity
            ->setCountryId($countryId)
            ->setChartName($chartName)
            ->setScheme($scheme)
            ->setHost($host)
            ->setUri($uri)
            ->setOriginalChartName($originalChartName)
            ->setPageTitle($pageTitle);
        DB::beginTransaction();
        try {
            $result = false;
            if ($phase->isReleased()) {
                $result = $this->chartRepository->modifyRelease($chartEntity, new ChartSpecification());
            } else if ($phase->isProvisioned()) {
                $result = $this->chartRepository->modifyProvision($chartEntity, new ChartSpecification());
            }
            if ($result === false) {
                DB::rollback();
                return false;
            }
        } catch(\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();

        if ($phase->isReleased()) {
            Event::dispatch(
                new ChartModified(
                    $chartEntity->id()->value(),
                    $oldCountryId->value(),
                    $oldChartName->value()
                )
            );
        }
        return true;
    }

    public function release(ChartDXO $chartDXO)
    {
        $entityId = $chartDXO->getEntityId();
        if (empty($entityId)) {
            return false;
        }

        $releasedEntity = null;
        DB::beginTransaction();
        try {
            $result = $this->chartRepository->release($entityId, new ChartSpecification());
            if ($result === false) {
                DB::rollback();
                return false;
            }
            $releasedEntity = $this->chartRepository->findRelease($entityId);
            if (empty($releasedEntity)) {
                DB::rollback();
                return false;
            }
        } catch(\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();

        Event::dispatch(
            new ChartReleased(
                $releasedEntity->id()->value(),
                $releasedEntity->countryId()->value(),
                $releasedEntity->chartName()->value()
            )
        );
        return true;
    }

    public function rollback(ChartDXO $chartDXO)
    {
        $entityId = $chartDXO->getEntityId();
        if (empty($entityId)) {
            return false;
        }

        $rollbackedEntity = null;
        DB::beginTransaction();
        try {
            $result = $this->chartRepository->rollback($entityId, new ChartSpecification());
            if ($result === false) {
                DB::rollback();
                return false;
            }
            $rollbackedEntity = $this->chartRepository->findProvision($entityId);
            if (empty($rollbackedEntity)) {
                return false;
            }
        } catch(\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();
        Event::dispatch(
            new ChartRollbacked(
                $rollbackedEntity->id()->value(),
                $rollbackedEntity->countryId()->value(),
                $rollbackedEntity->chartName()->value()
            )
        );
        return true;
    }

    public function delete(ChartDXO $chartDXO)
    {
        $entityId = $chartDXO->getEntityId();
        if (empty($entityId)) {
            return false;
        }

        DB::beginTransaction();
        try {
            $result = $this->chartRepository->delete($entityId, new ChartSpecification());
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

    public function refreshCachedChartList()
    {
        $this->chartListRepository->refreshCachedChartList(new ChartListSpecification());
        return true;
    }

    public function refreshCachedAggregation(ChartDXO $chartDXO)
    {
        $entityId = $chartDXO->getEntityId();
        $chartBusinessId = $chartDXO->getBusinessId();
        if (empty($entityId) || empty($chartBusinessId)) {
            return false;
        }
        $this->chartRepository->refreshCachedAggregation($entityId, $chartBusinessId, new ChartSpecification());
        return true;
    }

    public function frontGet(ChartDXO $chartDXO)
    {
        $chartBusinessId = $chartDXO->getBusinessId();
        if (empty($chartBusinessId)) {
            return null;
        }
        return $this->chartRepository->getAggregationWithCache($chartBusinessId, new ChartSpecification());
    }

}
