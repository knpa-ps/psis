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
						<label for="q_title" class="control-label">
							제목
						</label>
						<div class="controls">
							<input type="text" id="q_title" name="q_title" class="input-xlarge">		
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
			<table id="reports_table" class="datatable table table-striped table-hover table-bordered table-condensed">
				<colgroup>
					
				</colgroup>
				<thead>
					<tr>
						<th>
							번호
						</th>
						<th>
							제목
						</th>
						<th>
							상태
						</th>
						<th>
							전송일시
						</th>
					</tr>
				<tbody>
					<tr>
						<td class="center" colspan="100">
							{{HTML::image('static/img/ajax-loaders/ajax-loader-6.gif')}}
						</td>
					</tr>
				</tbody>
				</thead>

			</table>
		</div>
	</div>
</div>
@stop

@section('styles')
<style type="text/css" media="screen">
#q_form {
	margin-bottom: 0;
}
</style>
@stop

@section('scripts')
{{ HTML::script('static/js/jquery.dataTables.min.js') }}
{{ HTML::script('static/js/psis/dataTables.plugins.js') }}
{{ HTML::script('static/js/jquery.inputmask.bundle.min.js') }}
<script type="text/javascript">
$(function(){
	$(".datepicker").inputmask("y-m-d", {"placeholder":"yyyy/mm/dd"});
	var oTable = $("#reports_table").dataTable($.extend(dtOptions,{
			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": "{{ action('ReportController@getReports') }}",
			"aoColumnDefs": [
				{
					"sClass":"single-line",
					"aTargets": [0,1,2,3]
				},
				{
					"aTargets": [1],
					"mRender": function(data, type, full) {
						return "<a href=\"{{action('ReportController@showReport')}}"
						+"?id="+full[0]+"\">"+data+"</a>";
					}
				},
				{
					"aTargets": [2],
					"mRender": function(data, type, full) {
						return data?"<span style='color:red;'>마감</span>":"<span style='color:blue;'>수신</span>";
					}
				}
			],
			"fnServerParams": function(aoData) {
				var params = $("#q_form").serializeArray();
				aoData = $.merge(aoData, params);
				aoData.push({"name": "mine", "value": 1});
			}
		}));

	$("#q_form_submit").click(function(){
		oTable.fnDraw();
	});
});
</script>
@stop

