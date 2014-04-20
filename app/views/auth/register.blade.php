@extends('layouts.public-master')

@section('styles')
<style>
    #signup_form fieldset {
        margin-bottom: 20px;
    }
</style>
@stop

@section('content')

<div class="row">
    <div class="col-xs-8 col-xs-offset-2">
        <div class="panel panel-default" id="register_panel">
            <div class="panel-heading">
                <h4 class="panel-title">회원가입</h4>
            </div>
            <div class="panel-body">
                {{ Form::open(array('action'=>'AuthController@doRegister', 
                                    'method'=>'post', 
                                    'id'=>'signup_form', 
                                    'role'=>'form',
                                    'class'=>'form-horizontal',
                                    'novalidate'=>'')) }}

                <fieldset>
                    <legend><h5><span class="glyphicon glyphicon-user"></span> 기본정보</h5></legend>
                    
                    <div class="row">
                        <!-- 계정, 계급/이름 -->
                        <div class="form-group col-xs-6">
                            <label for="account_name" class="col-xs-4 control-label">계정</label>
                            <div class="col-xs-8">
                                
                                {{ Form::text('account_name', $form['account_name'], array(
                                    'class'=>'form-control',
                                    'id'=>'account_name',
                                    'autofocus'=>''
                                )) }}

                            </div>
                        </div>
                        
                        <div class="form-group col-xs-6">
                            <label for="user_name" class="col-xs-4 control-label">계급/이름</label>
                            <div class="col-xs-4">
                                {{ Form::select('user_rank', $userRanks, null, array(
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
                            <label for="password" class="col-xs-4 control-label">비밀번호</label>
                            <div class="col-xs-8">
                                {{ Form::password('password', array(
                                    'class'=>'form-control',
                                    'id'=>'password'
                                )) }}
                            </div>
                        </div>

                        <div class="form-group col-xs-6">
                            <label for="account_name" class="col-xs-4 control-label">비밀번호 확인</label>
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
                            <label for="dept_id" class="col-xs-4 control-label">관서</label>
                            <div class="col-xs-8">
                                {{ View::make('widget.dept-selector', array('id'=>'dept_id')) }}
                            </div>
                            
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend><h5><span class="glyphicon glyphicon-phone"></span> 연락처</h5></legend>

                    <!-- 연락처 -->
                    <div class="form-group col-xs-4">
                        <label for="contact" class="col-xs-4 control-label">일반</label>
                        <div class="col-xs-8">
                            {{ Form::text('contact', $form['contact'], array(
                                'class'=>'form-control',
                                'id'=>'contact'
                            )) }}
                        </div>
                    </div>

                    <div class="form-group col-xs-4">
                        <label for="contact_extension" class="col-xs-4 control-label">경비</label>
                        <div class="col-xs-8">
                            {{ Form::text('contact_extension', $form['contact_extension'], array(
                                'class'=>'form-control',
                                'id'=>'contact_extension'
                            )) }}
                        </div>
                    </div>

                    <div class="form-group col-xs-4">
                        <label for="contact_phone" class="col-xs-4 control-label">핸드폰</label>
                        <div class="col-xs-8">
                            {{ Form::text('contact_phone', $form['contact_phone'], array(
                                'class'=>'form-control',
                                'id'=>'contact_phone'
                            )) }}
                        </div>
                    </div>                    

                </fieldset> 

                <fieldset>
                    <legend><h5><span class="glyphicon glyphicon-tasks"></span> 시스템 사용목적
                    <small>사용할 시스템을 선택해주세요. 해당 지방청 관리자 또는 본청 관리자의 승인 후 사용하실 수 있습니다.</small> </h5></legend>

                    <div class="form-group col-xs-6">
                        <label class="col-xs-4 control-label">경비속보</label>
                        <div class="col-xs-8">
                            <div class="radio">
                                <label>
                                    <input type="radio" value="none" name="groups[report]" checked> 미사용
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" value="user" name="groups[report]"> 일반 사용자
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" value="admin" name="groups[report]"> 분임관리자
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-xs-6">
                        <label class="col-xs-4 control-label">경비예산관리</label>
                        <div class="col-xs-8">
                            <div class="radio">
                                <label>
                                    <input type="radio" value="none" name="groups[budget]" checked> 미사용
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" value="user" name="groups[budget]"> 일반 사용자
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" value="admin" name="groups[budget]"> 분임관리자
                                </label>
                            </div>
                        </div>
                    </div>

                </fieldset> 

                <input type="submit" value="가입신청" class="btn btn-lg btn-block btn-primary">

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
    $("#signup_form").validate({
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