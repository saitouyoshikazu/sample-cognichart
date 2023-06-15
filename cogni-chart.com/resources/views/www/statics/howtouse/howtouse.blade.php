@extends('templates.www')

@section('content')
@include('www.statics.howtouse.howtousedoc')
<script type="text/javascript">
    window.onload = function () {
        $('#howtouse').trigger('click');
    };
</script>
@endsection
