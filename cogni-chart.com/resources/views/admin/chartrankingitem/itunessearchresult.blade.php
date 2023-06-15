@extends('templates.admin', ['adminMenu' => 'ChartTerm'])

@section('content')
<pre>
{{var_export($clarifiedArtistName, true)}}
{{var_export($itunesSearchResult, true)}}
</pre>
@endsection
