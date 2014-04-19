<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title> 
		@lang('strings.site_title') :: 
		{{ $title or '' }} 
	</title>

	{{ HTML::style('static/vendor/bootstrap/css/bootstrap.min.css') }}
	{{ HTML::style('static/css/public.css') }}
    <!--[if lt IE 9]>
        {{ HTML::script('static/vendor/html5shiv/html5shiv.js') }}
        {{ HTML::script('static/vendor/respond/respond.min.js') }}
    <![endif]-->
	<link rel="shortcut icon" href="{{ url('static/img/favicon.ico') }}?v=2">
    @section('styles')
    @show
</head>
<body>

@section('header')
@show

<div class="container">
    @yield('content')
</div>

@include('parts.public-footer')

{{ HTML::script('static/vendor/jquery/jquery-1.10.2.min.js') }}
{{ HTML::script('static/vendor/jquery/jquery-migrate-1.2.1.min.js') }}
{{ HTML::script('static/vendor/bootstrap/js/bootstrap.min.js') }}
{{ HTML::script('static/vendor/placeholder/jquery.placeholder.js') }}
{{ HTML::script('static/js/app.js') }}

@section('scripts')

@show

@include('parts.notification')

</body>
</html>
