@extends('layouts.master')

@section('styles')

@stop
@section('content')
<div class="row">
	<div class="col-xs-6">
		<div class="panel panel-default" id="dept_tree_panel">
			<div class="panel-heading">
				<div class="panel-title"><b>조직도</b></div>
			</div>
			<div class="panel-body">
				<div class="btn-group toolbar-table">
					<button type="button" class="btn btn-primary btn-xs" id="ah">전체관서계층조정</button>
					<button type="button" class="btn btn-info btn-xs" id="ap">전체관서정렬순서조정</button>
				</div>
				<div id="dept_tree">
					
				</div>
			</div>
		</div>
	</div>
	<div class="col-xs-6">
		<div class="panel panel-default" id="detail_panel">
			<div class="panel-heading">
				<div class="panel-title"><b>세부정보</b></div>
			</div>
			<div class="panel-body">
				<form id="mod_detail" class="form-horizontal" role='form'>
					<fieldset>
							
						<table class="table table-striped" id="detail_table">
							<tbody>
								<tr>
									<th style="width:25%;"><b>부서 이름</b></th>
									<td colspan="3" id="dept_name"></td>
								</tr>
								<tr>
									<th>full name</th>
									<td id="full_name"></td>
								</tr>
								<tr>
									<div class="form-group">
										<th>Selectable</th>
										<td style="width:45%;"><input id="selectable" type="checkbox" name="selectable" value="1"></td>
										<th>하위부서까지</th>
										<td><input type="checkbox" name="child_selectable" value="1"></td>
									</div>
								</tr>
								<tr>
									<div class="form-group">
										<th>type_code</th>
										<td>
											<div class="select-type">
												{{ Form::select('type_code', $typeCodes, null , array('id'=>'type_code')) }}
											</div>
									    </td>
										<th>하위부서까지</th>
										<td><input type="checkbox" name="child_type" value="1"></td>
									</div>
								</tr>
								<tr>
									<th>is_alive</th>
									<td colspan="3" id="is_alive"></td>
								</tr>
							</tbody>
						</table>
						<input type="button" value="저장" class="btn btn-primary btn-xs pull-right" id="save_detail">
					</fieldset>
				</form>
				<br>
				<div id="user_table">
					{{ View::make('datatable.template', array(
						'id'=>'users_table',
						'columns'=>array('이름', '연락처'),
						'class'=>'multi-selectable'
					)) }}
				</div>

			</div>
		</div>
	</div>
</div>
@stop

@section('scripts')
<!-- Load Datatable Plugin -->
{{ HTML::script('static/vendor/datatables/js/jquery.dataTables.min.js') }}
{{ HTML::script('static/vendor/datatables/js/jquery.dataTables.plugins.js') }}
<!-- Load jstree Plugin -->
{{ HTML::style('static/vendor/jstree/themes/default/style.min.css') }}
{{ HTML::script('static/vendor/jstree/jstree.min.js') }}

<script type="text/javascript">
$(function() {
	var selected_id = null;

	$("#save_detail").on('click', function(){
		if(!selected_id){
			alert("수정할 부서를 선택하세요");
			return;
		}

		var params = $("#mod_detail").serializeArray();
		params.push({ 'name' : 'id', 'value' : selected_id });

		$.ajax({
			url: base_url+"/admin/depts/update",
			type : "post",
			data : params,
			success : function(){
				alert('변경사항이 저장되었습니다.');
			}
		});
	})
	$("#detail_table").on('select.dept-selector', function(e, data){
		$("#dept_name").text(data.dept_name);
		$("#full_name").text(data.full_name);
		if(data.selectable==1){
			$("#selectable").attr('checked', true);
		}
		else{
			$("#selectable").attr('checked', false);	
		}
		$("div.select-type select").val(data.type_code);
		$("#is_alive").text(data.is_alive);

	});
	$("#dept_tree")
	.on('activate_node.jstree', function(e, data) {

		// fire select event!
		selected_id = data.node.id;
		var id = { 'id' : selected_id };

		$.ajax({
			url : base_url+"/admin/depts/data",
			type : "post",
			data : id,
			success : function(response){
				$("#detail_table").trigger('select.dept-selector', response);
			}
		});
		
	})
	.on('move_node.jstree', function(e, data) {

		var params = { 'id' : data.node.id, 'parent_id' : data.parent, 'position' : data.position };

		$.ajax({
			url: base_url+"/admin/depts/move",
			type: "post",
			data: params,
			error: function() {
				data.instance.refresh();
			}
		});
	})
	.on('create_node.jstree', function(e, data) {
		//DB에 추가하기
		$.ajax({
			url : base_url+"/admin/depts/create",
			type:"post",
			data: 	{ 'parent_id' : data.parent ,
					  'name' : data.node.text
					},
			success: function(d) {
				data.instance.set_id(data.node, d.id);
			}, 
			error: function() {
				data.instance.refresh();
			}
		});
	})
	.on('rename_node.jstree', function(e, data) {
		$.ajax({
			url : base_url+"/admin/depts/rename",
			type : "post",
			data :  { 'id' : data.node.id,
					  'name' : data.node.text
					}
		})
	})
	.on('delete_node.jstree', function (e, data) {

		$.ajax({
			url: base_url+"/admin/depts/delete",
			type: "post",
			data: { id: data.node.id },
			error: function() {
				data.instance.refresh();
			}
		});
	})
	.jstree({
		core: {
			animation: 0,
			check_callback: true,
			themes: { stripes: true },
			data: {
				url: "{{ url('ajax/dept_tree') }}",
				data: function (node) {
					return { id: node.id }
				}
			}
		},
		plugins: [ "dnd", "wholerow", "contextmenu" ],
		contextmenu: {
			items: function (o, cb) { // Could be an object directly
				var items = {
					"create" : {
						"separator_before"	: false,
						"separator_after"	: true,
						"_disabled"			: false, //(this.check("create_node", data.reference, {}, "last")),
						"icon"				: "glyphicon glyphicon-plus",
						"label"				: "부서 추가",
						"action"			: function (data) {
							var inst = $.jstree.reference(data.reference),
								obj = inst.get_node(data.reference);
							inst.create_node(obj, {}, "last", function (new_node) {
								setTimeout(function () { inst.edit(new_node);},0);
							});
						}
					}
				};

				items.remove = {
					"separator_before"	: false,
					"icon"				: "glyphicon glyphicon-remove",
					"separator_after"	: false,
					"_disabled"			: false, //(this.check("delete_node", data.reference, this.get_parent(data.reference), "")),
					"label"				: "삭제",
					"action"			: function (data) {
						var inst = $.jstree.reference(data.reference),
							obj = inst.get_node(data.reference);
						if(inst.is_selected(obj)) {
							inst.delete_node(inst.get_selected());
						}
						else {
							inst.delete_node(obj);
						}
					}
				};
				items.rename = {
					"separator_before"	: false,
					"separator_after"	: false,
					"_disabled"			: false,
					"label"				: "이름변경",
					"icon"				: "glyphicon glyphicon-edit",
					"action"			: function (data) {
						var inst = $.jstree.reference(data.reference),
							obj = inst.get_node(data.reference);
						inst.edit(obj);
					}
				};
				return items;
			}
		}
	});

	$("#ah").click(function() {
		$.ajax({
			url: base_url+"/admin/depts/adjust-hierarchy",
			beforeSend: function() {
				$("body").modalmanager('loading');
			},
			success: function(res) {
				alert(res);
			},
			complete: function () {
				$("body").modalmanager('loading');	
			}
		});
	});

	$("#ap").click(function() {
		$.ajax({
			url: base_url+"/admin/depts/adjust-positions",
			beforeSend: function() {
				$("body").modalmanager('loading');
			},
			success: function(res) {
				alert(res);
			},
			complete: function () {
				$("body").modalmanager('loading');	
			}
		});

	});
});
</script>
@stop