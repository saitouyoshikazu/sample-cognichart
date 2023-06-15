<?php

namespace App\Http\Controllers\Admin\Chart;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Application\Chart\ChartApplicationInterface;
use App\Domain\Country\CountryRepositoryInterface;
use App\Expand\Validation\ExpValidation;
use App\Application\DXO\ChartDXO;
use App\Domain\ValueObjects\Phase;
use App\Domain\Chart\ChartException;

class ChartController extends Controller
{

    private $chartApplication;
    private $countryRepository;

    public function __construct(ChartApplicationInterface $chartApplication, CountryRepositoryInterface $countryRepository)
    {
        $this->middleware('auth');
        $this->chartApplication = $chartApplication;
        $this->countryRepository = $countryRepository;
    }

    public function list(Request $request, string $chart_phase)
    {
        $expValidator = new ExpValidation(['chart_phase']);
        $request->merge(['chart_phase' => $chart_phase]);
        $expValidator->validateWithRedirect($request);

        $chart_phase = trim($chart_phase);
        if (empty($chart_phase)) {
            $chart_phase = Phase::released;
        }
        $chartDXO = new ChartDXO();
        $chartDXO->list($chart_phase);
        $chartList = $this->chartApplication->list($chartDXO);

        $countryEntities = $this->countryRepository->list();

        return view(
            'admin.chart.list',
            [
                'chart_phase' => $chart_phase,
                'chartList' => $chartList,
                'countryEntities' => $countryEntities
            ]
        );
    }

    public function register(Request $request)
    {
        $expValidator = new ExpValidation(['country_id', 'chart_name', 'scheme', 'host', 'uri', 'original_chart_name', 'page_title']);
        $expValidator->validateWithRedirect($request);

        $chartDXO = new ChartDXO();
        $pageTitle = "";
        if (!empty($request->input('page_title'))) {
            $pageTitle = $request->input('page_title');
        }
        $chartDXO->register(
            $request->input('country_id'),
            $request->input('chart_name'),
            $request->input('scheme'),
            $request->input('host'),
            $request->input('uri'),
            $request->input('original_chart_name'),
            $pageTitle
        );
        try {
            $result = $this->chartApplication->register($chartDXO);
            if ($result === false) {
                return redirect()->back()->withInput()->withErrors(['application' => 'Failed to register Chart.']);
            }
        } catch(ChartException $e) {
            return redirect()->back()->withInput()->withErrors(['application' => $e->getMessage()]);
        }
        return redirect()->back();
    }

    public function get(Request $request, string $chart_phase)
    {
        $expValidator = new ExpValidation(['chart_phase', 'country_id', 'chart_name']);
        $request->merge(['chart_phase' => $chart_phase]);
        $expValidator->validateWithRedirect($request);

        $chartDXO = new ChartDXO();
        $chartDXO->get($chart_phase, $request->input('country_id'), $request->input('chart_name'));
        $chartEntity = $this->chartApplication->get($chartDXO);
        if (empty($chartEntity)) {
            return redirect()->back()->withErrors(['application' => 'Didn\'t find Chart.']);
        }

        $countryEntities = $this->countryRepository->list();

        $viewName = 'admin.chart.provisionedChart';
        if ($chart_phase === Phase::released) {
            $viewName = 'admin.chart.releasedChart';
        }
        return view(
            $viewName,
            [
                'chart_phase' => $chart_phase,
                'chartEntity' => $chartEntity,
                'countryEntities' => $countryEntities
            ]
        );
    }

    public function modify(Request $request, string $chart_phase)
    {
        $expValidator = new ExpValidation(['chart_phase', 'chart_id', 'country_id', 'chart_name', 'scheme', 'host', 'uri', 'original_chart_name', 'page_title']);
        $request->merge(['chart_phase' => $chart_phase]);
        $expValidator->validateWithRedirect($request);

        $chartDXO = new ChartDXO();
        $pageTitle = "";
        if (!empty($request->input('page_title'))) {
            $pageTitle = $request->input('page_title');
        }
        $chartDXO->modify(
            $chart_phase,
            $request->input('chart_id'),
            $request->input('country_id'),
            $request->input('chart_name'),
            $request->input('scheme'),
            $request->input('host'),
            $request->input('uri'),
            $request->input('original_chart_name'),
            $pageTitle
        );
        try {
            $result = $this->chartApplication->modify($chartDXO);
            if ($result === false) {
                return redirect()->back()->withInput()->withErrors(['application' => 'Failed to modify Chart.']);
            }
        } catch(ChartException $e) {
            return redirect()->back()->withInput()->withErrors(['application' => $e->getMessage()]);
        }
        return redirect(route('chart/list', ['chart_phase' => $chart_phase]));
    }

    public function release(Request $request)
    {
        $expValidator = new ExpValidation(['chart_id']);
        $expValidator->validateWithRedirect($request);

        $chartDXO = new ChartDXO();
        $chartDXO->release($request->input('chart_id'));
        try {
            $result = $this->chartApplication->release($chartDXO);
            if ($result === false) {
                return redirect()->back()->withInput()->withErrors(['application' => 'Failed to release Chart.']);
            }
        } catch(ChartException $e) {
            return redirect()->back()->withInput()->withErrors(['application' => $e->getMessage()]);
        }
        return redirect(route('chart/list', ['chart_phase' => Phase::released]));
    }

    public function rollback(Request $request)
    {
        $expValidator = new ExpValidation(['chart_id']);
        $expValidator->validateWithRedirect($request);

        $chartDXO = new ChartDXO();
        $chartDXO->rollback($request->input('chart_id'));
        try {
            $result = $this->chartApplication->rollback($chartDXO);
            if ($result === false) {
                return redirect()->back()->withInput()->withErrors(['application' => 'Failed to rollback Chart.']);
            }
        } catch(ChartException $e) {
            return redirect()->back()->withInput()->withErrors(['application' => $e->getMessage()]);
        }
        return redirect(route('chart/list', ['chart_phase' => Phase::provisioned]));
    }

    public function delete(Request $request)
    {
        $expValidator = new ExpValidation(['chart_id']);
        $expValidator->validateWithRedirect($request);

        $chartDXO = new ChartDXO();
        $chartDXO->delete($request->input('chart_id'));
        try {
            $result = $this->chartApplication->delete($chartDXO);
            if ($result === false) {
                return redirect()->back()->withInput()->withErrors(['application' => 'Failed to delete Chart.']);
            }
        } catch(ChartException $e) {
            return redirect()->back()->withInput()->withErrors(['application' => $e->getMessage()]);
        }
        return redirect(route('chart/list', ['chart_phase' => Phase::provisioned]));
    }

}
