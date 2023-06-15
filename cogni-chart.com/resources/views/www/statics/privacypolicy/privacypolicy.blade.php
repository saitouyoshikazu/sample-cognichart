@extends('templates.www')

@section('content')
@include('www.statics.privacypolicy.privacypolicydoc')
<script type="text/javascript">
    window.onload = function () {
        $('#privacypolicy').trigger('click');
    };
</script>
@endsection
