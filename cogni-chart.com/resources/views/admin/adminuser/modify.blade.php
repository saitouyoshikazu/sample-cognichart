@extends('templates.admin', ['adminMenu' => 'AdminUser'])

@section('content')
<section class="card w-100">
    <nav class="card-heading navbar navbar-expand navbar-light bg-light flex-column">
        <div class="navbar-nav w-100 justify-content-around">
            <div class="navbar-brand">
                <strong>
                    Modify Admin User
                </strong>
            </div>
        </div>
        <div class="navbar-nav w-100 justify-content-around">
            @if ($selfAdminUserEntity->isSuperUser() && !$selfAdminUserEntity->equals($adminUserEntity))
            <form action="javascript:void(0);" class="form-inline" method="post" id="adminUserDeleteForm">
                {{ method_field('delete') }}
                {{ csrf_field() }}
                <input type="hidden" name="adminuser_id" value="{{ $adminUserEntity->getId()->value() }}">
                <button type="submit" class="btn btn-outline-danger" onclick="var ok = confirm('You are about to delete this user.\nAre you sure?'); if (ok) $('#adminUserDeleteForm').attr('action', '{{ route('adminuser/delete') }}');">
                    Delete this user ?
                </button>
            </form>
            @endif
            @if ($selfAdminUserEntity->isSuperUser() || $selfAdminUserEntity->equals($adminUserEntity))
            <form action="javascript:void(0);" method="post" class="form-inline" id="sendPasswordResetMailForm">
                {{ csrf_field() }}
                <input type="hidden" name="email" value="{{ $adminUserEntity->getEmail() }}">
                <button type="submit" class="btn btn-warning" onclick="var ok = confirm('You are about to send password reset mail.\nAre you sure?'); if (ok) $('#sendPasswordResetMailForm').attr('action', '{{ route('adminuser/password/change') }}');">
                    Send Password Reset Link
                </button>
            </form>
            @endif
        </div>
    </nav>
    <article class="card-body">
        @if ($selfAdminUserEntity->isSuperUser() || $selfAdminUserEntity->equals($adminUserEntity))
        <form action="javascript:void(0);" method="post" id="adminUserModifyForm">
            {{ csrf_field() }}
            {{ method_field('put') }}
            <input type="hidden" name="adminuser_id" value="{{ $adminUserEntity->getId()->value() }}">
            <div class="form-group row{{ $errors->has('name') ? ' has-error' : '' }}">
                <label for="name" class="col-md-3 control-label">{{ __('name') }}</label>
                <div class="col-md-9">
                    <input id="name" type="text" class="form-control" name="name" value="{{ !empty(old('name')) ? old('name') : $adminUserEntity->getName() }}" required autofocus>
                </div>
            </div>

            <div class="form-group row{{ $errors->has('email') ? ' has-error' : '' }}">
                <label for="email" class="col-md-3 control-label">{{ __('email') }}</label>
                <div class="col-md-9">
                    <input id="email" type="email" class="form-control" name="email" value="{{ !empty(old('email')) ? old('email') : $adminUserEntity->getEmail() }}" required>
                </div>
            </div>

            @if ($selfAdminUserEntity->isSuperUser() && !$selfAdminUserEntity->equals($adminUserEntity))
            <div class="form-group row{{ $errors->has('superuser') ? ' has_error' : '' }}">
                <label for="superuser" class="col-md-3 control-label">{{ __('superuser') }}</label>
                <div class="col-md-9">
                    <select id="superuser" name="superuser" class="form-control">
                        <option value="0"<?php
                            if (!empty(old('superuser'))) {
                                if (old('superuser') === "0") {
                                    echo ' selected="selected"';
                                }
                            } elseif (!$adminUserEntity->isSuperUser()) {
                                echo ' selected="selected"';
                            }
                        ?>>FALSE</option>
                        <option value="1"<?php
                            if (!empty(old('superuser'))) {
                                if (old('superuser') === "1") {
                                    echo ' selected="selected"';
                                }
                            } elseif ($adminUserEntity->isSuperUser()) {
                                echo ' selected="selected"';
                            }
                        ?>>TRUE</option>
                    </select>
                </div>
            </div>
            @endif
            <div class="text-center">
                <button type="submit" class="btn btn-info" onclick="var ok = confirm('You are about to modify user imformation.\nAre you sure?'); if (ok) $('#adminUserModifyForm').attr('action', '{{ route('adminuser/update') }}');">
                    <i class="fas fa-edit"></i>&nbsp;Update
                </button>
            </div>
        </form>
        @endif
    </article>
</section>
@endsection
