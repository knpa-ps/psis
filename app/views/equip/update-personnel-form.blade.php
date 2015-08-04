@extends('layouts.base')

@section('body')
	<div class="col-xs-12" style="margin-top: 15px;">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<b>현원 / 정원 수정</b>
				</h3>
			</div>
			<div class="panel-body">
				 {{ Form::open(array(
						'id'=>'personnel_form',
						'class'=>'form-horizontal'
					)) }}
						<fieldset>
							<div class="form-group">
								<label for="personnel" class="control-label col-xs-2">현원</label>
								<div class="col-xs-10">
									<input type="number" class="form-control input-sm" name="personnel" id="personnel"
									value="{{ $node->personnel }}" min="0">
								</div>
							</div>
							<div class="form-group">
								<label for="capacity" class="control-label col-xs-2">정원</label>
								<div class="col-xs-10">
									<input type="number" class="form-control input-sm" name="capacity" id="capacity"
									value="{{ $node->capacity }}" min="0">
								</div>
							</div>
						</fieldset>
				{{ Form::close(); }}
				<button id="submit_personnel" class="btn btn-sm btn-block btn-primary">제출</button>
			</div>
		</div>
	</div>	
@stop
@section('scripts')
<script type="text/javascript">
$(function(){

	$("#submit_personnel").on('click', function() {
		var formData = $("#personnel_form").serialize();

		$.ajax({
			url : base_url+"/equips/update_personnel",
			data : formData,
			type : "post",
			success: function(res){
				alert(res.msg);
				window.close();
				window.opener.location.reload();
			}
		});
	});
});
</script>
@stop