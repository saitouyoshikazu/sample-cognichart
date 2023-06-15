{{$chartAggregation->chartName()->value()}} {{$chartTermAggregation->endDate()->value()}} released.

@foreach ($rankingInformations AS $rankingInformation)
{{ $rankingInformation['ranking'] }} {{ $rankingInformation['artistName'] }} - {{ $rankingInformation['musicTitle'] }}
@endforeach

Check it out
{{ route(
    'chart/get',
    [
        'chartNameValue' => $chartAggregation->chartName()->value(),
        'countryIdValue' => $chartAggregation->countryId()->value()
    ]
)}}
