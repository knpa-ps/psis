<!DOCTYPE html>
<html lang="kr">
<head>
    @include('parts.header')
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

        <noscript>
            <div class="alert alert-block span12">
                <h4 class="alert-heading">@lang('strings.warning')</h4>
                <p>@lang('strings.no_js')</p>
            </div>
        </noscript>

        <!-- content starts -->
            @include('parts.breadcrumbs')
            @section('content')
            @show
         <!-- content ends -->
        </div><!--/#content.span10-->

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

<script type="text/javascript">
    var baseUrl = '{{ url() }}/';
</script>
    <!-- jQuery -->
    {{ HTML::script('static/js/jquery-1.7.2.min.js') }}
    <!-- jQuery UI -->
    {{ HTML::script('static/js/jquery-ui-1.8.21.custom.min.js') }}
    <!-- transition / effect library -->
    {{ HTML::script('static/js/bootstrap-transition.js') }}
    <!-- alert enhancer library -->
    {{ HTML::script('static/js/bootstrap-alert.js') }}
    <!-- modal / dialog library -->
    {{ HTML::script('static/js/bootstrap-modal.js') }}
    <!-- custom dropdown library -->
    {{ HTML::script('static/js/bootstrap-dropdown.js') }}
    <!-- library for creating tabs -->
    {{ HTML::script('static/js/bootstrap-tab.js') }}
    <!-- library for advanced tooltip -->
    {{ HTML::script('static/js/bootstrap-tooltip.js') }}
    <!-- popover effect library -->
    {{ HTML::script('static/js/bootstrap-popover.js') }}
    <!-- button enhancer library -->
    {{ HTML::script('static/js/bootstrap-button.js') }}
    <!-- library for cookie management -->
    {{ HTML::script('static/js/jquery.cookie.js') }}
    <!-- select or dropdown enhancer -->
    {{ HTML::script('static/js/jquery.chosen.min.js') }}
    <!-- checkbox, radio, and file input styler -->
    {{ HTML::script('static/js/jquery.uniform.min.js') }}
    <!-- history.js for cross-browser state change on ajax -->
    {{ HTML::script('static/js/jquery.history.js') }}
    {{ HTML::script('static/js/jquery.placeholder.js') }}  
    {{ HTML::script('static/js/jquery.validate.min.js') }}  
    {{ HTML::script('static/js/bootbox.min.js') }}  
    {{ HTML::script('static/js/psis/app.js') }}
@section('scripts')

@show
</body>
</html>
