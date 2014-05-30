@extends('budget.meal-cost.base')

@section('tab-content')

<div class="row">
	<div class="col-xs-12">
		<div class="well well-small">
			<form class="form-horizontal" id="data_table_form">
				<input type="hidden" name="type" value="raw">
				<div class="row">
					<div class="col-xs-6 form-group">
						<label for="start" class="col-xs-3 control-label">
							집행일자
						</label>
						<div class="col-xs-9">
							<div class="input-daterange input-group">
							    <input type="text" class="input-sm form-control" name="start" 
							    value="{{ date('Y-m-d', strtotime('-1 month')) }}">
							    <span class="input-group-addon">~</span>
							    <input type="text" class="input-sm form-control" name="end"
							    value="{{ date('Y-m-d') }}" >
							</div>
						</div>
					</div>

					<div class="col-xs-6 form-group">
						<label for="dept_id" class="col-xs-3 control-label">
							관서
						</label>
						<div class="col-xs-9">
							{{ View::make('widget.dept-selector', array('id'=>'dept_id', 'inputClass'=>'input-sm')) }}
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-xs-6 form-group">
						<label for="sit_code" class="control-label col-xs-3">
							상황구분
						</label>
						<div class="col-xs-3">
							{{ View::make('widget.code-select', array(
								'id'=>'sit_code',
								'category'=>'B002'
							)) }}
						</div>
						<label for="use_code" class="control-label col-xs-3">
							지급구분
						</label>
						<div class="col-xs-3">
							{{ View::make('widget.code-select', array(
								'id'=>'use_code',
								'category'=>'B001'
							)) }}
						</div>
					</div>
					<div class="col-xs-6 form-group">
						<label for="event_name" class="control-label col-xs-3">
							행사명
						</label>
						<div class="col-xs-9">
							<input type="text" class="input-sm form-control" id="event_name" name="event_name">
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-xs-12">
						<div class="pull-right">
							<button class="btn btn-primary btn-xs" type="submit"><span class="glyphicon glyphicon-ok"></span> 조회</button>
							<!-- <button class="btn btn-default btn-xs" type="button"><span class="glyphicon glyphicon-download"></span> 다운로드</button> -->
						</div>
						<div class="clearfix"></div>
					</div>
				</div>

			</form>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-xs-12">
		{{ View::make('datatable.template', array(
			'id'=>'data_table', 
			'columns'=>array('번호', '집행일자', '집행관서', '상황구분', '행사명', '지급구분', '식수인원', '집행액 (원)','')
		)) }}
	</div>
</div>

@stop

@section('styles')
{{ HTML::style('static/vendor/bootstrap-datepicker/css/datepicker3.css') }}
@stop

@section('scripts')
{{ HTML::script('static/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js') }}
{{ HTML::script('static/vendor/bootstrap-datepicker/js/locales/bootstrap-datepicker.kr.js') }}
{{ HTML::script('static/vendor/bootstrap-datepicker/js/defaults.js') }}
{{ HTML::script('static/vendor/datatables/js/jquery.dataTables.min.js') }}
{{ HTML::script('static/vendor/datatables/js/jquery.dataTables.plugins.js') }}
<script type="text/javascript">
$(function() {
	var oTable = $("#data_table").dataTable(dt_get_options({
		"bFilter": false,
		"bSort": false,
		"bServerSide": true,
		"sAjaxSource" : base_url+ "/budgets/meal-cost/data",
		"fnServerData": function(sSource, aoData, fnCallback) {
			var params = $("#data_table_form").serializeArray();
			aoData = $.merge(aoData, params);
			$.getJSON(sSource, aoData, function(json) {
				
				fnCallback(json);
			});
		}
	}));

	$("#data_table_form").submit(function() {
		oTable.fnDraw();
		return false;
	});
});
</script>
@stop