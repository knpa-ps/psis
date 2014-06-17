<div class="modal-header" >
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3>사용자 선택</h3>
</div>
<div class="modal-body">
	<div class="btn-toolbar toolbar-table">
		<div class="btn-group pull-right">
			<button class="btn btn-primary btn-xs" id="user_add_submit">
				<span class="glyphicon glyphicon-check"></span> 추가
			</button>
		</div>
		<div class="btn-group pull-right">
			<button data-target="all_users_table" class="btn btn-default btn-xs select-all" data-toggle="button">
				<span class="glyphicon glyphicon-unchecked"></span> 전체선택
			</button>
		</div>
	</div>
	{{ View::make('datatable.template', array(
		'id'=>'all_users_table',
		'columns'=>array( 'ID', '이름', '관서'),
		'class'=>'multi-selectable'
	)) }}
</div>

<script type="text/javascript">
	$(function(){
		var usersTable = $("#all_users_table").dataTable(dt_get_options({
			"bStateSave": false,
			"sAjaxSource": base_url+"/admin/groups/users/all?group_id={{$groupId}}"
		}));
		// 선택된 유저들을 그 그룹에 저장한다
		$("#user_add_submit").click(function() {
			var ids = fnGetSelectedIds(usersTable, 0);
			var params = {};
			params['group_id'] = "{{ $groupId }}";
			params['users'] = ids;
			$.ajax({
				url : base_url+"/admin/groups/users/add",
				type : 'post',
				data : params,
				success : function(response) {
					alert(response);
					$('#ajax_modal').modal("hide");
					$('#ajax_modal').trigger('users_added.psis');
				}
			});
		});
	});
</script>