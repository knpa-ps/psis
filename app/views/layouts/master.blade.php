<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    @include('parts.header')

    @section('style')
    @show
</head>
<body>

@section('navbar')
@show

@section('sidebar')
@show

<div class="container">
	@section('content-header')
	@show
    @yield('content')
    @section('content-footer')
	@show
</div>

{{ HTML::script('assets/js/vendor/jquery-1.10.1.min.js') }}
{{ HTML::script('assets/js/vendor/bootstrap.min.js') }}
{{ HTML::script('assets/js/vendor/modernizr-2.6.2.min.js') }}
{{ HTML::script('assets/js/vendor/jquery.placeholder.js') }}
{{ HTML::script('assets/js/common.js') }}
{{ HTML::script('assets/js/plugins.js') }}

@section('footer')
@show

@section('script')

@show
</body>
</html>
