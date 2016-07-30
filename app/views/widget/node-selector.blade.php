<div class="modal-header" >
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3>사용자 선택</h3>
</div>
<div class="modal-body">
	<div class="btn-toolbar toolbar-table">
		<div class="btn-group pull-right">
			<button class="btn btn-primary btn-xs" id="user_add_submit">
				<span class="glyphicon glyphicon-check"></span> 변경
			</button>
		</div>
	</div>
	{{ View::make('datatable.template', array(
		'id'=>'all_users_table',
		'columns'=>array( 'ID', '이름', '계정', '소속'),
		'class'=>'single-selectable'
	)) }}
</div>

<script type="text/javascript">
	$(function(){
		var usersTable = $("#all_users_table").dataTable(dt_get_options({
			"bStateSave": false,
			"sAjaxSource": base_url+"/manager/sems/users"
		}));
		// 선택된 유저들을 그 그룹에 저장한다
		$("#user_add_submit").click(function() {
			var id = fnGetSelectedIds(usersTable, 0);
			var params = {};
			params['userId'] = id;
			params['nodeId'] = "{{ $nodeId }}";
			params['managerId'] = "{{ $managerId }}";
			$.ajax({
				url : base_url+"/manager/sems/users/change_node_manager",
				type : 'post',
				data : params,
				success : function(res) {
					alert(res.msg);
					if (res.code==1) {
						$('#ajax_modal').modal("hide");
						$('#ajax_modal').trigger('users_added.psis');	
					};
				}
			});
		});
	});
</script>