<?php

namespace App\Domain\AbstractChartTerm;
use App\Infrastructure\Storage\AbstractChartTermStorageInterface;
use App\Domain\ChartTerm\ChartTermBusinessId;

interface AbstractChartTermRepositoryInterface
{

    /**
     * Constructor.
     */
    public function __construct(AbstractChartTermStorageInterface $abstractChartTermStorage);

    /**
     * Get AbstractChartTerm from storage.
     */
    public function get(ChartTermBusinessId $businessId);

    /**
     * Register AbstractChartTerm to storage.
     */
    public function register(AbstractChartTerm $abstractChartTerm);

    /**
     * Delete AbstractChartTerm from storage.
     */
    public function delete(ChartTermBusinessId $businessId);

    /**
     * Check if AbstractChartTerm is existing.
     */
    public function exists(ChartTermBusinessId $businessId);

    /**
     * Get latest AbstractChartTerm from storage.
     */
    public function latest(ChartTermBusinessId $businessId);

}
