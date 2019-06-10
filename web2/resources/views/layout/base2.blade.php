<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>

    <!-- meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- css -->
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/main.css">

    <!-- google font -->
    <link rel='stylesheet' href='http://fonts.googleapis.com/css?family=Kreon:300,400,700'>
    
    <!-- js -->
    <script src="/js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
       
        @section('stylesheet')
        @show
    </head>
    <body >
            @section('body-content')
            @show
      
        <style>  
            .notifyjs-corner {
                top: 75px;
            }
            .navbar-nav>li:nth-child(4) {
    margin-right: 0px;
}
        </style>
        <script src="https://unpkg.com/vue"></script>
        <script src="https://unpkg.com/vuejs-datepicker"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.bundle.min.js" integrity="sha384-u/bQvRA/1bobcXlcEYpsEdFVK/vJs3+T+nXLsBYJthmdBuavHvAW6UsmqO2Gd/F9" crossorigin="anonymous"></script>
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
