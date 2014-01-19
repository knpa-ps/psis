@extends('layouts.master')

@section('style')
    {{ HTML::style('assets/css/login.css') }}
@stop

@section('content')
    {{ Form::open(array('action'=>'AuthController@doLogin', 'method'=>'post', 'class'=>'form-signin well', 'role'=>'form')) }}
        <h2 class="form-signin-heading">@lang('strings.site_title')</h2>
        <br>
        {{ Form::text('account_name', '', array('class'=>'form-control',
                                        'placeholder'=>Lang::get('labels.login_account_name'),
                                        'required'=>'',
                                        'autofocus'=>'' )) }}
        {{ Form::password('password', array('class'=>'form-control',
                                                'placeholder'=>Lang::get('labels.login_password'),
                                                'required'=>'')) }}
        <label class="checkbox">
            {{ Form::checkbox('remember', '1') }} @lang('labels.login_remember')
        </label>

        {{ Form::button(Lang::get('strings.login'), array('class'=>'btn btn-lg btn-primary btn-block', 'type'=>'submit')) }}
        <a href="{{ action('AuthController@showRegisterForm') }}" class="btn btn-lg btn-info btn-block">
            @lang('strings.register')
        </a>
    {{ Form::close() }}
@stop
@section('script')
<script type="text/javascript">
    $(document).ready(function(){ 
        @if (isset($message) && $message)
            alert('{{$message}}');
        @endif
    }); 
</script>
@stop