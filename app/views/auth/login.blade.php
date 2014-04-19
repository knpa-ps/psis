@extends('layouts.public-master')

@section('content')

<div class="row">
    <div class="col-xs-12" id="logo_container">
        <img src="{{ url('static/img/login_logo.png') }}" alt="logo" height="400">
    </div>
</div>

<div class="row" id="content_container">
    <div class="col-xs-4 col-xs-offset-2">
        <div class="panel panel-default" id="login_panel">
            <div class="panel-heading">
                <h3 class="panel-title"> @lang('auth.t_portal') </h3>
            </div>
            <div class="panel-body">
                {{ Form::open(array(
                        'url' => action('AuthController@doLogin'),
                        'method' => 'POST',
                        'id' => 'login_form'
                    )) }}
                    <fieldset>
                        <div class="form-group">
                            {{ Form::text('account', '', array(
                                'autofocus'=>'autofocus',
                                'minlength'=>4,
                                'maxlength'=>255,
                                'required'=>'required',
                                'placeholder'=>Lang::get('auth.lb_account'),
                                'class'=>'form-control'
                            )) }}
                        </div>
                        
                        <div class="form-group">
                            {{ Form::password('password', array(
                                'minlength'=>8,
                                'maxlength'=>255,
                                'required'=>'required',
                                'placeholder'=>Lang::get('auth.lb_password'),
                                'class'=>'form-control'
                            )) }}
                        </div>

                        <div class="checkbox">
                            <label>
                                {{ Form::checkbox('remember', '1') }} @lang('auth.lb_remember_me')
                            </label>
                        </div>
                        
                        {{ Form::submit(Lang::get('auth.btn_login'), array('class'=>'btn btn-lg btn-success btn-block')) }}

                        <a id="register_btn" 
                        href="{{ action('AuthController@showRegistrationForm') }}" 
                        class="btn btn-lg btn-info btn-block"> @lang('auth.btn_register') </a>
                    </fieldset>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <div class="col-xs-4">
        <div class="panel panel-default" id="notice_panel">
            <div class="panel-heading">
                <h3 class="panel-title pull-left"> @lang('auth.t_notice') </h3>
                <a href="{{ board_url('notice') }}" id="notice_more" class="btn btn-default btn-xs pull-right board-link">
                    @lang('global.lb_more') 
                 </a>
                <div class="clearfix"></div>
            </div>
            <div class="panel-body">
                <div id="notice_container">
                    <table id="notice_table" class="table table-condensed table-striped table-hover">
                        <thead>
                            <tr>
                                <th>
                                    @lang('auth.th_notice_title')
                                </th>
                                <th>
                                    @lang('auth.th_notice_date')
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($writes as $w)
                            <tr>
                                <td>
                                    <a href="{{ board_url('notice', $w['id']) }}" class="board-link">
                                        {{ $w['subject'] }}
                                    </a>
                                </td>
                                <td>
                                    {{ $w['created_at'] }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                
            </div>
        </div>
    </div>
</div>

@stop

@section('styles')
{{ HTML::style('static/css/login.css') }}
@stop

@section('scripts')
{{ HTML::script('static/vendor/validate/jquery.validate.min.js') }}
{{ HTML::script('static/vendor/validate/additional-methods.min.js') }}
{{ HTML::script('static/vendor/validate/messages_ko.js') }}

<script type="text/javascript">
$(function() {
    $("#login_form").validate();
});
</script>
@stop