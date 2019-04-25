<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimal-ui, shrink-to-fit=no"/>
        <meta name="description" content="page description"/>

        <title></title>

        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- for ios 7 style, multi-resolution icon of 152x152 --}}
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-barstyle" content="black-translucent">
        <!-- <link rel="apple-touch-icon" href="/images/logo.png"> -->
        <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
        {{-- for Chrome on Android, multi-resolution icon of 196x196 --}}
        <meta name="mobile-web-app-capable" content="yes">
        <link rel="shortcut icon" sizes="196x196" href="/favicon.ico">
        <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css" integrity="sha384-OHBBOqpYHNsIqQy8hL1U+8OXf9hH6QRxi0+EODezv82DfnZoV7qoHAZDwMwEJvSw" crossorigin="anonymous">
        {{--<link rel="stylesheet" href="/css/glyphicons/glyphicons.css" type="text/css"/>--}}
        <link rel="stylesheet" href="/css/main.css" type="text/css"/>
        <link rel="stylesheet" href="/css/bootstrap.min.css" type="text/css"/>
        <link rel="stylesheet" href="/css/bootstrap-theme.min.css" type="text/css"/>
        <link rel="stylesheet" href="/css/font-awesome.min.css" type="text/css"/>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <!-- <link rel="stylesheet" href="{{ mix('/css/app.css') }}" type="text/css"/> -->
        <script src='https://www.google.com/recaptcha/api.js'></script>
       
        @section('stylesheet')
        @show
    </head>
    <body >
        <div id="menu" class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header visible-xs">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#"><h2>T&T</h2></a>
            </div><!-- navbar-header -->
        <div id="navbar" class="navbar-collapse collapse">
            <div class="hidden-xs" id="logo" style="background: #ce252ce3"><a href="#header">
                <img src="/images/logo.png" alt="">
            </a></div>

            <ul class="nav navbar-nav" style="margin-left:20%">
                @if(Session::has('login') && Session::get('login') == true && Session::has('name'))
                    <li><a style="color: white" href="#story">Story</a></li>
                    <li><a style="color: white" href="#reservation">Reservation</a></li>
                    <li><a style="color: white" href="#chefs">Our Chefs</a></li>
                    <li><a style="color: white" href="#facts">Facts</a></li>
                    @if((Session::get('vaitro') == 1 && Session::get('type') == 1))
                    <li><a style="color: white" href="permission">Phân quyền</a></li>
                    @endif
                    @if((Session::get('vaitro') == 1 && Session::get('type') == 1) || (Session::get('vaitro') == 1 && Session::get('type') == 2))

                        <li class="dropdown">
                            <a style="color: white" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Tài Khoản<span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="{{route("manage-account", [], false)}}">Xem danh sách</a></li>
                            </ul>
                        </li>
                    @else
                        <li><a style="color: white" href="#special-offser">Special Offers</a></li>
                        <li><a style="color: white" href="#special-offser">Special Offers</a></li>
                    @endif
                    <li class="dropdown">
                        <a style="color: white" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ Session::get('name') }} <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="/logout">Đăng xuất</a></li>
                        </ul>
                    </li>
                @else
                    <li><a style="color: white" href="#story">Story</a></li>
                    <li><a style="color: white" href="#reservation">Reservation</a></li>
                    <li><a style="color: white" href="#chefs">Our Chefs</a></li>
                    <li><a style="color: white" href="#facts">Facts</a></li>
                    <li><a style="color: white" href="#special-offser">Special Offers</a></li>
                    <li><a style="color: white" href="#special-offser">Special Offers</a></li>
                    <li><a style="color: white" href="login">Login</a></li>
                    <li><a style="color: white" href="register">Register</a></li>
                @endif

            </ul>
        </div><!--/.navbar-collapse -->
        </div><!-- container -->
    </div><!-- menu -->

    <div id="header" style="height: 532px">
        <div class="bg-overlay"></div>
        <div class="center text-center">
            <div class="banner">
                 <img style="margin-top: 10%" src="/images/logo1.png" alt="" width="300px;" height="300px;">
            </div>
            <!-- <div class="subtitle"><h4>AWESOME RESTAURANT THEME</h4></div> -->
        </div>
    </div>
        <div class="dplhd"></div>
        <div class='notifications bottom-right'></div>

            <div>
                @section('body-header')
                @show
                @section('body-content')
                
                @show
            </div>
        <footer id="footer" class="dark-wrapper">
        <section class="ss-style-top"></section>
        <div class="container inner" style="height: 200px;">
            <div class="row">
                <div class="col-sm-6">
                    &copy; Copyright MeatKing 2014
                    <br/>Theme By <a class="themeBy" href="http://www.Themewagon.com">ThemeWagon</a>
                </div>
            </div>
        </div>
        <!-- /.container -->
    </footer>
        <style>  
            .notifyjs-corner {
                top: 75px;
            }
        </style>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.bundle.min.js" integrity="sha384-u/bQvRA/1bobcXlcEYpsEdFVK/vJs3+T+nXLsBYJthmdBuavHvAW6UsmqO2Gd/F9" crossorigin="anonymous"></script>
        
        {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-notify/0.2.0/css/styles/alert-bangtidy.min.css" />         --}}
        <script src="/js/lib/bootbox.min.js"></script>
        {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-notify/0.2.0/js/bootstrap-notify.min.js"></script>         --}}
        <script src="{{ mix('/js/lib/prefixfree.min.js') }}"></script>
        <script src="{{ mix('/js/lib/notify.min.js') }}"></script>
        <script src="{{ mix('/js/ui.js') }}"></script>
        <script src="{{ mix('/js/manifest.js') }}"></script>
        <script src="{{ mix('/js/vendor.js') }}"></script>
        <script src="{{ mix('/js/app.js') }}"></script>
        <script src="{{ mix('/js/common.js') }}"></script>
        @section('scripts')
    <script type="text/javascript">
        @php
            include public_path('/js/bootstrap.min.js');
            include public_path('/js/jquery.actual.min.js');
            include public_path('/js/jquery.scrollTo.min.js');
            include public_path('/js/main.js');
        @endphp
    </script>
@endsection
        @section('scripts')
        @show
    </body>
</html>
