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
				<?php $groupsTable = Datatable::table()
			    ->addColumn('id','Name')       // these are the column headings to be shown
			    ->setUrl(action('AdminController@getUserGroupsData'))   // this is the route where data will be retrieved
			    ->noScript(); ?>

			    {{ $groupsTable->render('datatable.template') }}
			</div>
		</div>
	</div>
	<div class="col-xs-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><strong>소속 사용자</strong></h3>
			</div>
			<div class="panel-body">
				<div class="btn-toolbar toolbar-table">
					<div class="btn-group pull-right">
						<button id="add_users_btn" class="btn btn-primary btn-xs">
							<span class="glyphicon glyphicon-plus"></span> 사용자 추가
						</button>
					</div>
				</div>

				<table class="table table-striped table-hover table-condensed">
					<thead>
						<tr>
							<th>ID</th>
							<th>이름</th>
							<th>관서</th>
						</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>
		</div>		
	</div>
</div>

@stop

@section('scripts')
{{ HTML::script('static/vendor/datatables/js/jquery.dataTables.min.js') }}
{{ HTML::script('static/vendor/datatables/js/jquery.dataTables.plugins.js') }}
{{ $groupsTable->script() }}
<script type="text/javascript">
$(function () { 

});
</script>
@stop