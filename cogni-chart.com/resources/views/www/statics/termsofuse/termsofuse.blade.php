@extends('templates.www')

@section('content')
@include('www.statics.termsofuse.termsofusedoc')
<script type="text/javascript">
    window.onload = function () {
        $('#termsofuse').trigger('click');
    };
</script>
@endsection
