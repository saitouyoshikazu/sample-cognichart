@if (!empty($chartAggregation->originalChartName()))
The original chart of this page is : <a href="{{ $chartAggregation->scheme().'://'.$chartAggregation->host().'/'.$chartAggregation->uri() }}" target="_blank">{{ $chartAggregation->originalChartName()->value() }}</a>
@endif
