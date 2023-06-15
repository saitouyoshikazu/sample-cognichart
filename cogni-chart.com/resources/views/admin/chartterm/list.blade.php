@extends('templates.admin', ['adminMenu' => 'ChartTerm'])

@section('content')
<section class="card w-100">
    <nav class="card-heading navbar navbar-expand navbar-light bl-light flex-column">
        <div class="navbar-nav w-100 justify-content-around">
            <div class="navbar-brand">
                <strong>Chart Term List</strong>
            </div>
        </div>
        <div class="navbar-nav w-100 justify-content-around">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalChartList">
                @if (empty($selectedChartEntity))
                    Select Chart
                @else
                    {{ $selectedChartEntity->label() }}
                @endif
            </button>
        </div>
    </nav>
    <article class="card-body">
        <div class="row">
            <div class="col-6">
                <div class="card w-100">
                    <nav class="card-heading navbar justify-content-around">
                        <div class="navbar-brand">
                            <strong>
                                {{ \App\Domain\ValueObjects\Phase::released }}
                            </strong>
                        </div>
                    </nav>
                    <div class="card-body">
                        @if (!empty($releasedChartTermList))
                        <table class="table text-center">
                            @foreach ($releasedChartTermList AS $chartTermEntity)
                            <tr class="mouseOverFocus chartTermRow"
                                data-chart_phase="{{ $chartPhase }}"
                                data-country_id="{{ $selectedChartEntity->countryId()->value() }}"
                                data-chart_name="{{ $selectedChartEntity->chartName()->value() }}"
                                data-chartterm_phase="{{ \App\Domain\ValueObjects\Phase::released }}"
                                data-end_date="{{ $chartTermEntity->endDate()->value() }}">
                                <td>
                                    {{ $chartTermEntity->endDate()->value() }}
                                </td>
                            </tr>
                            @endforeach
                        </table>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card w-100">
                    <nav class="card-heading navbar justify-content-around">
                        <div class="navbar-brand">
                            <strong>
                                {{ \App\Domain\ValueObjects\Phase::provisioned }}
                            </strong>
                        </div>
                    </nav>
                    <div class="card-body">
                        @if (!empty($provisionedChartTermList))
                        <table class="table text-center">
                            @foreach ($provisionedChartTermList AS $chartTermEntity)
                            <tr class="mouseOverFocus chartTermRow"
                                data-chart_phase="{{ $chartPhase }}"
                                data-country_id="{{ $selectedChartEntity->countryId()->value() }}"
                                data-chart_name="{{ $selectedChartEntity->chartName()->value() }}"
                                data-chartterm_phase="{{ \App\Domain\ValueObjects\Phase::provisioned }}"
                                data-end_date="{{ $chartTermEntity->endDate()->value() }}">
                                <td>
                                    {{ $chartTermEntity->endDate()->value() }}
                                </td>
                            </tr>
                            @endforeach
                        </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </article>
</section>

<div id="modalChartList" class="modal fade" tabIndex="-1" role="dialog" aria-labelledby="modalChartListLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalChartListLabel">Select ChartList</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-6">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>
                                        Released
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (!empty($releasedChartList))
                                @foreach($releasedChartList AS $chartEntity)
                                <tr class="mouseOverFocus chartTermChartRow" data-chart_phase="{{ \App\Domain\ValueObjects\Phase::released }}" data-country_id="{{ $chartEntity->countryId()->value() }}" data-chart_name="{{ $chartEntity->chartName()->value() }}">
                                    <td>
                                        {{ $chartEntity->label() }}
                                    </td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="col-6">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>
                                        Provisioned
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (!empty($provisionedChartList))
                                @foreach($provisionedChartList AS $chartEntity)
                                <tr class="mouseOverFocus chartTermChartRow" data-chart_phase="{{ \App\Domain\ValueObjects\Phase::provisioned }}" data-country_id="{{ $chartEntity->countryId()->value() }}" data-chart_name="{{ $chartEntity->chartName()->value() }}">
                                    <td>
                                        {{ $chartEntity->label() }}
                                    </td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
