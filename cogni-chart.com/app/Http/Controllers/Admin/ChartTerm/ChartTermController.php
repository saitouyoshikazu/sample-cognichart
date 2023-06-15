<?php

namespace App\Http\Controllers\Admin\ChartTerm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Application\Chart\ChartApplicationInterface;
use App\Application\ChartTerm\ChartTermApplicationInterface;
use App\Expand\Validation\ExpValidation;
use App\Application\DXO\ChartDXO;
use App\Application\DXO\ChartTermDXO;
use App\Domain\ValueObjects\Phase;

class ChartTermController extends Controller
{

    private $chartApplication;
    private $chartTermApplication;

    public  function __construct(
        ChartApplicationInterface $chartApplication,
        ChartTermApplicationInterface $chartTermApplication
    ) {
        $this->middleware('auth');
        $this->chartApplication = $chartApplication;
        $this->chartTermApplication = $chartTermApplication;
    }

    public function list(Request $request)
    {
        $chartDXO = new ChartDXO();
        $chartDXO->list(Phase::released);
        $releasedChartList = $this->chartApplication->list($chartDXO);

        $chartDXO = new ChartDXO();
        $chartDXO->list(Phase::provisioned);
        $provisionedChartList = $this->chartApplication->list($chartDXO);

        $selectedChartEntity = null;
        $chartPhase = "";
        if (!empty($request->input('chart_phase')) || !empty($request->input('country_id')) || !empty($request->input('chart_name'))) {
            $expValidator = new ExpValidation(['chart_phase', 'country_id', 'chart_name']);
            $expValidator->validateWithRedirect($request);
            $chartDXO = new ChartDXO();
            $chartDXO->get($request->input('chart_phase'), $request->input('country_id'), $request->input('chart_name'));
            $selectedChartEntity = $this->chartApplication->get($chartDXO);
            $chartPhase = $request->input('chart_phase');
        }

        $releasedChartTermList = null;
        $provisionedChartTermList = null;
        if (!empty($selectedChartEntity)) {
            $chartTermDXO = new ChartTermDXO();
            $chartTermDXO->list($selectedChartEntity->id()->value(), Phase::released);
            $releasedChartTermList = $this->chartTermApplication->list($chartTermDXO);

            $chartTermDXO = new ChartTermDXO();
            $chartTermDXO->list($selectedChartEntity->id()->value(), Phase::provisioned);
            $provisionedChartTermList = $this->chartTermApplication->list($chartTermDXO);
        }

        return view(
            'admin.chartterm.list',
            [
                'selectedChartEntity'       =>  $selectedChartEntity,
                'chartPhase'                =>  $chartPhase,
                'releasedChartList'         =>  $releasedChartList,
                'provisionedChartList'      =>  $provisionedChartList,
                'releasedChartTermList'     =>  $releasedChartTermList,
                'provisionedChartTermList'  =>  $provisionedChartTermList
            ]
        );
    }

    public function get(Request $request)
    {
        $expValidate = new ExpValidation(['chart_phase', 'country_id', 'chart_name', 'chartterm_phase', 'end_date']);
        $expValidate->validateWithRedirect($request);

        $chartDXO = new ChartDXO();
        $chartDXO->get($request->input('chart_phase'), $request->input('country_id'), $request->input('chart_name'));
        $chartPhase = $chartDXO->getPhase();
        $chartEntity = $this->chartApplication->get($chartDXO);
        if (empty($chartEntity)) {
            return redirect()->back()->withErrors(['application' => 'Chart doesn\'t exist.']);
        }

        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->masterAggregation($request->input('chartterm_phase'), $chartEntity->id()->value(), $request->input('end_date'));
        $chartTermPhase = $chartTermDXO->getPhase();
        $chartTermAggregation = $this->chartTermApplication->masterAggregation($chartTermDXO);
        $pageLocationId = empty($request->input('pageLocationId'))?'':$request->input('pageLocationId');
        return view(
            'admin.chartterm.chartterm',
            [
                'chartPhase'            =>  $chartPhase,
                'chartEntity'           =>  $chartEntity,
                'chartTermAggregation'  =>  $chartTermAggregation,
                'chartTermPhase'        =>  $chartTermPhase,
                'pageLocationId'        =>  $pageLocationId
            ]
        );
    }

    public function delete(Request $request)
    {
        $expValidator = new ExpValidation(['chartterm_id']);
        $expValidator->validateWithRedirect($request);

        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->delete($request->input('chartterm_id'));
        try {
            $result = $this->chartTermApplication->delete($chartTermDXO);
            if ($result === false) {
                return redirect()->back()->withErrors(['application' => 'Failed to delete ChartTerm.']);
            }
        } catch (ChartTermException $e) {
            return redirect()->back()->withErrors(['application' => $e->getMessage()]);
        }
        return redirect(
            route(
                'chartterm/list',
                [
                    'chart_phase'   =>  $request->input('chart_phase'),
                    'country_id'    =>  $request->input('country_id'),
                    'chart_name'    =>  $request->input('chart_name')
                ]
            )
        );
    }

    public function release(Request $request)
    {
        $expValidator = new ExpValidation(['chart_phase', 'country_id', 'chart_name', 'chartterm_id']);
        $expValidator->validateWithRedirect($request);

        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->release($request->input('chartterm_id'), $request->input('publish_released_message', false));
        try {
            $this->chartTermApplication->release($chartTermDXO);
        } catch (ChartTermException $e) {
            return redirect()->back()->withErrors(['application' => $e->getMessage()]);
        }
        return redirect(
            route(
                'chartterm/list',
                [
                    'chart_phase'   =>  $request->input('chart_phase'),
                    'country_id'    =>  $request->input('country_id'),
                    'chart_name'    =>  $request->input('chart_name')
                ]
            )
        );
    }

    public function rollback(Request $request)
    {
        $expValidator = new ExpValidation(['chart_phase', 'country_id', 'chart_name', 'chartterm_id']);
        $expValidator->validateWithRedirect($request);

        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->rollback($request->input('chartterm_id'));
        try {
            $this->chartTermApplication->rollback($chartTermDXO);
        } catch (ChartTermException $e) {
            return redirect()->back()->withErrors(['application' => $e->getMessage()]);
        }
        return redirect(
            route(
                'chartterm/list',
                [
                    'chart_phase'   =>  $request->input('chart_phase'),
                    'country_id'    =>  $request->input('country_id'),
                    'chart_name'    =>  $request->input('chart_name')
                ]
            )
        );
    }

    public function resolve(Request $request)
    {
        $expValidate = new ExpValidation(['chartterm_phase', 'chartterm_id']);
        $validator = $expValidate->validateOnly($request->all());
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $errorMessage = implode("\n", $errors);
            return ['message' => $errorMessage];
        }

        try {
            $chartTermDXO = new ChartTermDXO();
            $chartTermDXO->resolve($request->input('chartterm_phase'), $request->input('chartterm_id'));
            $result = $this->chartTermApplication->resolve($chartTermDXO);
            if ($result === false) {
                return ['message' => 'Failed to resolve ChartRankingItems of ChartTerm.'];
            }
        } catch (\Throwable $e) {
            return ['message' => $e->getMessage()."\n".$e->getTraceAsString()];
        }
        return ['message' => 'Correctly completed.'];
    }

}
