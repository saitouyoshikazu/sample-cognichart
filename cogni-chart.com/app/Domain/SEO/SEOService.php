<?php

namespace App\Domain\SEO;
use Config;
use Carbon\Carbon;
use App\Domain\Chart\ChartListRepositoryInterface;
use App\Domain\ValueObjects\Phase;
use App\Domain\Chart\ChartListSpecification;
use App\Infrastructure\Eloquents\Chart;
use App\Infrastructure\Eloquents\ChartTerm;
use App\Infrastructure\SEO\SiteMapXmlInterface;

class SEOService implements SEOServiceInterface
{

    private $chartListRepository;
    private $siteMapXML;

    public function __construct(
        ChartListRepositoryInterface $chartListRepository,
        SiteMapXmlInterface $siteMapXML
    ) {
        $this->chartListRepository = $chartListRepository;
        $this->siteMapXML = $siteMapXML;
    }

    public function sitemapxml()
    {
        $topCountryId = "US";
        $topChartName = "USA Singles Chart";

        $chartList = $this->chartListRepository->chartList(new Phase(Phase::released), new ChartListSpecification());
        if (empty($chartList) || $chartList->chartCount() == 0) {
            return;
        }

        $lastModified = Config::get('app.top_page_last_modified');
        $sitemapList[] = [
            'url' => route("top")."/",
            'updated_at' => new Carbon($lastModified),
            'priority' => Config::get('app.top_page_priority'),
            'changefreq' => 'weekly'
        ];

        foreach ($chartList->chartEntities() AS $chartEntity) {
            $remain = 50000;
            $settings = [
                'url'   =>  route(
                    "chart/get",
                    [
                        'chartNameValue' => $chartEntity->chartName()->value(),
                        'countryIdValue' => $chartEntity->countryId()->value()
                    ]
                )
            ];

            $chartBusinessId = $chartEntity->businessId();
            if ($chartBusinessId->countryId()->value() == $topCountryId &&
                $chartBusinessId->chartName()->value() == $topChartName
            ) {
                $settings['priority'] = '1.0';
            } else {
                $settings['priority'] = '0.9';
            }

            $chart = Chart::find($chartEntity->id()->value());
            $chartTerms = ChartTerm::where('chart_id', $chartEntity->id()->value())->orderBy('end_date', 'desc')->get();
            if ($chartTerms->count() == 0) {
                $settings['updated_at'] = $chart->updated_at;
                continue;
            } else {
                $settings['updated_at'] = $chartTerms->first()->updated_at;
            }
            $settings['changefreq'] = 'weekly';
            $sitemapList[] = $settings;
            --$remain;
        }
        $this->siteMapXML->drawFile("sitemap", $sitemapList);
    }

    public function exploadSitemapXml()
    {
        $topCountryId = "US";
        $topChartName = "USA Singles Chart";

        $chartList = $this->chartListRepository->chartList(new Phase(Phase::released), new ChartListSpecification());
        if (empty($chartList) || $chartList->chartCount() == 0) {
            return;
        }

        $fileList = [];

        $lastModified = Config::get('app.top_page_last_modified');
        $sitemapList[] = [
            'url' => route("top")."/",
            'updated_at' => new Carbon($lastModified),
            'priority' => Config::get('app.top_page_priority'),
            'changefreq' => 'weekly'
        ];
        $fileName = 'Top';
        $this->siteMapXML->drawFile($fileName, $sitemapList);
        $fileList[] = [
            'file'  =>  route("top")."/".$fileName.".xml",
            'updated_at' => new Carbon($lastModified)
        ];

        foreach ($chartList->chartEntities() AS $chartEntity) {
            $sitemapList = [];
            $remain = 50000;
            $fileName = preg_replace("/ /", "", $chartEntity->label());
            $settings = [
                'url'   =>  route(
                    "chart/get",
                    [
                        'chartNameValue' => $chartEntity->chartName()->value(),
                        'countryIdValue' => $chartEntity->countryId()->value()
                    ]
                )
            ];

            $chartBusinessId = $chartEntity->businessId();
            if ($chartBusinessId->countryId()->value() == $topCountryId &&
                $chartBusinessId->chartName()->value() == $topChartName
            ) {
                $settings['priority'] = '1.0';
            } else {
                $settings['priority'] = '0.9';
            }

            $chart = Chart::find($chartEntity->id()->value());
            $chartTerms = ChartTerm::where('chart_id', $chartEntity->id()->value())->orderBy('end_date', 'desc')->get();
            if ($chartTerms->count() == 0) {
                $settings['updated_at'] = $chart->updated_at;
                continue;
            } else {
                $settings['updated_at'] = $chartTerms->first()->updated_at;
            }
            $settings['changefreq'] = 'weekly';
            $sitemapList[] = $settings;
            --$remain;

/*
            foreach ($chartTerms AS $chartTerm) {
                $settings = [
                    'url' => route(
                        "chart/get",
                        [
                            'chartNameValue' => $chartEntity->chartName()->value(),
                            'countryIdValue' => $chartEntity->countryId()->value(),
                            'endDateValue' => $chartTerm->end_date
                        ]
                    ),
                    'priority' => '0.9',
                    'updated_at' => $chartTerm->updated_at,
                    'changefreq' => 'weekly'
                ];
                $sitemapList[] = $settings;
                --$remain;
                if ($remain === 0) {
                    break;
                }
            }
 */
            $this->siteMapXML->drawFile($fileName, $sitemapList);
            $fileList[] = [
                'file' => route('top')."/".$fileName.".xml",
                'updated_at' => $chartTerms->first()->updated_at
            ];
        }
        $this->siteMapXML->sitemapIndex($fileList);
    }

}
