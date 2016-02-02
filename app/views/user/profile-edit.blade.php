@extends('layouts.master')

@section('content')
	<div class="col-xs-12">
		<h1 class="page-header">사용자 프로필 <small>{{$user->account_name or ''}}</small></h1>
		<form class="form-horizontal well" role="form" id="mod_form">
			<fieldset>
				<legend>기본정보 수정</legend>
				<p class="help-block">계정의 기본정보를 수정합니다. 수정 내역은 해당 지방청 분임관리자의 승인 후 반영됩니다.</p>
				<div class="form-group">
					<label for="user_name" class="col-xs-2 control-label">이름</label>
					<div class="col-xs-4">
						{{ Form::text('user_name', $user->user_name, array('class'=>'form-control'))}}
					</div>
				</div>
				<div class="form-group">
					<label for="user_rank" class="col-xs-2 control-label">계급</label>
					<div class="col-xs-4">
						{{ Form::select('user_rank', $userRanks, $user->rank->code, array(
                            'class'=>'form-control',
                            'id'=>'user_rank'
                        )) }}
					</div>
				</div>
				<div class="form-group">
					<label for="dept_id" class="col-xs-2 control-label">관서</label>
					<div class="col-xs-4">
						{{ View::make('widget.dept-selector', array('id'=>'dept_id', 'default'=>$user->department)) }}
					</div>
				</div>
				<div class="form-group">
					<div class="col-xs-4 col-xs-offset-2">
						<button type="button" class="btn btn-primary btn-xs" id="save_request">변경신청</button>
					</div>
				</div>
			</fieldset>
		</form>
		</div>
	</div>
@stop

@section('scripts')
<script>
	$(function(){
		$('#save_request').click(function(){
			var params = $('#mod_form').serializeArray();
			$.ajax({
				url: base_url+"/user/general_mod",
				type: "post",
				data: params,
				success: function(){
					alert('기본정보 변경 신청되었습니다. 관리자의 승인 후 적용됩니다.');
					return redirect(base_url+'/user/profile');
				},
				error: function(){
					alert('변경신청 실패!');
				}
			});
		});
	});
</script>
@stop
