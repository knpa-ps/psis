@extends('layouts.master')


@section('content')

<div class="row">
	<div class="col-xs-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<div class="panel-title"><b>조직도</b></div>
			</div>
			<div class="panel-body">
				<div id="dept_tree">
					
				</div>
			</div>	
		</div>
	</div>
	<div class="col-xs-8">
		<div class="panel panel-default">
			<div class="panel-body">
					
			</div>
		</div>
	</div>
</div>

@stop
@section('styles')
@stop
@section('scripts')
<script type="text/javascript">
$(function() {

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
			success: function(id) {
				data.instance.set_id(data.node, id);
				data.instance.edit(data.node);
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
				url: "{{ url('equips/categories') }}",
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
});
</script>
@stop

