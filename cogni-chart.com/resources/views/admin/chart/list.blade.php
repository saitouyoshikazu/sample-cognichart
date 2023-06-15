@extends('templates.admin', ['adminMenu' => 'Chart'])

@section('content')
<section class="card w-100">
    <nav class="card-heading navbar navbar-expand navbar-light bg-light flex-column">
        <div class="navbar-nav w-100 justify-content-around">
            <div class="navbar-brand">
                <strong>
                    {{ $chart_phase }} Chart
                </strong>
            </div>
        </div>
        <div class="navbar-nav w-100 justify-content-around">
            <form action="javascript:void(0);" class="form-inline" method="get" id="chartListForm">
                <?php
                    use App\Domain\ValueObjects\Phase;
                    $changePhase = Phase::released;
                    if ($chart_phase === Phase::released) {
                        $changePhase = Phase::provisioned;
                    }
                ?>
                <button type="submit" class="btn btn-secondary" onclick="$('#chartListForm').attr('action', '{{ route('chart/list', ['chart_phase' => $changePhase]) }}');">
                    Go {{ $changePhase }}
                </button>
            </form>

            @if ($chart_phase === Phase::provisioned)
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalChartRegister">
                <i class="fas fa-plus"></i>&nbsp;Register
            </button>
            @endif
        </div>
    </nav>
    <article class="card-body">
        @if (!empty($chartList))
        <table class="table">
            <thead>
                <tr>
                    <th>label</th>
                    <th>original chart name</th>
                    <th>page title</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($chartList AS $chartEntity)
                <tr class="mouseOverFocus chartRow" data-chart_phase="{{ $chart_phase }}" data-country_id="{{ $chartEntity->countryId()->value() }}" data-chart_name="{{ $chartEntity->chartName()->value() }}">
                    <td>
                        {{ $chartEntity->label() }}
                    </td>
                    <td>
                        {{ !empty($chartEntity->originalChartName()) ? $chartEntity->originalChartName()->value() : '' }}
                    </td>
                    <td>
                        {{ $chartEntity->pageTitle() }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </article>
</section>

@if ($chart_phase === Phase::provisioned)
<div id="modalChartRegister" class="modal fade" tabIndex="-1" role="dialog" aria-labelledby="modalChartRegisterLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalChartRegisterLabel">Register Chart</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('chart/register') }}" method="post">
                    {{ csrf_field() }}
                    <div class="form-group row">
                        <label for="modal_country_id" class="col-3 control-label">{{ __('country_id') }}</label>
                        <select name="country_id" id="modal_country_id" class="col-9 form-control">
                            @if (!empty($countryEntities))
                            @foreach ($countryEntities AS $countryEntity)
                            <option value="{{ $countryEntity->id()->value() }}" {{ ($countryEntity->id()->value() === old('country_id')) ? 'selected="selected"' : '' }}>
                                {{ $countryEntity->getCountryName() }}
                            </option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group row">
                        <label for="modal_chart_name" class="col-3 control-label">{{ __('chart_name') }}</label>
                        <input type="text" name="chart_name" id="modal_chart_name" value="{{ old('chart_name') }}" class="col-9 form-control">
                    </div>
                    <div class="form-group row">
                        <label for="modal_scheme" class="col-3 control-label">{{ __('scheme') }}</label>
                        <input type="text" name="scheme" id="modal_scheme" value="{{ old('scheme') }}" class="col-9 form-control">
                    </div>
                    <div class="form-group row">
                        <label for="modal_host" class="col-3 control-label">{{ __('host') }}</label>
                        <input type="text" name="host" id="modal_host" value="{{ old('host') }}" class="col-9 form-control">
                    </div>
                    <div class="form-group row">
                        <label for="modal_uri" class="col-3 control-label">{{ __('uri') }}</label>
                        <input type="text" name="uri" id="modal_uri" value="{{ old('uri') }}" class="col-9 form-control">
                    </div>
                    <div class="form-group row">
                        <label for="modal_original_chart_name" class="col-3 control-label">{{ __('original_chart_name') }}</label>
                        <input type="text" name="original_chart_name" id="modal_original_chart_name" value="{{ old('original_chart_name') }}" class="col-9 form-control">
                    </div>
                    <div class="form-group row">
                        <label for="modal_page_title" class="col-3 control-label">{{ __('page_title') }}</label>
                        <input type="text" name="page_title" id="modal_page_title" value="{{ old('page_title') }}" class="col-9 form-control">
                    </div>
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-plus"></i>Register
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
