@if (!empty($chartAggregation))
<?php
$chartTermList = $chartAggregation->chartTermList();
$chartTermEntities = null;
if (!empty($chartTermList)) {
    $chartTermEntities = $chartTermList->chartTermEntities();
}
?>
<div class="dropdown chartTermList">
    <button class="btn btn-purple dropdown-toggle" type="button" id="chartTermList"
data-toggle="dropdown"
aria-haspopup="true"
aria-expanded="false"
data-loading-text="<i class='fas fa-compact-disc loading-spin'></i> Loading...">
        @if (!empty($end_date))
        {{ $end_date }}
        @else
        Select Date
        @endif
        <span class="caret"></span>
    </button>
    <div class="dropdown-menu" aria-labelledby="chartTermList">
        @if (!empty($chartTermEntities))
        @foreach ($chartTermEntities AS $chartTermEntity)
        <a href="/chart/{{ rawurlencode($chartAggregation->chartName()->value()) }}/{{ $chartAggregation->countryId()->value() }}/{{ $chartTermEntity->endDate()->value() }}" class="dropdown-item chartTermListItem">
            {{ $chartTermEntity->endDate()->value() }}
        </a>
        @endforeach
        @endif
    </div>
</div>
@endif
