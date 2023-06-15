<div class="dropdown chartList">
    <button class="btn btn-purple dropdown-toggle" type="button" id="chartList"
data-toggle="dropdown"
aria-haspopup="true"
aria-expanded="false"
data-loading-text="<i class='fas fa-compact-disc loading-spin'></i> Loading...">
        @if (!empty($chartAggregation))
        {{ $chartAggregation->chartName()->value() }}
        @else
        Select Chart
        @endif
        <span class="caret"></span>
    </button>
    @if (!empty($chartList))
    <div class="dropdown-menu" aria-labelledby="chartList">
        @foreach ($chartList AS $chartEntity)
        <a href="/chart/{{ rawurlencode($chartEntity->chartName()->value()) }}/{{ $chartEntity->countryId()->value() }}" class="dropdown-item chartListItem">
            {{ $chartEntity->chartName()->value() }}
        </a>
        @endforeach
    </div>
    @endif
</div>
