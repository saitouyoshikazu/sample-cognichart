@extends('templates.admin', ['adminMenu' => 'ChartTerm'])

@section('content')
<section class="card w-100">
    <nav class="card-heading navbar navbar-expand navbar-light bg-light flex-column">
        <div class="navbar-nav w-100 justify-content-around">
            <div class="navbar-brand">
                <strong>
                    {{ $chartEntity->label() }} ({{ $chartPhase->value() }})
                </strong>
            </div>
        </div>
        <div class="navbar-nav w-100 justify-content-around">
            <div class="navbar-brand">
                <strong>
                    {{ $chartTermAggregation->endDate()->value() }} ({{ $chartTermPhase->value() }})
                </strong>
            </div>
        </div>
        <div class="navbar-nav w-100 justify-content-around">
            @if ($chartTermPhase->isProvisioned())
            <form action="javascript:void(0);" class="form-inline" method="post" id="chartTermDeleteForm">
                {{ csrf_field() }}
                {{ method_field('delete') }}
                <input type="hidden" name="chart_phase" value="{{ $chartPhase->value() }}">
                <input type="hidden" name="country_id" value="{{ $chartEntity->countryId()->value() }}">
                <input type="hidden" name="chart_name" value="{{ $chartEntity->chartName()->value() }}">
                <input type="hidden" name="chartterm_id" value="{{ $chartTermAggregation->id()->value() }}">
                <button type="submit" class="btn btn-danger navbar-btn" onclick="var ok = confirm('You are about to delete ChartTerm {{ $chartEntity->label() }}, {{ $chartTermAggregation->endDate()->value() }}.\nAre you sure?'); if (ok) $('#chartTermDeleteForm').attr('action', '{{ route('chartterm/delete') }}');">
                    <i class="fas fa-trash-alt"></i>&nbsp;Delete
                </button>
            </form>
            @endif
            <div id="resolvechartrankingitems">
                <button type="button" class="btn btn-primary" v-on:click="sendResolve('{{ $chartTermPhase->value() }}', '{{ $chartTermAggregation->id()->value() }}', '{{ route('chartterm/resolve') }}')" id="resolvechartrankingitemsbutton" data-loading-text="Resolving..." autocomplete="off">
                    <i class="fas fa-question-circle"></i>&nbsp;Resolve
                </button>
            </div>
        </div>
    </nav>
    <article class="card-body">
        @if (!empty($chartTermAggregation->chartRankings()))
        @php
            $chartRankingItemRepository = app('App\Domain\ChartRankingItem\ChartRankingItemRepositoryInterface');
        @endphp
        @foreach ($chartTermAggregation->chartRankings() AS $chartRanking)
        @php
            $chartRankingItemEntity = null;
            if (!empty($chartRanking->chartRankingItemId())) {
                $chartRankingItemEntity = $chartRankingItemRepository->find($chartRanking->chartRankingItemId());
            }
        @endphp
        <div class="chartRankingItemRow">
            <div class="rankingBox">
                {{ $chartRanking->ranking() }}
            </div>
            <div id="chartrankingitem{{ $chartRankingItemEntity->id()->value() }}" class="chartRankingItemBox">
                <div class="w-100">
                    @include(
                        'admin.chartrankingitem.chartrankingitem',
                        [
                            'chartRankingItemEntity' => $chartRankingItemEntity,
                        ]
                    )
                </div>
            </div>
        </div>
        @endforeach
        @endif
    </article>
    <nav class="card-footer text-center">
        <form action="javascript:void(0);" method="post" id="chartTermActionForm" class="justify-content-center">
            {{ csrf_field() }}
            <input type="hidden" name="chart_phase" value="{{ $chartPhase->value() }}">
            <input type="hidden" name="country_id" value="{{ $chartEntity->countryId()->value() }}">
            <input type="hidden" name="chart_name" value="{{ $chartEntity->chartName()->value() }}">
            <input type="hidden" name="chartterm_phase" value="{{ $chartTermPhase->value() }}">
            <input type="hidden" name="end_date" value="{{ $chartTermAggregation->endDate()->value() }}">
            <input type="hidden" name="chartterm_id" value="{{ $chartTermAggregation->id()->value() }}">
            @if ($chartTermPhase->value() === App\Domain\ValueObjects\Phase::provisioned)
            <div class="form-check form-check-inline">
                <button type="submit" class="btn btn-primary" onclick="var ok = confirm('You are about to release ChartTerm {{ $chartEntity->label() }}, {{ $chartTermAggregation->endDate()->value() }}.\nAre you sure?'); if (ok) $('#chartTermActionForm').attr('action', '{{ route('chartterm/release') }}');">
                    <i class="fas fa-home"></i><i class="fas fa-arrow-right"></i><i class="fas fa-globe-americas"></i>&nbsp;Release
                </button>
            </div>
            <div class="form-check form-check-inline">
                <input type="checkbox" name="publish_released_message" value="1" class="form-check-input" id="publish-released-message" checked>
                <label class="form-check-label" for="publish-released-message">Publish released message.</label>
            </div>
            @else
            <div class="form-check form-check-inline">
                <button type="submit" class="btn btn-danger" onclick="var ok = confirm('You are about to rollback ChartTerm {{ $chartEntity->label() }}, {{ $chartTermAggregation->endDate()->value() }}.\nAre you sure?'); if (ok) $('#chartTermActionForm').attr('action', '{{ route('chartterm/rollback') }}');">
                    <i class="fas fa-globe-americas"></i><i class="fas fa-arrow-right"></i><i class="fas fa-home"></i>&nbsp;Rollback
                </button>
            </div>
            @endif
        </form>
    </nav>
</section>
<div id="itunesSearchResultModal" class="modal fade" tabIndex="-1" role="dialog" aria-labelledby="itunesSearchResultModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="itunesSearchResultModalLabel">iTunes Search</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="javascript:void(0);" id="itunessearchresultform">
                    <div class="form-group row">
                        <label for="itunes_search_result_modal_chart_artist" class="col-3 control-label">{{ __('chart_artist') }}</label>
                        <input type="text" name="chart_artist" id="itunes_search_result_modal_chart_artist" value="" class="col-9 form-control">
                    </div>
                    <div class="form-group row">
                        <label for="itunes_search_result_modal_chart_music" class="col-3 control-label">{{ __('chart_music') }}</label>
                        <input type="text" name="chart_music" id="itunes_search_result_modal_chart_music" value="" class="col-9 form-control">
                    </div>
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-primary" onclick="$('#itunessearchresultform').attr('target', $('#itunes_search_result_modal_chart_artist').val() + ', ' + $('#itunes_search_result_modal_chart_music').val()); $('#itunessearchresultform').attr('action', '{{ route('chartrankingitem/itunessearch') }}'); $('#itunesSearchResultModal').modal('hide');">
                            <i class="fas fa-search"></i>&nbsp;Search
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if (!empty($pageLocationId))
<script type="text/javascript">
    $(function() {
        $(window).scrollTop(
            $("#chartrankingitem{{$pageLocationId}}").offset().top
        );
    });
</script>
@endif
@endsection
