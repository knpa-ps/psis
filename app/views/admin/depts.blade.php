@extends('layouts.master')

@section('styles')
<style>
#detail_table th, td{
	width : 50%;
}
</style>

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
				<table class="table table-striped" id="detail_table">
					<tr>
						<th><b>부서 이름</b></th>
						<td id="dept_name"></td>
					</tr>
					<tr>
						<th>full name</th>
						<td id="full_name"></td>
					</tr>
					<tr>
						<th>Selectable</th>
						<td id="selectable"></td>
					</tr>
					<tr>
						<th>type_code</th>
						<td id="type_code"></td>
					</tr>
					<tr>
						<th>is_alive</th>
						<td id="is_alive"></td>
					</tr>
				</table>
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
	$("#detail_table").on('select.dept-selector', function(e, data){
		$("#dept_name").text(data.dept_name);
		$("#full_name").text(data.full_name);
		$("#selectable").text(data.selectable);

	});
	$("#dept_tree")
	.on('activate_node.jstree', function(e, data) {

		// TODO : 일요일에 여기서부터
		// fire select event!
		$("#detail_table").trigger('select.dept-selector', [ extras ]);
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

	})
	.on('rename_node.jstree', function(e, data) {

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
								setTimeout(function () { inst.edit(new_node); },0);
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