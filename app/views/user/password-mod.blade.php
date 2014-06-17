@extends('layouts.master')

@section('content')
	<div class="col-xs-12">
		<h1 class="page-header">비밀번호 변경 <small>{{$user->account_name or ''}}</small></h1>
		{{ Form::open(array('method' => 'post',
							'action'=>'UserController@passwordMod',
							'class'=>'form-horizontal well',
							'role'=>'form',
							'id'=>'mod_form')) }}
			<fieldset>
				<legend>비밀번호 변경</legend>
				<p class="help-block">계정의 비밀번호를 수정합니다.</p>
				{{ Form::hidden( $user->account_name, null, array('id'=>'account_name')) }}
				<div class="form-group">
					<label for="existing_pw" class="col-xs-2 control-label"><b>현재 비밀번호</b></label>
					<div class="col-xs-4">
						{{ Form::password('existing_pw', array('class'=>'form-control', 'id'=>'existing_pw')) }}
					</div>
				</div>
				<div class="form-group">
					<label for="new_pw" class="col-xs-2 control-label"><b>새 비밀번호</b></label>
					<div class="col-xs-4">
						{{ Form::password('new_pw', array('class'=>'form-control','id'=>'new_pw'))}}
					</div>
				</div>
				<div class="form-group">
					<label for="new_pw_conf" class="col-xs-2 control-label"><b>새 비밀번호 확인</b></label>
					<div class="col-xs-4">
						{{ Form::password('new_pw_conf', array('class'=>'form-control','id'=>'new_pw_conf'))}}
					</div>
				</div>
				<div class="form-group">
					<div class="col-xs-offset-2 col-xs-4">
						<button type="submit" class="btn btn-primary btn-xs">비밀번호 변경</button>
					</div>
				</div>
			</fieldset>
		{{ Form::close() }}
	</div>
@stop

@section('scripts')
{{ HTML::script('static/vendor/validate/jquery.validate.min.js') }}
{{ HTML::script('static/vendor/validate/additional-methods.min.js') }}
{{ HTML::script('static/vendor/validate/messages_ko.js') }}

<script type="text/javascript">
$(function(){ 
    $("#mod_form").validate({
        rules: {
            existing_pw: {
                required: true,
                rangelength: [8, 255]
            },
            new_pw: {
                required: true,
                rangelength: [8, 255]
            },
            new_pw_conf: {
            	equalTo : "#new_pw"
            }
        }
    });
});
</script>
@stop