@extends('layouts.master')

@section('content')
<div class="row">
	<div class="col-xs-6">
		<div class="panel panel-default" id="dept_tree_panel">
			<div class="panel-heading">
				<div class="panel-title"><b>장비 관리자 조직도 - {{ $fullName }}</b></div>
			</div>
			<div class="panel-body">
				<div id="node_tree">
				</div>
			</div>
		</div>
	</div>
	<div class="col-xs-6">
		<div class="panel panel-default" id="detail_panel_1">
			<div class="panel-heading">
				<div class="panel-title"><b>관리자 정보</b></div>
			</div>
			<div class="panel-body">
				<form id="mod_detail" class="form-horizontal" role='form'>
					<fieldset>

						<table class="table table-striped" id="detail_table">
							<tbody>
								<tr>
									<th style="width:50%;"><b>관리자 이름</b></th>
									<td id="manager_name_1"></td>
								</tr>
								<tr>
									<th>ID</th>
									<td id="user_account_1"></td>
								</tr>
								<tr>
									<th>경비전화</th>
									<td id="guard_phone_1"></td>
								</tr>
								<tr>
									<th>일반전화</th>
									<td id="normal_phone_1"></td>
								</tr>
								<tr>
									<th>휴대전화</th>
									<td id="cellphone_1"></td>
								</tr>
							</tbody>
						</table>
						<!-- 선택한 부서 id 및 manager id-->
						<input type="text" class="hidden" id="node_id_1">
						<input type="text" class="hidden" id="manager_id_1">
						<input type="button" value="관리자 변경" class="btn btn-primary btn-xs pull-right" id="change_manager_1">
					</fieldset>
				</form>
				<br>
			</div>
		</div>

		<div class="panel panel-default" id="detail_panel_2">
			<div class="panel-heading">
				<div class="panel-title"><b>관리자 정보</b></div>
			</div>
			<div class="panel-body">
				<form id="mod_detail" class="form-horizontal" role='form'>
					<fieldset>

						<table class="table table-striped" id="detail_table">
							<tbody>
								<tr>
									<th style="width:50%;"><b>관리자 이름</b></th>
									<td id="manager_name_2"></td>
								</tr>
								<tr>
									<th>ID</th>
									<td id="user_account_2"></td>
								</tr>
								<tr>
									<th>경비전화</th>
									<td id="guard_phone_2"></td>
								</tr>
								<tr>
									<th>일반전화</th>
									<td id="normal_phone_2"></td>
								</tr>
								<tr>
									<th>휴대전화</th>
									<td id="cellphone_2"></td>
								</tr>
							</tbody>
						</table>
						<!-- 선택한 부서 id 및 manager id-->
						<input type="text" class="hidden" id="node_id_2">
						<input type="text" class="hidden" id="manager_id_2">
						<input type="button" value="관리자 변경" class="btn btn-primary btn-xs pull-right" id="change_manager_2">
					</fieldset>
				</form>
				<br>
			</div>
		</div>
		<div class="panel panel-default" id="detail_panel_3">
			<div class="panel-heading">
				<div class="panel-title"><b>관리자 정보</b></div>
			</div>
			<div class="panel-body">
				<form id="mod_detail" class="form-horizontal" role='form'>
					<fieldset>

						<table class="table table-striped" id="detail_table">
							<tbody>
								<tr>
									<th style="width:50%;"><b>관리자 이름</b></th>
									<td id="manager_name_3"></td>
								</tr>
								<tr>
									<th>ID</th>
									<td id="user_account_3"></td>
								</tr>
								<tr>
									<th>경비전화</th>
									<td id="guard_phone_3"></td>
								</tr>
								<tr>
									<th>일반전화</th>
									<td id="normal_phone_3"></td>
								</tr>
								<tr>
									<th>휴대전화</th>
									<td id="cellphone_3"></td>
								</tr>
							</tbody>
						</table>
						<!-- 선택한 부서 id 및 manager id-->
						<input type="text" class="hidden" id="node_id_3">
						<input type="text" class="hidden" id="manager_id_3">
						<input type="button" value="관리자 변경" class="btn btn-primary btn-xs pull-right" id="change_manager_3">
					</fieldset>
				</form>
				<br>
			</div>
		</div>

	</div>
</div>
@stop

@section('scripts')

<!-- Load jstree Plugin -->
{{ HTML::style('static/vendor/jstree/themes/default/style.min.css') }}
{{ HTML::script('static/vendor/jstree/jstree.min.js') }}
{{ HTML::script('static/vendor/datatables/js/jquery.dataTables.min.js') }}
{{ HTML::script('static/vendor/datatables/js/jquery.dataTables.plugins.js') }}

<script type="text/javascript">
$(function(){
	for (var i=1; i<3; i++) {
		$("#detail_panel_"+(i+1)).hide();
	}
})
$(function(){
	$("#node_tree")
	.on('activate_node.jstree', function(e, data) {

		if (!data.node.li_attr["data-selectable"]) {
			$("#node_tree").jstree('toggle_node', data.node);
			return;
		}

		$("#node_id_1").val(data.node.id);
		$("#node_id_2").val(data.node.id);
		$("#node_id_3").val(data.node.id);

		// TODO
		// 우측 표에 ajax로 데이터를 넣는다
		$.ajax({
			url: base_url+"/manager/sems",
			data: { nodeId: data.node.id },
			type: "post",
			success: function(res){
				for (var i=res.length; i<3; i++) {
					$("#detail_panel_"+(i+1)).hide();
				}
				for (var i=0; i<res.length; i++) {
					$("#detail_panel_"+(i+1)).show();
					if (res[i] == null) {
						$("#manager_id_"+(i+1)).val('');
						$("#manager_name_"+(i+1)).text("없음");
						$("#user_account_"+(i+1)).empty();
						$("#guard_phone_"+(i+1)).empty();
						$("#normal_phone_"+(i+1)).empty();
						$("#cellphone_"+(i+1)).empty();
					} else {
						$("#manager_id_"+(i+1)).val(res[i].manager_id);
						$("#manager_name_"+(i+1)).text(res[i].user_name);
						$("#user_account_"+(i+1)).text(res[i].account_name);
						$("#guard_phone_"+(i+1)).text(res[i].contact_extension);
						$("#normal_phone_"+(i+1)).text(res[i].contact);
						$("#cellphone_"+(i+1)).text(res[i].contact_phone);
					};
				}
			}
		});

	})
	$("#node_tree").jstree({
		core: {
			animation: 0,
			check_callback: true,
			themes: { stripes: true },
			data: {
				url: "{{ url('ajax/supply_node_tree') }}",
				// contentType: 'text/plain;',
				dataType: 'json',
				data: function (node) {
					return { id: node.id, initId: {{$id}} <?php echo (isset($mngDeptId))? ',mngDeptId :'.$mngDeptId : '' ?> }
				}
			}
		},

		plugins: [ "wholerow" ]
	});

	$("#change_manager_1").click(function() {
		var node_id = $("#node_id_1").val();
		var manager_id = $("#manager_id_1").val();
		if (!node_id) {
			alert('먼저 부서를 선택해주세요.');
			return;
		}

		$("body").modalmanager('loading');
		var $modal = $("#ajax_modal");
		$modal.load(base_url+"/manager/sems/users/show", { node_id: node_id, manager_id: manager_id },  function() {
			$modal.modal({
				modalOverflow: true
			});
		});
	});
	$("#change_manager_2").click(function() {
		var node_id = $("#node_id_2").val();
		var manager_id = $("#manager_id_2").val();
		if (!node_id) {
			alert('먼저 부서를 선택해주세요.');
			return;
		}

		$("body").modalmanager('loading');
		var $modal = $("#ajax_modal");
		$modal.load(base_url+"/manager/sems/users/show", { node_id: node_id, manager_id: manager_id },  function() {
			$modal.modal({
				modalOverflow: true
			});
		});
	});
	$("#change_manager_3").click(function() {
		var node_id = $("#node_id_3").val();
		var manager_id = $("#manager_id_3").val();
		if (!node_id) {
			alert('먼저 부서를 선택해주세요.');
			return;
		}

		$("body").modalmanager('loading');
		var $modal = $("#ajax_modal");
		$modal.load(base_url+"/manager/sems/users/show", { node_id: node_id, manager_id: manager_id},  function() {
			$modal.modal({
				modalOverflow: true
			});
		});
	});

	$("#ajax_modal").on("users_added.psis", function() {
		var node_id = $("#node_id_1").val(); // 대표로 맨 위만
		$.ajax({
			url: base_url+"/manager/sems",
			data: { nodeId: node_id },
			type: "post",
			success: function(res){
				for (var i=0; i<res.length; i++) {
					if (res[i] == null) {
						$("#manager_id_"+(i+1)).val('');
						$("#manager_name_"+(i+1)).text("없음");
						$("#user_account_"+(i+1)).empty();
						$("#guard_phone_"+(i+1)).empty();
						$("#normal_phone_"+(i+1)).empty();
						$("#cellphone_"+(i+1)).empty();
					} else {
						$("#manager_id_"+(i+1)).val(res[i].manager_id);
						$("#manager_name_"+(i+1)).text(res[i].user_name);
						$("#user_account_"+(i+1)).text(res[i].account_name);
						$("#guard_phone_"+(i+1)).text(res[i].contact_extension);
						$("#normal_phone_"+(i+1)).text(res[i].contact);
						$("#cellphone_"+(i+1)).text(res[i].contact_phone);
					};
				}
			}
		});
	})

})
</script>
@stop
