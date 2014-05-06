@extends('layouts.master')

@section('content')

<div class="row">
	<div class="col-xs-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<strong>사용자 목록</strong>
				</h3>
			</div>
			<div class="panel-body">
				{{ View::make('datatable.template', array(
						'id'=>'users_table',
						'class' => 'single-selectable',
						'columns'=>array('ID','계정', '이름', '관서')
				)) }}		
			</div>
		</div>
	</div>
	<div class="col-xs-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<strong>사용자 정보</strong>
				</h3>
			</div>
			<div class="panel-body">
				<table class="table table-hover table-striped">
					<tbody>
						<tr>
							<th>계정 활성화</th>
							<td colspan=3>
								<input type="checkbox" name="activate">
							</td>
						</tr>
						<tr>
							<th>계정</th>
							<td>[아이디]</td>
						</tr>
						<tr>
							<th>계급</th>
							<td>[계급]</td>
							<th>이름</th>
							<td>[이름]</td>
						</tr>
						<tr>
							<th>관서</th>
							<td>[관서명]</td>
							<th>가입일시</th>
							<td>[가입날짜]</td>
						</tr>
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
<script type="text/javascript">
$(function(){

	var usersTable = $("#users_table").dataTable(dt_get_options({
		"sAjaxSource": base_url+"/manage/data",
		"bServerSide": true
	}));

	$("#users_table tbody").on('click', 'tr td', function() {
		var id = $(this).parent().find('td').eq(0).text();
		alert(id);
	});

});
</script>
@stop