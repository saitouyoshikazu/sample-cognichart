<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CogniChart') }}</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('ico/favicon.ico') }}">
    <link rel="apple-touch-icon" size="180x180" href="{{ asset('png/apple-touch-icon-180x180.png') }}">

    <!-- Styles -->
    <link href="{{ asset('css/admin/admin.css') }}" rel="stylesheet">
    <script src="{{ asset('js/app.js') }}"></script>
</head>
<body>

    <nav class="extNavbar navbar-expand-lg navbar-light bg-light navbar-static-top">
        <div class="container">
            <div class="logo">
                <a class="extNavbar-brand" href="{{ url('/login') }}">
                    <img src="{{ asset('png/cogni-chart.png') }}"/>
                </a>
            </div>
            @auth
            <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#app-navbar-collapse" aria-controls="app-navbar-collapse" aria-expanded="false" aria-label="Toggle Navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            @endauth
            <div class="navbar-collapse collapse" id="app-navbar-collapse">
                @auth
                <ul class="navbar-nav ml-auto text-right">
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle ml-auto" id="app-nav" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="app-nav">
                            <a href="javascript:void(0);" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('adminuserGetForm').submit();">
                                Modify
                            </a>
                            <a href="{{ route('logout') }}" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Logout
                            </a>
                            <form action="{{ route('adminuser/get') }}" method="get" id="adminuserGetForm" style="display: none;">
                                <input type="hidden" name="adminuser_id" value="{{ Auth::user()->id }}">
                            </form>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </div>
                    </li>
                </ul>
                @endauth
            </div>
        </div>
    </nav>

@auth
@include('common.errors')
    <section class="admincontainer">
        <div class="row">
            <section class="adminmenu col-2">
@include('admin.adminmenu', ['adminMenu' => $adminMenu])
            </section>
            <section class="admincontent col-10">
                <div class="row">
@yield('content')
                </div>
            </section>
        </div>
    </section>
@else
@yield('content')
@endauth

    <section class="adminfooter">
        <div class="row">
@include('admin.adminfooter')
        </div>
    </section>

    <!-- Scripts -->
    <script src="https://www.youtube.com/iframe_api"></script>
    <script src="{{ asset('js/admin/admin.js') }}"></script>
</body>
</html>
