<?php

namespace App\Infrastructure\SEO;

class SiteMapXml implements SiteMapXmlInterface
{

    public function sitemapIndex(array $sitemapFiles)
    {
        $sitemap = \App::make("sitemap");

        foreach ($sitemapFiles AS $sitemapFile) {
            $file = $sitemapFile['file'];
            $lastModified = !empty($sitemapFile['updated_at']) ? $sitemapFile['updated_at'] : null;
            $sitemap->addSitemap($file, $lastModified);
        }

        $sitemap->store('sitemapindex', 'sitemap');
    }

    public function drawFile(string $fileName, array $sitemapList)
    {

        $sitemap = \App::make("sitemap");

        foreach ($sitemapList AS $key => $sitemaprow) {
            $sitemap->add(
                $sitemaprow['url'],
                $sitemaprow['updated_at']->format('Y-m-d H:i:s'),
                $sitemaprow['priority'],
                $sitemaprow['changefreq']
            );
        }

        $sitemap->store('xml', $fileName);
    }

}
