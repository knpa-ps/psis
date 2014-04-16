@extends('layouts.master')

@section('content')
<div class="row-fluid">
	<div class="span12 well well-small">
		<form class="form form-horizontal form-query" id="q_form">
			<div class="header">
				<h4>조회조건</h4>
			</div>
			<div class="row-fluid">
				<div class="span6">
					
					<div class="control-group">
						<label for="q_date" class="control-label">
							조회기간 
						</label>
						<div class="controls">
							<input type="text" class="input-small datepicker start" name="q_date_start" id="q_date_start"> ~ 
							<input type="text" class="input-small datepicker end" id="q_date_end" name="q_date_end">
						</div>
					</div>
				</div>
				<div class="span6">
					
					
					<div class="control-group">
						<label for="q_department" class="control-label">
							관서명 
						</label>
						<div class="controls">
							<div class="input-append">
						        <input type="text" id="q_department" disabled
						        name="q_department" class="input-medium"><button class="btn" type="button" id="dept-search">
						            @lang('strings.select')
						        </button>
						        <input type="hidden" name="q_dept_id">
					        </div>
						</div>
					</div>
					
				</div>
			</div>
			<div class="row-fluid">
				<div class="span12">
					<div class="control-group pull-right">
						<div class="controls">
							<button type="button" class="btn btn-primary" id="q_form_submit">
								@lang('strings.view')
							</button>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<div class="row-fluid">		
	<div class="panel panel-default span12">
		<div class="panel-body">
			<ul class="nav nav-tabs" id="table_tabs">
			  <li class="active"><a href="#indiv">개인별 사용통계</a></li>
			  <li><a href="#dept">부서별 사용통계</a></li>
			</ul>
			 
			<div class="tab-content">
				<div class="tab-pane active" id="indiv">
			  		<table id="stat_table1" class="table datatable table-condensed table-bordered table-striped table-hover">
			  			<thead>
			  				<tr>
			  					<th>소속</th>
			  					<th>계급</th>
			  					<th>이름</th>	
			  					<th>작성 속보 수</th>
			  				</tr>
			  			</thead>
			  			<tbody>
			  			</tbody>
			  		</table>
				</div>
				<div class="tab-pane" id="dept">
			  		<table id="stat_table2" class="table datatable table-condensed table-bordered table-striped table-hover">
			  			<thead>
			  				<tr>
			  					<th>관서</th>
			  					<th>작성 속보 수</th>
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
@stop

@section('scripts')
{{ HTML::script('static/js/jquery.dataTables.min.js') }}
{{ HTML::script('static/js/psis/dataTables.plugins.js') }}
<script type="text/javascript">
$(document).ready(function(){
    $('#table_tabs a').click(function(e){
    	e.preventDefault();
    	$(this).tab('show');
    });

	var oTable = $("#stat_table1").dataTable($.extend(dtOptions,{
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "{{ action('ReportController@getUserStats') }}",
		"aoColumnDefs": [
		],
		"fnServerParams": function(aoData) {
			var params = $("#q_form").serializeArray();
			aoData = $.merge(aoData, params);
		}
	}));

	var oTable2 = $("#stat_table2").dataTable($.extend(dtOptions,{
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "{{ action('ReportController@getDeptStats') }}",
		"aoColumnDefs": [
		],
		"fnServerParams": function(aoData) {
			var params = $("#q_form").serializeArray();
			aoData = $.merge(aoData, params);
		}
	}));

	$("#q_form_submit").click(function(){
		oTable.fnDraw();
		oTable2.fnDraw();
		
	});
});
</script>
@stop
