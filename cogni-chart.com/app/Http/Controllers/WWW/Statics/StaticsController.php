<?php

namespace App\Http\Controllers\WWW\Statics;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Application\Chart\ChartApplicationInterface;
use App\Application\ChartTerm\ChartTermApplicationInterface;
use App\Application\DXO\ChartDXO;
use App\Application\DXO\ChartTermDXO;
use App\Domain\ValueObjects\Phase;

class StaticsController extends Controller
{

    private $chartApplication;

    public function __construct(
        ChartApplicationInterface $chartApplication
    ) {
        $this->chartApplication = $chartApplication;
    }

    public function howtouse()
    {
        return view(
            'www.statics.howtouse.howtouse',
            $this->getViewParameters('How to Use')
        );
    }

    public function privacypolicy()
    {
        return view(
            'www.statics.privacypolicy.privacypolicy',
            $this->getViewParameters('Privacy Policy')
        );
    }

    public function termsofuse()
    {
        return view(
            'www.statics.termsofuse.termsofuse',
            $this->getViewParameters('Terms of Use')
        );
    }

    public function contactus()
    {
        return view(
            'www.statics.contactus.contactus',
            $this->getViewParameters('Contact Us')
        );
    }

    public function mailsent()
    {
        return view(
            'www.statics.contactus.mailsent',
            $this->getViewParameters('Mail was sent')
        );
    }

    private function getViewParameters(string $pageTitle)
    {
        $countryIdValue = 'US';
        $chartNameValue = 'USA Singles Chart';

        $chartDXO = new ChartDXO();
        $chartDXO->frontGet($countryIdValue, $chartNameValue);
        $chartAggregation = $this->chartApplication->frontGet($chartDXO);
        if (empty($chartAggregation)) {
            return [];
        }
        $chartDXO = new ChartDXO();
        $chartDXO->list(Phase::released);
        $chartList = $this->chartApplication->list($chartDXO);

        return [
            'meta_description'  =>  'YouTube songs of singles chart. You can listen to musics without an account.',
            'page_title' => $pageTitle,
            'chartList' => $chartList,
            'chartAggregation' => $chartAggregation,
            'isNoIndex' => true,
        ];
    }

}
