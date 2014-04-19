<!DOCTYPE html>
<html lang="kr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>
        @lang('strings.site_title') :: {{ $title }}  
    </title>

    <!-- The styles -->
    {{ HTML::style('static/vendor/bootstrap/css/bootstrap.min.css') }}
    {{ HTML::style('static/css/app.css') }}
    <link rel="shortcut icon" type="image/x-icon" href="{{ url('static/img/favicon.ico') }}">
    <!-- The HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
        {{ HTML::script('static/vendor/html5shiv/html5shiv.js') }}
        {{ HTML::script('static/vendor/respond/respond.min.js') }}
    <![endif]-->

    @section('styles')
    @show
</head>
<body>

@include('parts.navbar')    

<div class="container-fluid">
    <div class="row-fluid">

        @include('parts.sidebar')
            
        @if ($GLOBALS['showSidebar'])
            <div id="content" class="span10">
        @else
            <div id="content" class="span12">
        @endif

        <!-- content starts -->
            @section('content')
            @show
         <!-- content ends -->
        </div><!--/#content-->

    </div><!--/fluid-row-->
                
        
</div><!--/.fluid-container-->

<hr>

<footer class="footer">
    <div class="container">
        <p class="pull-left">
            @lang('strings.footer_text')
        </p>
    </div>
</footer>

{{ HTML::script('static/vendor/jquery/jquery-1.10.2.min.js') }}
{{ HTML::script('static/vendor/jquery/jquery-migrate-1.2.1.min.js') }}
{{ HTML::script('static/vendor/bootstrap/js/bootstrap.min.js') }}
{{ HTML::script('static/js/app.js') }}

@section('scripts')
@show
</body>
</html>
