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
						<th>&nbsp</th>
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

	})
	$("#dept_tree")
	.on('activate_node.jstree', function(e, data) {

		if (!data.node.li_attr["data-selectable"]) {
			$("#dept_tree").jstree('toggle_node', data.node);
			return;
		}

		var extras = {
			dept_id: data.node.id,
			dept_name: data.node.text,
			full_name: data.node.li_attr["data-full-name"],
			selectable : data.node.li_attr["data-selectable"]
		};
		// fire select event!
		$("#detail_table").trigger('select.dept-selector', [ extras ]);
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
		plugins: [ "dnd", "wholerow" ]
	});
});
</script>
@stop