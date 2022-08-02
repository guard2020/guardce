<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name=”robots” content=”noindex, nofollow”>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Security Dashboard') }}</title>

    <!-- Scripts -->
    <script src="{!! asset('limitless/js/jquery.min.js') !!}"></script>
    <script src="{!! asset('limitless/js/bootstrap.bundle.min.js') !!}"></script>
    <script src="{!! asset('limitless/js/plugins/loaders/blockui.min.js') !!}"></script>
    <script src="{!! asset('limitless/js/plugins/ui/ripple.min.js') !!}"></script>

    <!-- Theme JS files -->
    <script src="{!! asset('limitless/material/js/app.js') !!}"></script>
    <!-- /theme JS files -->

    <!-- Dashboard Charts-->
    <script src="https://www.google.com/jsapi"></script>
    <script src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="{!! asset('limitless/js/plugins/visualization/echarts/echarts.min.js') !!}"></script>
    <script src="{!! asset('limitless/js/plugins/visualization/d3/d3.min.js') !!}"></script>
    <script src="{!! asset('limitless/js/plugins/visualization/d3/d3_tooltip.js') !!}"></script>
    <script src="{!! asset('limitless/js/plugins/ui/moment/moment.min.js') !!}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js" charset="utf-8"></script>

    <script src="{!! asset('js/smartdashboard.js') !!}"></script>
    <script src="{!! asset('js/cytoscape/cytoscape.min.js') !!}"></script>
    <script src="{!! asset('js/cytoscape/cytoscape-node-html-label.min.js') !!}"></script>



    <script src="{!! asset('limitless/js/plugins/forms/styling/uniform.min.js') !!}"></script>





    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
    <link rel="dns-prefetch" href="{!! asset('plugins/font-awesome/webfonts') !!}">
    <link href="{!! asset('plugins/font-awesome/css/all.min.css') !!}" rel="stylesheet">

    <!-- Styles -->
    <!-- Global stylesheets -->
    <link href="{!! asset('plugins/font-awesome/css/all.min.css')!!}" rel="stylesheet" type="text/css">
    <link href="{!! asset('limitless/css/icons/icomoon/styles.min.css') !!}" rel="stylesheet" type="text/css">
    <link href="{!! asset('limitless/material/css/bootstrap.min.css') !!}" rel="stylesheet" type="text/css">
    <link href="{!! asset('limitless/material/css/bootstrap_limitless.min.css') !!}" rel="stylesheet" type="text/css">
    <link href="{!! asset('limitless/material/css/layout.min.css') !!}" rel="stylesheet" type="text/css">
    <link href="{!! asset('limitless/material/css/components.min.css') !!}" rel="stylesheet" type="text/css">
    <link href="{!! asset('limitless/material/css/colors.min.css') !!}" rel="stylesheet" type="text/css">
    @yield("style-links")
    <link rel="stylesheet" type="text/css" href="{!! asset('css/smartdashboard.css') !!}">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    @yield("stylesheets")


</head>
<body class="">
@include('layouts.auth-navbar')
<!-- Page content -->
<div class="page-content">

@yield('secondary-sidebar')

<!-- Main content -->
    <div class="content-wrapper">

    @yield('page-header')

    <!-- Content area -->
        <div class="content ">

            @yield('content')

        </div>
        <!-- /content area -->
        @include('layouts.footer')

    </div>
    <!-- /main content -->

    <!-- right sidebar -->
@yield('right-sidebar')

<!-- /right sidebar -->
</div>
<!-- /page content -->
@yield('scripts')
@include('partials.js.scripts')
</body>
</html>