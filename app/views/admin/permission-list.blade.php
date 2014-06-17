<form class="form-horizontal" id="permissions">
	<input type="hidden" value="{{ $group->id }}" name="group_id">
	<div class="form-group">

		<?php $groupPermissions = $group->getPermissions(); ?>

		@foreach ($permissions as $perm)
			<div class="checkbox col-xs-10 col-xs-offset-2">
				<label>
					<input type="checkbox" value="{{ $perm->key }}" 
					name="permission_keys[]"
					{{ isset($groupPermissions[$perm->key])&&$groupPermissions[$perm->key]==1?'checked="checked"':'' }} >
					{{ $perm->description }}
				</label>
			</div>
		@endforeach
	</div>


	<div class="form-group">
		<div class="col-xs-10 col-xs-offset-2">
			<button type="submit" class="btn btn-primary btn-xs">저장</button>
		</div>
	</div>
</form>

<script type="text/javascript">
$(function(){
	$('#permissions').submit(function(){

		var params = $(this).serializeArray();

		$.ajax({
			url : base_url+"/admin/permission/save",
			type : 'post',
			data : params,
			success : function(res){
				alert(res);
			}	
		});

		return false;
	});
});
</script>