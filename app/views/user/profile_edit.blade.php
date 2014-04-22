@extends('layouts.master')

@section('content')
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title pull-left">
					<strong>기본정보 수정</strong> 
				</h3>
				<div class="clearfix"></div>
			</div>
			<div class="panel-body">
				<form class="form-horizontal" role="form">
					<fieldset>
						<div class="form-group">
							<label for="name_mod" class="col-xs-2 control-label">이름</label>
							<div class="col-xs-4">
								<input type="text" id="name_mod" class="form-control input-sm">
							</div>
						</div>
						<div class="form-group">
							<label for="group_mod" class="col-xs-2 control-label">계급</label>
							<div class="col-xs-4">
								{{ Form::select('user_rank', $userRanks, null, array(
                                    'class'=>'form-control',
                                    'id'=>'user_rank'
                                )) }}
							</div>
						</div>
						<div class="form-group">
							<label for="office_mod" class="col-xs-2 control-label">관서</label>
							<div class="col-xs-4">
								{{ View::make('widget.dept-selector', array('id'=>'dept_id')) }}
							</div>
						</div>
						<div class="form-group">
							<div class="col-xs-4 col-xs-offset-2">
								<input type="button" value="저장" class="btn btn-primary btn-sm" id="save">
							</div>
						</div>
					</fieldset>
				</form>
			</div>
		</div>
	</div>
@stop