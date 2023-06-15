<?php

namespace App\Infrastructure\SEO;

interface SiteMapXmlInterface
{

    public function sitemapIndex(array $sitemapFiles);

    public function drawFile(string $fileName, array $sitemaplist);

}
