@extends('layouts.master')

@section('content')
<div class="row">
	<div class="col-xs-6">
		<div class="panel panel-default" id="menu_tree_panel">
			<div class="panel-heading">
				<h3 class="panel-title"><strong>메뉴 구조</strong></h3>
			</div>
			<div class="panel-body">
				<div id="menu_tree"></div>
			</div>
		</div>
	</div>
	<div class="col-xs-6">
		<div class="panel panel-default" id="menu_info_panel">
			<div class="panel-heading">
				<h3 class="panel-title"><strong>상세정보</strong></h3>
			</div>
			<div class="panel-body">
				<form id="menu_form" class="form-horizontal" role="form" novalidate>
					<fieldset>
						<legend><span id="menu_name"></span></legend>
						<input type="hidden" id="menu_id" name="menu_id">
                        <div class="form-group">
                            <label for="browser_title" class="col-xs-4 control-label">브라우저 제목</label>
                            <div class="col-xs-8">
                                <input type="text" class="form-control input-sm" name="browser_title" id="browser_title">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="url" class="col-xs-4 control-label">링크(url)</label>
                            <div class="col-xs-8">
                                <input type="text" class="form-control input-sm" name="url" id="url">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="url" class="col-xs-4 control-label">접근권한</label>
                            <div class="col-xs-8">
                           		@foreach ($groups as $group)
									<div class="checkbox col-xs-6">
	                                	<label>
	                                		<input type="checkbox" name="group_ids[]" value="{{$group->id}}" id="gp_{{ $group->id }}" class="group-ids"> 
	                                		{{ $group->name }}
	                                	</label>
	                                </div>	
                           		@endforeach
                                
                            </div>
                        </div>
						<div class="form-group">
							<div class="col-xs-8 col-xs-offset-4">
								<input type="button" value="저장" class="btn btn-primary btn-sm" id="menu_form_submit">
							</div>
						</div>
					</fieldset>
				</form>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts')

{{ HTML::style('static/vendor/jstree/themes/default/style.min.css') }}
{{ HTML::script('static/vendor/jstree/jstree.min.js') }}

<script type="text/javascript">
$(function() {

	$("#menu_tree")
	.on('delete_node.jstree', function (e, data) {
		$.ajax({
			url: "{{ url('menu') }}/"+data.node.id,
			type: "DELETE",
			error: function() {
				data.instance.refresh();
			}
		});
	})
	.on('create_node.jstree', function (e, data) {

		var path = $("#menu_tree").jstree('get_path', data.node, false, true);
		
		var type = path[0];

		var params = { 
			'parent_id' : data.node.parent, 
			'position' : data.position, 
			'text' : data.node.text,
			'type' : type
		};

		$.ajax({
			url: "{{ url('menu') }}",
			type: "post",
			data: params,
			success: function(d) {
				data.instance.set_id(data.node, d.id);
			}, 
			error: function() {
				data.instance.refresh();
			}
		});
	})
	.on('rename_node.jstree', function (e, data) {
		var params = { 'text' : data.text };
		$.ajax({
			url: "{{ url('menu') }}/"+data.node.id+"?operation=rename",
			type: "put",
			data: params,
			error: function() {
				data.instance.refresh();
			}
		});
	})
	.on('move_node.jstree', function (e, data) {
		var path = $("#menu_tree").jstree('get_path', data.node, false, true);
		
		var type = path[0];

		var params = { 'id' : data.node.id, 'parent_id' : data.parent, 'position' : data.position, 'type':type };

		$.ajax({
			url: "{{ url('menu') }}/"+data.node.id+"?operation=move",
			type: "put",
			data: params,
			error: function() {
				data.instance.refresh();
			}
		});
	})
	.on('activate_node.jstree', function(e, data) {
		$.ajax({
			url: "{{ url('menu') }}/"+data.node.id,
			type: "get",
			success: function(d) {
				display_menu(d);
			}
		});
	})
	.jstree({
		core: {
			animation: 0,
			check_callback: true,
			themes: { stripes: true },
			data: {
				url: "{{ url('menu') }}"
			}
		},
		contextmenu: {
			items: function (o, cb) { // Could be an object directly

				var items = {
					"create" : {
						"separator_before"	: false,
						"separator_after"	: true,
						"_disabled"			: false, //(this.check("create_node", data.reference, {}, "last")),
						"icon"				: "glyphicon glyphicon-plus",
						"label"				: "메뉴 추가",
						"action"			: function (data) {
							var inst = $.jstree.reference(data.reference),
								obj = inst.get_node(data.reference);
							inst.create_node(obj, {}, "last", function (new_node) {
								setTimeout(function () { inst.edit(new_node); },0);
							});
						}
					}
				};

				if (o.parent != '#') {
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
				}

				return items;
			}
		},
		plugins: [ "dnd", "wholerow", "contextmenu" ]
	});

	$("#menu_form_submit").on('click', function() {
		var params = $("#menu_form").serializeArray();
		var menuId = $("#menu_id").val();
		if (!menuId) {
			alert('먼저 수정할 메뉴를 선택해주세요');
			return;
		}
		$.ajax({
			url: "{{ url('menu') }}/"+menuId+"?operation=edit",
			type: "put",
			data: params,
			success: function(response) {
				alert(response);
			}
		});
	});
});
function display_menu(menu) {
	$("#menu_name").text(menu.name);
	$("#menu_id").val(menu.id);
	$("#browser_title").val(menu.browser_title);
	$("#url").val(menu.url);
	var groupIds = menu.group_ids.split(',');
	$(".group-ids").each(function() {
		var groupId = $(this).val();
		$(this).prop('checked', $.inArray(groupId, groupIds) >= 0);
	});
}
</script>
@stop