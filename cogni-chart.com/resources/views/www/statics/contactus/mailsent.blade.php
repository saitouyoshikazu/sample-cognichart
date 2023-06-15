@extends('templates.www')

@section('content')
<div class="w-100">
    <div class="contactus-wakeup-board pl-4 pr-4">
        <div class="row h-100">
            <div class="col-12 align-self-center text-center">
                <div class="alert alert-success">
                    <p>
                        {{ __("Your email was correctly sent.") }}</br>
                    </p>
                    <p>
                        {{ __("Please wait for the reply of your email.") }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
