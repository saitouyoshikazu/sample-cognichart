@extends('templates.www')

@section('content')
@include('www.statics.contactus.contactusdoc')
<script type="text/javascript">
    window.onload = function () {
        $('#contactus').trigger('click');
    };
</script>
@endsection
