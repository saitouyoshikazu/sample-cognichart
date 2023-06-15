<?php

namespace App\Http\Controllers\WWW\Chart;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Expand\Validation\ExpValidation;
use App\Application\Chart\ChartApplicationInterface;
use App\Application\ChartTerm\ChartTermApplicationInterface;
use App\Application\DXO\ChartDXO;
use App\Application\DXO\ChartTermDXO;
use App\Domain\ValueObjects\Phase;
use App\Domain\ValueObjects\ChartTermDate;
use Config;
use View;

class ChartController extends Controller
{

    private $chartApplication;
    private $chartTermApplication;

    public function __construct(
        ChartApplicationInterface $chartApplication,
        ChartTermApplicationInterface $chartTermApplication
    ) {
        $this->chartApplication = $chartApplication;
        $this->chartTermApplication = $chartTermApplication;
    }

    public function chart(
        Request $request,
        string $chartNameValue = null,
        string $countryIdValue = null,
        string $endDateValue = null
    ) {
        $expValidator = new ExpValidation(['country_id', 'chart_name', 'end_date'], 'www_validation');
        $params = [
            'country_id' => $countryIdValue,
            'chart_name' => $chartNameValue,
            'end_date' => $endDateValue
        ];
        $validator = $expValidator->validateOnly($params);
        if ($validator->fails()) {
            return redirect()->back();
        }
        if (!empty($countryIdValue) || !empty($chartNameValue)) {
            if (empty($countryIdValue) || empty($chartNameValue)) {
                return redirect()->back();
            }
        } else {
            $countryIdValue = 'US';
            $chartNameValue = 'USA Singles Chart';
        }

        $metaDescription = "YouTube songs of singles chart. You can listen to musics without an account.";

        $chartDXO = new ChartDXO();
        $chartDXO->frontGet($countryIdValue, $chartNameValue);
        $chartAggregation = $this->chartApplication->frontGet($chartDXO);
        if (empty($chartAggregation)) {
            if (url()->current() == route('top')) {
                return view(
                    'www.statics.maintenance.maintenance',
                    [
                        'page_title'    =>  'maintenance',
                        'meta_description' => $metaDescription
                    ]
                );
            }
            return redirect()->back();
        }
        $metaDescription .= !empty($chartAggregation) ? " This is " . $chartAggregation->chartName()->value() . '.' : '';

        $chartDXO = new ChartDXO();
        $chartDXO->list(Phase::released);
        $chartList = $this->chartApplication->list($chartDXO);

        $chartTermList = $chartAggregation->chartTermList();
        if (!empty($chartTermList)) {
            $searchChartTermEndDate = null;
            if (!empty($endDateValue)) {
                $searchChartTermEndDate = new ChartTermDate($endDateValue);
            }
            $nearestChartTerm = $chartTermList->nearest($searchChartTermEndDate);
            $endDateValue = $nearestChartTerm->endDate()->value();
        }

        $chartTermAggregation = null;
        if (!empty($endDateValue)) {
            $chartTermDXO = new ChartTermDXO();
            $chartTermDXO->aggregation($chartAggregation->id()->value(), $endDateValue);
            $chartTermAggregation = $this->chartTermApplication->aggregation($chartTermDXO);
        }

        $pageTitle = $chartAggregation->pageTitle();
        $linkCanonical = route(
            'chart/get',
            [
                'chartNameValue' => $chartAggregation->chartName()->value(),
                'countryIdValue' => $chartAggregation->countryId()->value()
            ]
        );
        $chartBusinessId = $chartAggregation->businessId();
        $viewName = 'www.chart.chart';
        if (!empty($request->input('purpose')) && $request->input('purpose') === 'parts') {
            $chartListPart = View::make(
                'www.chart.chartlist',
                [
                    'end_date' => $endDateValue,
                    'chartList' => $chartList,
                    'chartAggregation' => $chartAggregation,
                    'chartTermAggregation' => $chartTermAggregation
                ]
            )->render();
            $chartTermListPart = View::make(
                'www.chart.charttermlist',
                [
                    'end_date' => $endDateValue,
                    'chartList' => $chartList,
                    'chartAggregation' => $chartAggregation,
                    'chartTermAggregation' => $chartTermAggregation
                ]
            )->render();
            $wwwBodyPart = View::make(
                'www.chart.chartdoc',
                [
                    'end_date' => $endDateValue,
                    'chartList' => $chartList,
                    'chartAggregation' => $chartAggregation,
                    'chartTermAggregation' => $chartTermAggregation
                ]
            )->render();
            return response()
                ->json([
                    'linkCanonicalPart' => $linkCanonical,
                    'titlePart' => $pageTitle,
                    'descriptionPart' => $metaDescription,
                    'chartListPart' => $chartListPart,
                    'chartTermListPart' => $chartTermListPart,
                    'wwwBodyPart' => $wwwBodyPart,
                ])
                ->header(
                    'Link',
                    "<{$linkCanonical}>;rel=\"canonical\""
                );
        }
        return response()
            ->view(
                'www.chart.chart',
                [
                    'link_canonical' => $linkCanonical,
                    'page_title' => $pageTitle,
                    'meta_description' => $metaDescription,
                    'end_date' => $endDateValue,
                    'chartList' => $chartList,
                    'chartAggregation' => $chartAggregation,
                    'chartTermAggregation' => $chartTermAggregation
                ]
            )->header(
                'Link',
                "<{$linkCanonical}>;rel=\"canonical\""
            );
    }

}
