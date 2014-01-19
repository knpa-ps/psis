@extends('layouts.master')

@section('style')
    {{ HTML::style('assets/css/register.css') }}
@stop

@section('content')
    {{ Form::open(array('action'=>'AuthController@doRegister', 'method'=>'post', 'class'=>'form-signup panel', 'role'=>'form')) }}
        <h2 class="form-signup-heading">@lang('strings.register')</h2>
        <br>
        @if (isset($messages) && !empty($messages))
        <div class="alert alert-danger">
            @foreach ($messages as $m)
                <p>{{ $m }}</p>
            @endforeach
        </div>
        @endif
        {{ Form::text('account_name', $accountName, array('class'=>'form-control',
                                        'placeholder'=>Lang::get('labels.login_account_name'),
                                        'required'=>'',
                                        'autofocus'=>'' )) }}
        <p class="help-block">@lang('strings.account_name_help')</p>

        {{ Form::password('password', array('class'=>'form-control first',
                                                'placeholder'=>Lang::get('labels.login_password'),
                                                'required'=>'')) }}
        {{ Form::password('password_confirmation', array('class'=>'form-control last',
                                                'placeholder'=>Lang::get('labels.password_confirmation'),
                                                'required'=>'')) }}
        <p class="help-block">@lang('strings.password_help')</p>

        {{ Form::label('user_rank', Lang::get('labels.user_rank'), array('class'=>'control-label') ) }}
        {{ Form::select('user_rank', $codeSelectItems, $userRank, array(
            'class'=>'form-control'
        )) }}
        <br>
        {{ Form::text('user_name', $userName, array('class'=>'form-control',
                                                'placeholder'=>Lang::get('labels.user_name'),
                                                'required'=>'')) }}
        <br>
        <div class="input-group">
            {{ Form::text('department', $departmentName, array('class'=>'form-control',
                                                    'placeholder'=>Lang::get('labels.department'),
                                                    'required'=>'',
                                                    'readonly'=>'',)) }}
            <span class="input-group-btn">
                <button class="btn btn-default" type="button" id="dept-search"
                onclick="popup('{{action('DepartmentController@showDeptTree')}}', '', 500, 800)">@lang('strings.search')</button>
            </span>
        </div>
        <br>
        {{ Form::hidden('department_id', $departmentId) }}
        {{ Form::button(Lang::get('strings.register'), array('class'=>'btn btn-lg btn-primary btn-block', 'type'=>'submit')) }}
    {{ Form::close() }}
@stop

@section('script')
<script type="text/javascript">
    $(document).ready(function(){ 
        @if (isset($message) && $message)
            alert('{{$message}}');
        @endif

    }); 
    function setDept(deptId, deptName) {
        $("input[name='department']").val(deptName);
        $("input[name='department_id']").val(deptId);
    }
</script>
@stop