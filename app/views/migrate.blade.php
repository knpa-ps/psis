@extends('layouts.public-master')

@section('content')
<div class="row">
	<div class="col-xs-8 col-xs-offset-2">
		<div class="panel panel-default">
			<div class="panel-heading">
				<div class="panel-title">
					<strong>유저 정보 수정</strong>
				</div>
			</div>

			<div class="panel-body">
				<p>경비경찰포탈 업데이트로 인해 사용자 정보를 업데이트 하셔야 합니다. 다음 항목들을 입력 후 진행해주세요.</p>

				<form method="POST" class="form-horizontal" id="migrate_form">
					<div class="form-group">
						<label for="dept_id" class="control-label col-xs-3">관서</label>
						<div class="col-xs-9">
							{{ View::make('widget.dept-selector', array('id'=>'dept_id')) }}
							<p class="help-block">조직도에 해당되는 관서가 없다면 한단계 위의 소속 관서를 선택해주세요.</p>
						</div>
					</div>
					
					<p>지방청 소속 분임관리자(해당 지방청 사용자관리 권한)분들만 분임관리자로 체크해주세요.</p>
                    <div class="form-group col-xs-6">
                        <label class="col-xs-4 control-label">
                            @lang('auth.lb_guard_news')
                        </label>
                        <div class="col-xs-8">
                            <div class="radio">
                                <label>
                                    <input type="radio" value="none" name="groups[report]" checked>
                                    @lang('auth.lb_not_in_use')
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" value="user" name="groups[report]">
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
                                    <input type="radio" value="none" name="groups[budget]" checked>
                                    @lang('auth.lb_not_in_use')
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" value="user" name="groups[budget]">
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
						<button type="submit" class="btn btn-primary btn-block btn-lg">제출</button>
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
    $("#migrate_form").validate({
        rules: {
            dept_id_display: {
                required: true
            }
        }
    });
}); 
</script>
@stop