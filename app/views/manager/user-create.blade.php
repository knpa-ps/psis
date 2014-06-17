@extends('layouts.master')


@section('styles')
<style>
    #signup_form fieldset {
        margin-bottom: 20px;
    }
</style>
@stop

@section('content')

<div class="row">
    <div class="col-xs-12">
        <div class="panel panel-default" id="register_panel">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <strong>회원 추가</strong>
                </h4>
            </div>
            <div class="panel-body">
                {{ Form::open(array('url'=>url('manager/users'), 
                                    'method'=>'post',
                                    'id'=>'modify_userdata_form', 
                                    'role'=>'form',
                                    'class'=>'form-horizontal',
                                    'novalidate'=>'')) }}

                <fieldset>
                    <legend><h5><span class="glyphicon glyphicon-user"></span>
                        @lang('auth.lb_basicinfo')
                    </h5></legend>
                    
                    <div class="row">
                        <!-- 계정, 계급/이름 -->
                        <div class="form-group col-xs-6">
                            <label for="account_name" class="col-xs-4 control-label">
                                @lang('auth.lb_account')
                            </label>
                            <div class="col-xs-8">
                                {{ Form::text('account_name', $form['account_name'], array(
                                    'class'=>'form-control',
                                    'id'=>'account_name',
                                )) }}

                            </div>
                        </div>
                        
                        <div class="form-group col-xs-6">
                            <label for="user_name" class="col-xs-4 control-label">
                                @lang('auth.lb_rankname')
                            </label>
                            <div class="col-xs-4">
                                {{ Form::select('user_rank', $userRanks, $form['user_rank'], array(
                                    'class'=>'form-control',
                                    'id'=>'user_rank'
                                )) }}
                            </div>
                            <div class="col-xs-4">
                                {{ Form::text('user_name', $form['user_name'], array(
                                    'class'=>'form-control',
                                    'id'=>'user_name'
                                )) }}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- 비밀번호, 비밀번호 확인 -->
                        <div class="form-group col-xs-6">
                            <label for="password" class="col-xs-4 control-label">
                                @lang('auth.lb_password')
                            </label>
                            <div class="col-xs-8">
                                {{ Form::password('password', array(
                                    'class'=>'form-control',
                                    'id'=>'password'
                                )) }}
                            </div>
                        </div>

                        <div class="form-group col-xs-6">
                            <label for="account_name" class="col-xs-4 control-label">
                                @lang('auth.lb_password_confirm')
                            </label>
                            <div class="col-xs-8">
                               {{ Form::password('password_confirmation', array(
                                    'class'=>'form-control',
                                    'id'=>'password_confirmation'
                                )) }}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- 부서 -->
                        <div class="form-group col-xs-6">
                            <label for="dept_id" class="col-xs-4 control-label">
                                @lang('auth.lb_office')
                            </label>
                            <div class="col-xs-8">
                                {{ View::make('widget.dept-selector', array('id'=>'dept_id'))->with('mngDeptId', $mngDeptId) }}
                            </div>
                            
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend><h5><span class="glyphicon glyphicon-phone"></span>
                        @lang('auth.lb_contact')
                    </h5></legend>

                    <!-- 연락처 -->
                    <div class="form-group col-xs-4">
                        <label for="contact" class="col-xs-4 control-label">
                            @lang('auth.lb_general')
                        </label>
                        <div class="col-xs-8">
                            {{ Form::text('contact', $form['contact'], array(
                                'class'=>'form-control',
                                'id'=>'contact'
                            )) }}
                        </div>
                    </div>

                    <div class="form-group col-xs-4">
                        <label for="contact_extension" class="col-xs-4 control-label">
                            @lang('auth.lb_guard')
                        </label>
                        <div class="col-xs-8">
                            {{ Form::text('contact_extension', $form['contact_extension'], array(
                                'class'=>'form-control',
                                'id'=>'contact_extension'
                            )) }}
                        </div>
                    </div>

                    <div class="form-group col-xs-4">
                        <label for="contact_phone" class="col-xs-4 control-label">
                            @lang('auth.lb_cellphone')
                        </label>
                        <div class="col-xs-8">
                            {{ Form::text('contact_phone', $form['contact_phone'], array(
                                'class'=>'form-control',
                                'id'=>'contact_phone'
                            )) }}
                        </div>
                    </div>                    

                </fieldset> 
                <fieldset>
                    <legend><h5><span class="glyphicon glyphicon-tasks"></span> 
                        @lang('auth.lb_system_purpose') </h5></legend>

                    <div class="form-group col-xs-6">
                        <label class="col-xs-4 control-label">
                            @lang('auth.lb_guard_news')
                        </label>
                        <div class="col-xs-8">
                            <div class="radio">
                                <label>
                                    <input type="radio" value="none" name="groups[report]">
                                    @lang('auth.lb_not_in_use')
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" value="user" name="groups[report]" checked>
                                    @lang('auth.lb_general_user')
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" value="admin" name="groups[report]">
                                    @lang('auth.lb_division_manager')
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-xs-6">
                        <label class="col-xs-4 control-label">
                            @lang('auth.lb_guard_budget_manage')
                        </label>
                        <div class="col-xs-8">
                            <div class="radio">
                                <label>
                                    <input type="radio" value="none" name="groups[budget]">
                                    @lang('auth.lb_not_in_use')
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" value="user" name="groups[budget]" checked>
                                    @lang('auth.lb_general_user')
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" value="admin" name="groups[budget]">
                                    @lang('auth.lb_division_manager')
                                </label>
                            </div>
                        </div>
                    </div>

                </fieldset> 
                <fieldset>
                    <legend><h5><span class="glyphicon glyphicon-off"></span> 
                        계정 활성화 상태</h5></legend>

                    <div class="form-group col-xs-6">
                        <label class="col-xs-4 control-label">
                            계정 상태
                        </label>
                        <div class="col-xs-8">
                            <div class="radio">
                                <label>
                                    <input type="radio" value="1" name="status" checked>
                                    활성화
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" value="0" name="status">
                                    비활성화
                                </label>
                            </div>
                        </div>
                    </div>

                </fieldset> 
                
                <button type="submit" class="btn btn-lg btn-block btn-primary">
                    저장하기
                </button>

                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
@stop

@section('scripts')
{{ HTML::script('static/vendor/validate/jquery.validate.min.js') }}
{{ HTML::script('static/vendor/validate/additional-methods.min.js') }}
{{ HTML::script('static/vendor/validate/messages_ko.js') }}

<script type="text/javascript">
$(function(){ 
    $("#modify_userdata_form").validate({
        rules: {
            account_name: {
                required: true,
                rangelength: [4, 255],
                alphanumeric: true
            },
            user_name: {
                required: true,
                maxlength: 10
            },
            password: {
                required: true,
                rangelength: [8, 255]
            },
            password_confirmation: {
                equalTo: "#password"
            },
            dept_id_display: {
                required: true
            },
            contact: "required",
            contact_extension: "required",
            contact_phone: "required"

        }
    });
}); 
</script>
@stop