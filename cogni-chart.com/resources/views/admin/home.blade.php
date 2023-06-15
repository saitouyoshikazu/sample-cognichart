@extends('templates.admin', ['adminMenu' => ''])

@section('content')
<section class="card w-100">
    <article class="card-body">
        <dl>
            <dt>
                Welcome
            </dt>
            <dd class="offset-1">
                This is management page of cogni-chart.
            </dd>
            <dt>
                Notice
            </dt>
            <dd class="offset-1">
                Management page is using SSL Client certification.</br>
                Never lose SSL client certificate file.</br>
                Never give SSL client certificate file to anyone else.</br>
                If you lose SSL Client certificate file, please contact the administrator as soon as possible.
            </dd>
        </dl>
    </article>
</section>
@endsection
