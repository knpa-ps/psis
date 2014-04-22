@extends('layouts.master')

@section('content')

<div class="row">
	<div class="col-xs-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<strong>사용자 그룹 목록</strong>
				</h3>
			</div>
			<div class="panel-body">
				<div class="btn-toolbar toolbar-table" role="toolbar">
					<div class="btn-group pull-right">
						<button id="create_group_btn" class="btn btn-primary btn-xs">
							<span class="glyphicon glyphicon-plus"></span> 그룹생성
						</button>
					</div>
				</div>

				{{ View::make('datatable.template', array(
					'id'=>'groups_table',
					'columns'=>array( 'ID', '이름', '권한 키', '사용자 수' )
				)) }}
			    
			</div>
		</div>
	</div>
	<div class="col-xs-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><strong>구성원 편집</strong></h3>
			</div>
			<div class="panel-body">
				<div class="btn-toolbar toolbar-table">
					<div class="btn-group pull-right">
						<button id="remove_users_btn" class="btn btn-danger btn-xs">
							<span class="glyphicon glyphicon-remove"></span> 구성원 제거
						</button>
						<button id="add_users_btn" class="btn btn-primary btn-xs">
							<span class="glyphicon glyphicon-plus"></span> 구성원 추가
						</button>
					</div>
					<div class="btn-group pull-right">
						<button data-target="users_table" class="btn btn-default btn-xs select-all" data-toggle="button">
							<span class="glyphicon glyphicon-unchecked"></span> 전체선택
						</button>
					</div>
				</div>
				<input type="hidden" id="selected_group_id">
				{{ View::make('datatable.template', array(
					'id'=>'users_table',
					'class'=> 'multi-selectable',
					'columns'=>array('ID', '이름', '관서')
				)) }}
			</div>
		</div>		
	</div>
</div>

@stop

@section('scripts')
{{ HTML::script('static/vendor/datatables/js/jquery.dataTables.min.js') }}
{{ HTML::script('static/vendor/datatables/js/jquery.dataTables.plugins.js') }}
<script type="text/javascript">
var usersTable = null;

$(function () { 
	var groupsTable = $("#groups_table").dataTable(dt_get_options({
		"sAjaxSource": base_url+"/admin/groups/data",
		"bServerSide": true,
		"aoColumnDefs": [ {
	      "aTargets": [ 1 ],
	      "mRender": function ( data, type, full ) {

	        return '<a href="#" class="show-detail" data-id="'+full[0]+'">'+data+'</a>';
	      }
	    } ]
	}));

	groupsTable.on('click', '.show-detail', function() {
		var group_id = $(this).data('id');
		$("#selected_group_id").val(group_id);
		reload_users(group_id);
	});

	usersTable = $("#users_table").dataTable(dt_get_options());

	$("#add_users_btn").click(function() {
		var group_id = $("#selected_group_id").val();
		if (!group_id) {
			alert('먼저 그룹을 선택해주세요');
			return;
		}

		$("body").modalmanager('loading');
		var $modal = $("#ajax_modal");
		$modal.load(base_url+"/admin/groups/users", null, function() {
			$modal.modal({
				modalOverflow: true
			});
		});
	});

	$("#remove_users_btn").click(function() {
		var group_id = $("#selected_group_id").val();
		if (!group_id) {
			alert('먼저 그룹을 선택해주세요');
			return;
		}

		var ids = fnGetSelectedIds(usersTable, 0);
		if (ids.length == 0) {
			alert('선택된 유저가 없습니다.');
			return;
		}

		$.ajax({
			url: base_url+"/admin/groups/delete?group_id="+group_id,
			type: "post",
			contentType: 'application/json; charset=utf-8',
			dataType: 'json',
			data: JSON.stringify(ids),
			success: function(response) {
				
				if (response.result == 0) {
					groupsTable.fnDraw();
					reload_users(group_id);
				}

				alert(response.message);
			}
		});
	});
});
function reload_users(group_id) {
	if (usersTable != null)
		usersTable.fnReloadAjax(base_url+"/admin/groups/users/data?group="+group_id);
}
</script>
@stop