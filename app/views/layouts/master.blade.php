<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>
        @if (empty($title))
            @lang('strings.site_title')
        @else
            {{ $title }} :: @lang('strings.site_title')
        @endif
    </title>

    {{ HTML::style('assets/css/bootstrap.min.css') }}
    {{ HTML::style('assets/css/common.css') }}
    {{ HTML::script('assets/js/vendor/respond.min.js') }}

    @section('style')

    @show

    <!--[if lt IE 9]>
    {{ HTML::script('assets/js/vendor/html5shiv.js') }}
    <![endif]-->
</head>
<body>

@section('navbar')
@show

@section('sidebar')
@show

<div class="container">
    @yield('content')
</div>

{{ HTML::script('assets/js/vendor/jquery-1.10.1.min.js') }}
{{ HTML::script('assets/js/vendor/bootstrap.min.js') }}
{{ HTML::script('assets/js/vendor/modernizr-2.6.2.min.js') }}
{{ HTML::script('assets/js/vendor/jquery.placeholder.js') }}
{{ HTML::script('assets/js/common.js') }}
{{ HTML::script('assets/js/plugins.js') }}

@section('script')

@show
</body>
</html>
