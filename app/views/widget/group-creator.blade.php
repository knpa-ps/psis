<div class="modal-header">
	<button class="close" type="button" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3><b>그룹 생성</b></h3>
</div>
<div class="modal-body">
	{{ Form::open(array('method' => 'post',
						'action'=>'AdminController@createUserGroup',
						'class'=>'form-horizontal',
						'role'=>'form',
						'id'=>'newgroup')) }}
		<div>
		<fieldset>
			<div class="form-group">
				<label class="control-label col-xs-2" for="groupName"><b>이름</b></label>	
				<div class="col-xs-10">
					<input type="text" class="form-control input-sm" name="groupName" id="groupName">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-2" for="key"><b>권한 키</b></label>	
				<div class="col-xs-10">
					<input type="text" class="form-control input-sm" name="key" id="key">
				</div>
			</div>
			<div class="form-group">
				<div class="col-xs-offset-2 col-xs-10 ">
					<button type="submit" class="btn btn-primary btn-xs pull-right" id="save">저장하기</button>
				</div>
			</div>
		</fieldset>
		</div>
	{{ Form::close() }}
</div>