@extends('layouts.master')

@section('content')
	<div class="col-xs-12">
		<h1 class="page-header">사용자 프로필 <small>{{$user->account_name or ''}}</small></h1>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title pull-left">
					<strong>기본정보</strong>
				</h3>
				<div class="clearfix"></div>
			</div>
			<div class="panel-body">
				<div class="btn-toolbar toolbar-table" role="toolbar">
					<div class="btn-group pull-right">
						<a href="{{ action('UserController@displayProfileEdit')}}" class="btn btn-primary btn-xs">
							<span class="glyphicon glyphicon-edit"></span> 변경신청
						</a>
					</div>		
				</div>
				<table class="table table-hover table-striped">
					<tbody>
						<tr>
							<th>계정</th>
							<td colspan="3">{{ $user->account_name }}</td>
						</tr>
						<tr>
							<th>계급</th>
							<td>{{ $user->rank->title }}</td>
							<th>이름</th>
							<td>{{$user->user_name}}</td>
						</tr>
						<tr>
							<th>관서</th>
							<td>{{ $user->department->full_name}} </td>
							<th>가입일시</th>
							<td>{{ $user->created_at }}</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title pull-left">
					<strong>연락처</strong>
				</h3>
				<div class="clearfix"></div>
			</div>
			<div class="panel-body">
				<div class="btn-toolbar" role="toolbar">
					<div class="btn-group pull-right">
						<button id="contact_mod_btn" class="btn btn-primary btn-xs">
							<span class="glyphicon glyphicon-edit"></span> 저장
						</button>
					</div>		
				</div>
				<div class="form-group col-xs-10">
					<form id="contact_mod" class="form-horizontal" role="form">
						<fieldset>
							<table class="table table-hover table-striped">
								<tbody>
									<tr>
										<th class="col-xs-3">일반</th>
										<td>
											<input class="form-control input-sm" id="contact" name="contact" type="text" value="{{$user->contact or ''}}">
										</td>
									</tr>
									<tr>
										<th>경비</th>
										<td>
											<input class="form-control input-sm" id="contact_extension" name="contact_extension" type="text" value="{{$user->contact_extension or ''}}">
										</td>
									</tr>
									<tr>
										<th>핸드폰</th>
										<td>
											<input class="form-control input-sm" id="contact_phone" name="contact_phone" type="text" value="{{$user->contact_phone or ''}}">
										</td>
									</tr>
								</tbody>
							</table>
						</fieldset>
					</form>
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
			$('#contact_mod').validate({
				rules : {
					contact: {
						required:true,
					},
					contact_extension: {
						required:true,
					},
					contact_phone: {
						required:true,
					}

				}
			});
		});
		$(function(){
			$('#contact_mod_btn').click(function(){
				var params = $("#contact_mod").serializeArray();
				$.ajax({
					url: base_url+"/user/contact_mod",
					type: "post",
					data: params,
					success: function(){
						alert('연락처 변경 완료');
					},
					error: function(){
						alert('연락처 변경 실패');
					}
				});
			});
		});
		$(function(){
			$('#group_mod').click(function(){
				alert('foo');
			});
		});
	</script>
@stop