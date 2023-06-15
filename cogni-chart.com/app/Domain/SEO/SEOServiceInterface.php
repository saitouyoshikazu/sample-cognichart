<?php

namespace App\Domain\SEO;
use App\Domain\Chart\ChartListRepositoryInterface;
use App\Infrastructure\SEO\SiteMapXmlInterface;

interface SEOServiceInterface
{

    public function __construct(
        ChartListRepositoryInterface $chartListRepository,
        SiteMapXmlInterface $siteMapXML
    );

    public function sitemapxml();

}
