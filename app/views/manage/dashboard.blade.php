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
							<th style="width:25%">계정 활성화</th>
							<td colspan=3>
								<input type="checkbox" name="activated" id="activated" class="pull-left">
							</td>
						</tr>
						<tr>
							<th>계정</th>
							<td style="width:25%;" id="email"></td>
						</tr>
						<tr>
							<th>계급</th>
							<td id="rank"></td>
							<th style="width:20%;">이름</th>
							<td id="name"></td>
						</tr>
						<tr>
							<th>관서</th>
							<td colspan=3 id="dept_name"></td>
						</tr>
						<tr>
							<th>가입일시</th>
							<td colspan=3 id="created_at"></td>
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
	var selectedId = null;
	$("#activated").on("click", function(){
		var checked = $(this).attr("checked")?1:0;
		$.ajax({
			url : base_url+"/manage/activate",
			type : "post",
			data : { "checked" : checked,
					 "selected" : selectedId },
			success : function(res){
				alert(res);
			}
		});
	});

	var usersTable = $("#users_table").dataTable(dt_get_options({
		"sAjaxSource": base_url+"/manage/data",
		"bServerSide": true
	}));

	$("#users_table tbody").on('click', 'tr td', function() {
		var id = $(this).parent().find('td').eq(0).text();
		selectedId = id;
		$.ajax({
			url : base_url+"/manage/data/detail",
			type : 'post',
			data : { "id" : id },
			success : function(res){
				$("#email").text(res.email);
				$("#rank").text(res.rank);
				$("#name").text(res.name);
				$("#dept_name").text(res.dept);
				$("#created_at").text(res.createdAt.date);
				if(res.activated==1){
					$("#activated").attr('checked',true);
				} else {
					$("#activated").attr('checked',false);
				}
			}
		});
	});

});
</script>
@stop