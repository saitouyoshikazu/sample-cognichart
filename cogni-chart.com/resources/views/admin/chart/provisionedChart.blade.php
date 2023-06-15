@extends('templates.admin', ['adminMenu' => 'Chart'])

@section('content')
<section class="card w-100">
    <nav class="card-heading navbar navbar-expand navbar-light bg-light flex-column">
        <div class="navbar-nav w-100 justify-content-around">
            <div class="navbar-brand">
                <strong>
                    {{ $chart_phase }} Chart : {{ $chartEntity->label() }}
                </strong>
            </div>
        </div>
        <div class="navbar-nav w-100 justify-content-around">
            <form action="{{ route('chart/list', ['chart_phase' => 'released']) }}" class="form-inline" method="get">
                <button type="submit" class="btn btn-secondary">Go released</button>
            </form>
            <form action="{{ route('chart/list', ['chart_phase' => 'provisioned']) }}" class="form-inline" method="get">
                <button type="submit" class="btn btn-secondary">Go provisioned</button>
            </form>
            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modalChartModify">
                <i class="fas fa-edit"></i>&nbsp;Modfiy
            </button>
            <form action="javascript:void(0);" class="form-inline" method="post" id="chartDeleteForm">
                {{ csrf_field() }}
                {{ method_field('delete') }}
                <input type="hidden" name="chart_id" value="{{ $chartEntity->id()->value() }}">
                <button type="submit" class="btn btn-danger" onclick="var ok = confirm('You are about to delete {{ $chartEntity->label() }}.\nAre you sure?'); if (ok) $('#chartDeleteForm').attr('action', '{{ route('chart/delete') }}');">
                    <i class="fas fa-trash-alt"></i>&nbsp;Delete
                </button>
            </form>
        </div>
    </nav>
    <article class="card-body">
        <form action="javascript:void(0);" method="post" id="chartReleaseForm">
            {{ csrf_field() }}
            <input type="hidden" name="chart_id" value="{{ $chartEntity->id()->value() }}">
            <div class="form-group row">
                <label class="col-3 control-label">{{ __('country_id') }}</label>
                <var class="col-9">{{ $chartEntity->countryId()->value() }}</var>
            </div>
            <div class="form-group row">
                <label class="col-3 control-label">{{ __('chart_name') }}</label>
                <var class="col-9">{{ $chartEntity->chartName()->value() }}</var>
            </div>
            <div class="form-group row">
                <label class="col-3 control-label">{{ __('scheme') }}</label>
                <var class="col-9">{{ $chartEntity->scheme() }}</var>
            </div>
            <div class="form-group row">
                <label class="col-3 control-label">{{ __('host') }}</label>
                <var class="col-9">{{ $chartEntity->host() }}</var>
            </div>
            <div class="form-group row">
                <label class="col-3 control-label">{{ __('uri') }}</label>
                <var class="col-9">{{ $chartEntity->uri() }}</var>
            </div>
            <div class="form-group row">
                <label class="col-3 control-label">{{ __('original_chart_name') }}</label>
                <var class="col-9">{{ !empty($chartEntity->originalChartName()) ? $chartEntity->originalChartName()->value() : '' }}</var>
            </div>
            <div class="form_group row">
                <label class="col-3 control-label">{{ __('page_title') }}</label>
                <var class="col-9">{{ $chartEntity->pageTitle() }}</var>
            </div>
            <div class="form-group text-center">
                <button type="submit" class="btn btn-primary" onclick="var ok = confirm('You are about to release {{ $chartEntity->label() }}.\nAre you sure?'); if (ok) $('#chartReleaseForm').attr('action', '{{ route('chart/release') }}');">
                    <i class="fas fa-home"></i><i class="fas fa-arrow-right"></i><i class="fas fa-globe-americas"></i>&nbsp;Release
                </button>

            </div>
        </form>
    </article>
</section>
<div id="modalChartModify" class="modal fade" tabIndex="-1" role="dialog" aria-labelledby="modalChartModifyLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalChartModifyLabel">Modify Chart</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php
                    $currentCountryId = !empty(old('country_id')) ? old('country_id') : $chartEntity->countryId()->value();
                    $currentChartName = !empty(old('chart_name')) ? old('chart_name') : $chartEntity->chartName()->value();
                    $currentScheme = !empty(old('scheme')) ? old('scheme') : $chartEntity->scheme();
                    $currentHost = !empty(old('host')) ? old('host') : $chartEntity->host();
                    $currentUri = !empty(old('uri')) ? old('uri') : $chartEntity->uri();
                    $currentOriginalChartName = !empty(old('original_chart_name')) ? old('original_chart_name') : !empty($chartEntity->originalChartName()) ? $chartEntity->originalChartName()->value() : '';
                    $pageTitle = !empty(old('page_title')) ? old('page_title') : $chartEntity->pageTitle();
                ?>
                <form action="{{route('chart/modify', ['chart_phase' => $chart_phase])}}" method="post">
                    {{ csrf_field() }}
                    {{ method_field('put') }}
                    <input type="hidden" name="chart_id" value="{{ $chartEntity->id()->value() }}">
                    <div class="form-group row">
                        <label for="modal_country_id" class="col-3 control-label">{{ __('country_id') }}</label>
                        <select name="country_id" id="modal_country_id" class="col-9 form-control">
                            @if (!empty($countryEntities))
                            @foreach ($countryEntities AS $countryEntity)
                            <option value="{{ $countryEntity->id()->value() }}" {{ ($countryEntity->id()->value() === $currentCountryId) ? 'selected="selected"' : '' }}>
                                {{ $countryEntity->getCountryName() }}
                            </option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group row">
                        <label for="modal_chart_name" class="col-3 control-label">{{ __('chart_name') }}</label>
                        <input type="text" name="chart_name" id="modal_chart_name" value="{{ $currentChartName }}" class="col-9 form-control">
                    </div>
                    <div class="form-group row">
                        <label for="modal_scheme" class="col-3 control-label">{{ __('scheme') }}</label>
                        <input type="text" name="scheme" id="modal_scheme" value="{{ $currentScheme }}" class="col-9 form-control">
                    </div>
                    <div class="form-group row">
                        <label for="modal_host" class="col-3 control-label">{{ __('host') }}</label>
                        <input type="text" name="host" id="modal_host" value="{{ $currentHost }}" class="col-9 form-control">
                    </div>
                    <div class="form-group row">
                        <label for="modal_uri" class="col-3 control-label">{{ __('uri') }}</label>
                        <input type="text" name="uri" id="modal_uri" value="{{ $currentUri }}" class="col-9 form-control">
                    </div>
                    <div class="form-group row">
                        <label for="modal_original_chart_name" class="col-3 control-label">{{ __('original_chart_name') }}</label>
                        <input type="text" name="original_chart_name" id="modal_original_chart_name" value="{{ $currentOriginalChartName }}" class="col-9 form-control">
                    </div>
                    <div class="form-group row">
                        <label for="modal_page_title" class="col-3 control-label">{{ __('page_title') }}</label>
                        <input type="text" name="page_title" id="modal_page_title" value="{{ $pageTitle }}" class="col-9 form-control">
                    </div>
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-edit"></i>&nbsp;Modfiy
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
