@extends('templates.admin', ['adminMenu' => 'AdminUser'])

@section('content')
<section class="card w-100">
    <nav class="card-heading navbar navbar-expand navbar-light bg-light flex-column">
        <div class="navbar-nav w-100 justify-content-around">
            <div class="navbar-brand">
                <strong>
                    Register
                </strong>
            </div>
        </div>
    </nav>
    <article class="card-body">
        <form method="POST" action="{{ route('register') }}">
            {{ csrf_field() }}

            <div class="form-group row{{ $errors->has('name') ? ' has-error' : '' }}">
                <label for="name" class="col-md-3 control-label">{{ __('name') }}</label>
                <div class="col-md-9">
                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>
                    @if ($errors->has('name'))
                    <span class="help-block">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                    @endif
                </div>
            </div>

            <div class="form-group row{{ $errors->has('email') ? ' has-error' : '' }}">
                <label for="email" class="col-md-3 control-label">{{ __('email') }}</label>
                <div class="col-md-9">
                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                    @if ($errors->has('email'))
                    <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                    @endif
                </div>
            </div>

            <div class="form-group row{{ $errors->has('password') ? ' has-error' : '' }}">
                <label for="password" class="col-md-3 control-label">{{ __('password') }}</label>
                <div class="col-md-9">
                    <input id="password" type="password" class="form-control" name="password" required>
                    @if ($errors->has('password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                    @endif
                </div>
            </div>

            <div class="form-group row">
                <label for="password-confirm" class="col-md-3 control-label">{{ __('password_confirmation') }}</label>
                <div class="col-md-9">
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i>&nbsp;Register
                    </button>
                </div>
            </div>
        </form>
    </article>
</section>
@endsection
