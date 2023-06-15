@extends('templates.admin', ['adminMenu' => 'ChartRankingItem'])

@section('content')
<section class="card w-100">
    <nav class="card-heading navbar navbar-expand navbar-light bg-light flex-column">
        <div class="navbar-nav w-100 justify-content-around">
            <div class="navbar-brand">
                <strong>
                    Not Attached ChartRankingItem
                </strong>
            </div>
        </div>
        <div class="navbar-nav w-100 justify-content-around">
            <form action="{{ route('chartrankingitem/notattached') }}" class="form-inline" method="get">
                <div class="col-5">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                Chart Artist
                            </div>
                        </div>
                        <input type="text" name="search_chart_artist" value="{{ !empty(old('search_chart_artist')) ? old('search_chart_artist') : $search_chart_artist }}" class="form-control" placeholder="Chart Artist">
                    </div>
                </div>
                <div class="col-5">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                Chart Music
                            </div>
                        </div>
                        <input type="text" name="search_chart_music" value="{{ !empty(old('search_chart_music')) ? old('search_chart_music') : $search_chart_music }}" class="form-control" placeholder="Chart Music">
                    </div>
                </div>
                <div class="col-2">
                    <div class="input-group">
                        <button type="submit" class="btn btn-secondary">
                            <i class="fas fa-search"></i>Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </nav>
    <article class="card-body">
        @foreach ($chartRankingItemEntities AS $chartRankingItemEntity)
            <div class="card card-body">
                @include(
                    'admin.chartrankingitem.chartrankingitem',
                    [
                        'chartRankingItemEntity' => $chartRankingItemEntity
                    ]
                )
            </div>
        @endforeach
        {{
             $chartRankingItemPaginator
                ->appends([
                    'search_chart_artist' => $search_chart_artist,
                    'search_chart_music' => $search_chart_music,
                ])
                ->links()
        }}
    </article>
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
@endsection
