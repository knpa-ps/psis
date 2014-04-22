<!DOCTYPE html>
<html lang="kr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title> 
        @lang('global.t_portal') 
        :: 
        {{ $title or '' }}
    </title>
    
    {{ HTML::style('static/vendor/bootstrap/css/bootstrap-yeti.min.css') }}
    {{ HTML::style('static/css/app.css') }}
    {{ HTML::style('static/vendor/bootstrap-modal/css/bootstrap-modal-bs3patch.css') }}
    {{ HTML::style('static/vendor/bootstrap-modal/css/bootstrap-modal.css') }}
    
    <!--[if lt IE 9]>
        {{ HTML::script('static/vendor/html5shiv/html5shiv.js') }}
        {{ HTML::script('static/vendor/respond/respond.min.js') }}
    <![endif]-->

    <link rel="shortcut icon" href="{{ url('static/img/favicon.ico') }}?v=2">

    {{-- page-specific stylesheets --}}
    @section('styles')
    @show
    
    {{-- 페이지 중간에 사용될 스크립트들을 위해서 jquery는 먼저 include 한다. --}}
    {{ HTML::script('static/vendor/jquery/jquery-1.10.2.min.js') }}
    {{ HTML::script('static/vendor/jquery/jquery-migrate-1.2.1.min.js') }}
</head>
<body>

{{-- bootstrap-modal 플러그인을 위한 wrapper --}}
<div class="page-container">

    @include('parts.header')

    <div class="container" id="container">
        <div class="row">

            <div class="col-xs-2">
                @include('parts.sidebar')
                @section('sidebar')
                @show
            </div>

            <div class="col-xs-10" id="content">
                @yield('content')
            </div>
            
        </div>
    </div>

    @include('parts.footer')

    <div id="ajax_modal" class="modal fade" tabindex="-1" style="display: none;"></div>
</div>

{{-- vendor scripts --}}
{{ HTML::script('static/vendor/bootstrap/js/bootstrap.min.js') }}
{{ HTML::script('static/vendor/placeholder/jquery.placeholder.js') }}
{{ HTML::script('static/vendor/bootstrap-modal/js/bootstrap-modal.js') }}
{{ HTML::script('static/vendor/bootstrap-modal/js/bootstrap-modalmanager.js') }}
<script type="text/javascript">
    var base_url = "{{ url('/') }}";
</script>

{{-- application global script --}}
{{ HTML::script('static/js/app.js') }}

<script type="text/javascript">
</script>
{{-- page-specific scripts --}}
@section('scripts')
@show

@include('parts.notification')

</body>
</html>
