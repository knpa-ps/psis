@extends('layouts.master')

@section('content')
<div class="row">
	<div class="col-xs-6">
		<div class="panel panel-default" id="dept_tree_panel">
			<div class="panel-heading">
				<div class="panel-title"><b>장비 관리자 조직도</b></div>
			</div>
			<div class="panel-body">
				<div id="node_tree">
				</div>
			</div>
		</div>
	</div>
	<div class="col-xs-6">
		<div class="panel panel-default" id="detail_panel">
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
									<td id="manager_name"></td>
								</tr>
								<tr>
									<th>ID</th>
									<td id="user_account"></td>
								</tr>
								<tr>
									<th>경비전화</th>
									<td id="guard_phone"></td>
								</tr>
								<tr>
									<th>일반전화</th>
									<td id="normal_phone"></td>	
								</tr>
								<tr>
									<th>휴대전화</th>
									<td id="cellphone"></td>
								</tr>
								<tr>
									<th>관리자변경일</th>
									<td id="manager_date"></td>
								</tr>
								<tr>
									<th>현원/정원</th>
									<td id="personnel"></td>
								</tr>
							</tbody>
						</table>
						<!-- 선택한 부서 id -->
						<input type="text" class="hidden" id="node_id">
						<input type="button" value="관리자 변경" class="btn btn-primary btn-xs pull-right" id="change_manager">
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
	$("#node_tree")
	.on('activate_node.jstree', function(e, data) {

		if (!data.node.li_attr["data-selectable"]) {
			$("#node_tree").jstree('toggle_node', data.node);
			return;
		}

		$("#node_id").val(data.node.id);

		// TODO
		// 우측 표에 ajax로 데이터를 넣는다
		$.ajax({
			url: base_url+"/manager/sems",
			data: { nodeId: data.node.id },
			type: "post",
			success: function(res){
				if (res == "") {
					$("#manager_name").text("없음");
					$("#user_account").empty();
					$("#guard_phone").empty();
					$("#normal_phone").empty();
					$("#cellphone").empty();
					$("#manager_date").empty();
					$("#personnel").empty();
				} else {
					$("#manager_name").text(res.user_name);
					$("#user_account").text(res.account_name);
					$("#guard_phone").text(res.contact_extension);
					$("#normal_phone").text(res.contact);
					$("#cellphone").text(res.contact_phone);
					$("#manager_date").text(res.last_manager_changed_date);
					$("#personnel").text(res.personnel+'/'+res.capacity);
				};
			}
		});

	})
	.jstree({
		core: {
			animation: 0,
			check_callback: true,
			themes: { stripes: true },
			data: {
				url: "{{ url('ajax/supply_node_tree') }}",
				data: function (node) {
					return { id: node.id, initId: {{$id}} <?php echo (isset($mngDeptId))? ',mngDeptId :'.$mngDeptId : '' ?> };
				}
			}
		},

		plugins: [ "wholerow" ]
	});

	$("#change_manager").click(function() {
		var node_id = $("#node_id").val();
		if (!node_id) {
			alert('먼저 부서를 선택해주세요.');
			return;
		}

		$("body").modalmanager('loading');
		var $modal = $("#ajax_modal");
		$modal.load(base_url+"/manager/sems/users/show", { node_id: node_id},  function() {
			$modal.modal({
				modalOverflow: true
			});
		});	
	});

	$("#ajax_modal").on("users_added.psis", function() {
		var node_id = $("#node_id").val();
		$.ajax({
			url: base_url+"/manager/sems",
			data: { nodeId: node_id },
			type: "post",
			success: function(res){
				if (res == "") {
					$("#manager_name").text("없음");
					$("#user_account").empty();
					$("#guard_phone").empty();
					$("#normal_phone").empty();
					$("#cellphone").empty();
					$("#manager_date").empty();
					$("#personnel").empty();
				} else {
					$("#manager_name").text(res.user_name);
					$("#user_account").text(res.account_name);
					$("#guard_phone").text(res.contact_extension);
					$("#normal_phone").text(res.contact);
					$("#cellphone").text(res.contact_phone);
					$("#manager_date").text(res.last_manager_changed_date);
					$("#personnel").text(res.personnel+'/'+res.capacity);
				};
			}
		});
	})

})
</script>
@stop