@extends('templates.wwwtop')

@section('content')
<section class="topcontent">

    <nav class="topheader navbar navbar-expand navbar-light w-100 fixed-top flex-column">
        <div class="topheader-row navbar-nav w-100">
            <div class="toplogobox col-6 text-center">
@include('www.top.topcognichartlogo')
            </div>
            <div class="sns-links col-6 text-center">
@include('www.sns.snslinks')
            </div>
        </div>
    </nav>
    <article class="toparticle container-fluid">
        <div class="listentomusics row pt-2">
            <div class="col-12 text-center">
                <h1>{{__("In Cogni Chart, you can watch Youtube videos of hot songs.")}}</h1>
            </div>
        </div>
        <div class="row pt-2">
            <div class="col-8 offset-2">
                {{__("Release informations of each charts will be published on twitter or facebook.")}}
            </div>
        </div>
        <div class="row">
            <div class="col-8 offset-2">
                {{__("If you want to receive notifications, please follow Cogni Chart.")}}
            </div>
        </div>
        <div class="row">
            <nav class="navbar navbar-light col-sm-10 offset-sm-1 col-12">
                <div class="navbar-brand col-12">
                    music charts
                </div>
                <div class="navbar-nav col-sm-10 offset-sm-1 col-12">
@if (!empty($chartList))
@foreach ($chartList->chartEntities() AS $chartEntity)
@if ($loop->iteration % 2 == 1)
<div class="row pb-2">
@endif
    <div class="col-6">
        <a href="{{ route('chart/get', [
            'chartNameValue' => $chartEntity->chartName()->value(),
            'countryIdValue' => $chartEntity->countryId()->value()
        ]) }}" class="sitemaplink">{{$chartEntity->chartName()->value()}}</a>
    </div>
@if ($loop->iteration % 2 == 0 || $loop->last)
</div>
@endif
@endforeach
@endif
                </div>
            </nav>
        </div>
        <div class="row gadBox2">
            <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- ChartRanking内Ad -->
<ins class="adsbygoogle gad2" style="display:block;"
     data-ad-client="ca-pub-7212715746724887"
     data-ad-slot="9260128995"
     data-ad-format="auto"
     data-full-width-responsive="false"
></ins>
            <script>
                 (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </div>
    </article>
</section>
@endsection
