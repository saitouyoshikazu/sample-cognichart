<?php

namespace App\Domain\Chart;
use App\Infrastructure\RedisDAO\RedisDAOInterface;
use App\Domain\ValueObjects\Phase;

interface ChartListRepositoryInterface
{

    /**
     * Constructor.
     * @param   RedisDAOInterface       $redisDAO       RedisDAO.
     * @param   ChartFactoryInterface   $chartFactory   ChartFactory.
     */
    public function __construct(RedisDAOInterface $redisDAO, ChartFactoryInterface $chartFactory);

    /**
     * Get ChartList depending on $phase.
     * @param   Phase                   $phase                      Phase.
     * @param   ChartListSpecification  $chartListSpecification     ChartListSpecification.
     * @return  ChartList   When all of chart correctly searched.
     *          null        When there is no chart or when failed to search chart.
     */
    public function chartList(Phase $phase, ChartListSpecification $chartListSpecification);

    /**
     * Get released ChartList from cache and storage.
     * @param   ChartListSpecification  $chartListSpecification     ChartListSpecification.
     * @return  ChartList   When all of chart correctly searched.
     *          null        When there is no chart or when failed to search chart.
     */
    public function chartListWithCache(ChartListSpecification $chartListSpecification);

    /**
     * Refresh cached ChartList.
     * @param   ChartListSpecification  $chartListSpecification     ChartListSpecification.
     */
    public function refreshCachedChartList(ChartListSpecification $chartListSpecification);

    /**
     * Get ChartList that is released.
     * @return  ChartList   When all of chart correctly searched.
     *          null        When there is no chart or when failed to search chart.
     */
    function releasedChartList();

    /**
     * Get ChartList that is provisioned.
     * @return  ChartList   When all of chart correctly searched.
     *          null        When there is no chart or when failed to search chart.
     */
    function provisionedChartList();

    /**
     * Store ChartList to cache.
     * @param   ChartList   $chartList  ChartList will be cached.
     * @return  true    When ChartList was correctly cached.
     *          false   When failed to store to cache.
     */
    function storeCacheChartList(ChartList $chartList);

    /**
     * Get ChartList from cache.
     * @return  ChartList   When ChartList is cached.
     *          null        When ChartList isn't cached.
     */
    function cachedChartList();

    /**
     * Delete ChartList from cache.
     * @return  int     The count of deleted ChartList.
     */
    function deleteCachedChartList();

}
