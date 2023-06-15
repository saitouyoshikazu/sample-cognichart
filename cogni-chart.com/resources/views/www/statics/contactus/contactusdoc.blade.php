<div class="w-100">
    <div class="contactus-wakeup-board-wrapper">
        <div class="contactus-wakeup-board pl-4 pr-4">
            @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <h3 class="heading">{{ __("Contact Us") }}</h3>
            <p class="sentence">
                {{ __("Cogni Chart is managed by itgoritech(IT合理テック).") }}
            </p>
            <p class="sentence">
                {{ __("I don't have website of the itgoritech, because I just started up.") }}
            </p>
            <p class="sentence">
                {{ __("So, if you want to contact me, please input and submit the form below, or please send email to ") }}<a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a>
            </p>
            <div class="card w-100">
                <div class="card-body">
                    <form action="{{ route('contactmail') }}" method="post">
                        {{csrf_field()}}
                        <div class="form-row">
                            <div class="form-group col-12 col-md-12 col-lg-6 col-xl-6">
                                <label for="your-email-address">
                                    {{ __("Your email address") }}
                                </label>
                                <input type="text" name="your_email_address" id="your-email-address" value="{{!empty(old('your_email_address')) ? old('your_email_address') : '' }}" class="form-control">
                            </div>
                            <div class="form-group col-12 col-md-12 col-lg-6 col-xl-6">
                                <label for="subject">
                                    {{ __("Subject") }}
                                </label>
                                <input type="text" name="subject" id="subject" value="{{!empty(old('subject')) ? old('subject') : '' }}" class="form-control">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="body-of-email">
                                    {{ __("Body of email") }}
                                </label>
                                <textarea name="body_of_email" rows="8" class="form-control">{{!empty(old('body_of_email')) ? old('body_of_email') : '' }}</textarea>
                            </div>
                        </div>
                        <div class="form-row justify-content-center">
                            <input type="submit" class="btn btn-success" value="Submit">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
