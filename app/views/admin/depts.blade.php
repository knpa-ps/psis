@extends('layouts.master')

@section('content')

<div class="row-fluid">
	<div class="span4 panel panel-default" id="tree_container">
		<div class="panel-heading">
			<h4>조직도</h4>
		</div>
		<div class="panel-body">
			<div id="dept_tree">
				<ul>
					<li>Root 1
					<ul>
						<li>Child 1</li>
						<li>Child 2</li>
						<li>Child 3</li>
						<li>Child 4</li>
						<li>Child 5</li>
					</ul></li>
					<li>Root 2</li>
					<li>Root 3</li>
				</ul>
			</div>
		</div>
	</div>
	<div class="span8 panel panel-default" id="info_container">
		<div class="panel-heading">
			<h4>관서 정보</h4>
		</div>
		<div class="panel-body">
			<div class="row-fluid">
				<div class="span12">
					<table class="table table-hover">
						<tbody>
							<tr>
								<th>관서명</th>
								<td><a href="#" id="dept_name"></a></td>
							</tr>
							<tr>
								<th>전체 관서명</th>
								<td><span id="dept_full_name"></span></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

			<div class="row-fluid">
				<div class="span6">
					<h4>구성원</h4>
				</div>
				<div class="span6">
					<button id="move_users" class="btn btn-small pull-right btn-primary"><i class="icon-share-alt"></i> 부서이동</button>
				</div>
			</div>

			<div class="row-fluid">
				<div class="span12">
					<table class="table table-striped table-condensed table-bordered" id="dept_users_table">
						<thead>
							<tr>
								<th>계급</th>
								<th>이름</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

@stop

@section('styles')
{{ HTML::style('assets/css/jquery.treeview.css') }}
{{ HTML::style('static/vendor/bootstrap-editable/css/bootstrap-editable.css') }}
{{ HTML::style('static/vendor/jstree/themes/default/style.min.css')  }}
<style type="text/css" media="screen">
#move_users {
	margin-bottom: 10px;
}

</style>
@stop

@section('scripts')
{{ HTML::script('static/js/jquery.dataTables.min.js') }}
{{ HTML::script('static/js/psis/dataTables.plugins.js') }}
{{ HTML::script('static/vendor/bootstrap-editable/js/bootstrap-editable.min.js') }}
{{ HTML::script('static/vendor/jstree/jstree.min.js') }}

<script type="text/javascript">
$.fn.editable.defaults.mode = 'inline';
$(function(){
	var oTable = $("#dept_users_table").dataTable($.extend(dtOptions,{
			"bFilter": false,
			"bLengthChange": false,
			"sDom": "t<'row-fluid'<'span12'i>><'row-fluid'<'span12'<'pull-right'p>>>",
		}));

	$("#dept_tree").jstree({ "plugins" : [ "themes", "html_data" ] });

});
</script>
@stop