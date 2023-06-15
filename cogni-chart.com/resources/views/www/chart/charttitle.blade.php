<section class="charttitle w-100 pt-1 pb-1 d-flex flex-column">
    <div class="text-center">
        <h1>{{$chartAggregation->chartName()->value()}}</h1>
    </div>
@if (!empty($chartTermAggregation))
    <div class="text-center">
        <h2>{{$chartTermAggregation->endDate()->getDate()->format('Y.m.d')}}</h2>
    </div>
@endif
</section>
