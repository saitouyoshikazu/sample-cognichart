{{$chartAggregation->chartName()->value()}} {{$chartTermAggregation->endDate()->value()}}

@foreach ($rankingInformations AS $rankingInformation)
{{ $rankingInformation['ranking'] }} {!! $rankingInformation['artistName'] !!} - {!! $rankingInformation['musicTitle'] !!}
@endforeach

Check it out
{{ route(
    'chart/get',
    [
        'chartNameValue' => $chartAggregation->chartName()->value(),
        'countryIdValue' => $chartAggregation->countryId()->value()
    ]
)}}

@php
    $hashtag = "";
    switch ($chartAggregation->chartName()->value()) {
        case "USA Singles Chart":
            $hashtag = "#USA";
            break;
        case "UK Singles Chart":
            $hashtag = "#UK";
            break;
        case "Scotland Singles Chart":
            $hashtag = "#UK #Scotland";
            break;
        case "Ireland Singles Chart":
            $hashtag = "#Ireland";
            break;
        case "Australia Singles Chart":
            $hashtag = "#Australia";
            break;
        case "Australian Artist Singles Chart":
            $hashtag = "#Australia";
            break;
        case "Sweden Singles Chart":
            $hashtag = "#Sweden";
            break;
        case "Swedish Artist Singles Chart":
            $hashtag = "#Sweden";
            break;
        default:
            $hashtag = "";
    }
@endphp
{{$hashtag}} #music #musicchart #YouTube
