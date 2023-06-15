<?php

namespace App\Http\Controllers\WWW\Top;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Application\Chart\ChartApplicationInterface;
use App\Application\DXO\ChartDXO;
use App\Domain\ValueObjects\Phase;
use Config;

class TopController extends Controller
{

    private $chartApplication;

    public function __construct(
        ChartApplicationInterface $chartApplication
    ) {
        $this->chartApplication = $chartApplication;
    }

    public function top(Request $request)
    {
        $chartDXO = new ChartDXO();
        $chartDXO->list(Phase::released);
        $chartList = $this->chartApplication->list($chartDXO);
        $linkCanonical = route('top')."/";
        return response()
            ->view(
                "www.top.top",
                [
                    "link_canonical" => $linkCanonical,
                    "chartList" =>  $chartList
                ]
            )->header(
                'Link',
                "<{$linkCanonical}>;rel=\"canonical\""
            );
    }

}
