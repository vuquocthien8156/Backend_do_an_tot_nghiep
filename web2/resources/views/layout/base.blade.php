<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimal-ui, shrink-to-fit=no"/>
        <meta name="description" content="page description"/>

        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- for ios 7 style, multi-resolution icon of 152x152 --}}
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-barstyle" content="black-translucent">
        <link rel="apple-touch-icon" href="/images/logo.png">
        <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
        {{-- for Chrome on Android, multi-resolution icon of 196x196 --}}
        <meta name="mobile-web-app-capable" content="yes">
        <link rel="shortcut icon" sizes="196x196" href="/favicon.ico">
        <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.6/dist/jquery.fancybox.min.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css" integrity="sha384-OHBBOqpYHNsIqQy8hL1U+8OXf9hH6QRxi0+EODezv82DfnZoV7qoHAZDwMwEJvSw" crossorigin="anonymous">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        {{--<link rel="stylesheet" href="/css/glyphicons/glyphicons.css" type="text/css"/>--}}
       <!--  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous"> -->
        <!-- <link rel="stylesheet"href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> -->
        <link rel="stylesheet" type="text/css" href="/css/font-awesome.min.css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" href="{{ mix('/css/app.css') }}" type="text/css"/>
        <script src='https://www.google.com/recaptcha/api.js'></script>
       
        @section('stylesheet')
        @show
    </head>
    <body >
         @if(Session::has('login') && Session::get('login') == true && Session::has('name'))
        <div class="header bg-white fixed-top">
            <nav class="navbar box-shadow container" id="navbar-example2">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div class="d-flex align-items-center flex-grow-1 flex-md-grow-0">
                        <a href="/customer/manage" class="navbar-brand p-0 m-0 d-flex">
                            <img src="/images/logo1.png" alt="" style="width: 70px; height: 65px">
                        </a>
                    </div>
                    <div class="d-none d-md-block flex-md-grow-1 justify-content-center">
                        <ul id="header-navbar" class="nav justify-content-center font-weight-bold">
                            <li class="nav-item d-none d-lg-block dropdown">
                                    <a class="nav-link p-2 px-lg-3 dropdown-toggle" 
                                        style="font-size: 16px" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                       TÀI KHOẢN
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{route("manage-account", [], false)}}">Xem danh sách</a>
                                </div>
                            </li>
                            <li class="nav-item d-none d-lg-block dropdown">
                                    <a class="nav-link p-2 px-lg-3 dropdown-toggle" 
                                        style="font-size: 16px" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                       SẢN PHẨM
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{route("manage-product", [], false)}}">Xem danh sách</a>
                                    <a class="dropdown-item" href="{{route("product-add", [], false)}}">Thêm sản phẩm</a>
                                    <a class="dropdown-item" href="">Danh sách loại sản phẩm</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="btn-group d-flex align-items-center">
                       <li class="nav-item d-none d-lg-block dropdown">

                            <a class="nav-link p-2 px-lg-3 dropdown-toggle" style="font-size: 16px" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="/images/logo1.png" width="30px" style="border-radius: 50%;">
                                {{ Session::get('name') }}
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{route("logout", [], false)}}">Logout</a>
                            </div>
                        </li>
                    </div>
                </div>
            </nav>
        </div>
        @endif
            @section('body-content')
            @show
      
        <style>  
            .notifyjs-corner {
                top: 75px;
            }
        </style>
        <script src="https://unpkg.com/vue"></script>
        <script src="https://unpkg.com/vuejs-datepicker"></script>
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
        @show
    </body>
</html>
