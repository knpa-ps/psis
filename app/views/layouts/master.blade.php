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
<div class="modal hide" id="change-password-modal">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h3>@lang('strings.change_password')</h3>
    </div>
    <form class="form-horizontal form-modal" id="change-password-form" novalidate>
    <div class="modal-body">
            <fieldset>
                <div class="control-group">
                    <label class="control-label" for="old-password">@lang('strings.old_password')</label>
                    <div class="controls">
                        <input type="password" required class="input-xlarge" name="old_password">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="password">@lang('strings.login_password')</label>
                    <div class="controls">
                        <input type="password" required class="input-xlarge" name="password" minlength="8">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="password-confirmation">@lang('strings.password_confirmation')</label>
                    <div class="controls">
                        <input type="password" required class="input-xlarge" name="password_confirmation" data-validation-passwordagain>
                    </div>
                </div>
            </fieldset>
    </div>
    <div class="modal-footer">
        <button type="submit" id="change-password-submit" class="btn btn-primary">@lang('strings.ok')</button>
        <button type="button" class="btn">@lang('strings.cancel')</button>
    </div>
    </form>
</div>
    <!-- jQuery -->
    {{ HTML::script('static/js/jquery-1.10.2.min.js') }}
    {{ HTML::script('static/js/jquery-migrate-1.2.1.min.js') }}
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
    {{ HTML::script('static/js/chosen.jquery.min.js') }}
    <!-- checkbox, radio, and file input styler -->
    {{ HTML::script('static/js/jquery.uniform.min.js') }}
    <!-- history.js for cross-browser state change on ajax -->
    {{ HTML::script('static/js/jquery.history.js') }}
    {{ HTML::script('static/js/jquery.noty.js') }}  
    {{ HTML::script('static/js/jquery.placeholder.js') }}  
    {{ HTML::script('static/js/bootbox.min.js') }}  
    {{ HTML::script('static/js/psis/app.js') }}

    {{HTML::script('static/js/jqBootstrapValidation.js')}}

    <script type="text/javascript">
        $("#change-password-modal input,select,textarea").not("[type=submit]").jqBootstrapValidation(); 
        var baseUrl = '{{ url() }}/';
        @foreach ($notifications as $noty)
            noty({{ json_encode($noty) }});
        @endforeach
        $("#change-password-form").submit(function(){
            var params = $("#change-password-form").serializeArray();
            $.ajax({
                url: "{{ action('UserController@changePassword') }}",
                type: 'post',
                data: params,
                success: function(result) {
                    var msg = "";
                    switch(parseInt(result)) {

                        case 0:
                            $("#change-password-modal").modal('hide');
                            msg = "@lang('strings.password_changed')";
                            $("#change-password-form input").val("");
                            break;
                        case -1:
                            msg = "@lang('strings.invalid_parameters')";
                            break;
                        case -2:
                            msg = "@lang('strings.login_wrong_password')";
                            break;

                    }
                    bootbox.alert(msg);
                }
            });
            return false;
        });
    </script>

@section('scripts')

@show
</body>
</html>
