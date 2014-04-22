@extends('layouts.master')

@section('content')
	<div class="col-xs-12">
		<p>
			<h1>사용자 프로필 <small>{{$user->user_name or ''}}</small></h1>
		</p>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title pull-left">
					<strong>기본정보</strong>
				</h3>
				<div class="clearfix"></div>
			</div>
			<div class="panel-body">
				<div class="btn-toolbar" role="toolbar">
					<div class="btn-group pull-right">
						<a href="{{ URL::action('UserController@showProfileEdit')}}" class="btn btn-primary btn-sm">
							<span class="glyphicon glyphicon-edit"></span>변경신청
						</a>
					</div>		
				</div>
				<p>
					<dl class="dl-horizontal">
					  <dt>이름</dt>
					  <dd>{{$user->user_name or ''}}</dd>
					  <dt>계급</dt>
					  <dd>{{$user->rank->title or ''}}</dd>
					  <dt>관서</dt>
					  <dd>{{$user->department->full_name or ''}}</dd>
					</dl>
				</p>
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
						<button id="contact_mod_btn" class="btn btn-primary btn-sm">
							<span class="glyphicon glyphicon-edit"></span>변경신청
						</button>
					</div>		
				</div>
				<div class="form-group col-xs-10">
					<form id="contact_mod" class="form-horizontal" role="form">
						<fieldset>
							<div class="form-group col-xs-4">
		                        <label for="contact" class="col-xs-4 control-label">
		                            일반
		                        </label>
		                        <div class="col-xs-8">
		                            <input class="form-control input-sm" id="contact" name="contact" type="text" value="{{$user->contact or ''}}">
		                        </div>
		                    </div>

		                    <div class="form-group col-xs-4">
		                        <label for="contact_extension" class="col-xs-4 control-label">
		                            경비
		                        </label>
		                        <div class="col-xs-8">
		                            <input class="form-control input-sm" id="contact_extension" name="contact_extension" type="text" value="{{$user->contact_extension or ''}}">
		                        </div>
		                    </div>

		                    <div class="form-group col-xs-4">
		                        <label for="contact_phone" class="col-xs-4 control-label">
		                            핸드폰
		                        </label>
		                        <div class="col-xs-8">
		                            <input class="form-control input-sm" id="contact_phone" name="contact_phone" type="text" value="{{$user->contact_phone or ''}}">
		                        </div>
		                    </div>
						</fieldset>
					</form>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title pull-left">
					<strong>권한그룹</strong>
				</h3>
				<div class="clearfix"></div>
			</div>
			<div class="panel-body">
				<div class="btn-toolbar" role="toolbar">
					<div class="btn-group pull-right">
						<button id="group_mod" class="btn btn-primary btn-sm">
							<span class="glyphicon glyphicon-edit"></span>변경신청
						</button>
					</div>		
				</div>
				<ul class="list-group col-xs-4">
			   		@foreach($groups as $group)
			  			<li class="list-group-item">
			  				{{$group->name}}
			  			</li> 
			  		@endforeach
				</ul>
			</div>
		</div>
	</div>
@stop

@section('scripts')
	<script type="text/javascript">
		$(function(){
			$('#contact_mod_btn').click(function(){
				var params = $("#contact_mod").serializeArray();
				$.ajax({
					url: base_url+"user/contact_mod",
					type: "post",
					data: params,
					success: function(){
						bootbox.alert('연락처 변경 완료');
					},
					error: function(){
						bootbox.alert('연락처 변경 실패');
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