@extends('budget.mob-pay.base')

@section('tab-content')
<div class="row">
	<div class="col-xs-12">
		<h4>동원급식비 자료 입력</h4>
		<div class="well well-sm">
			<div class="row">
				<span class="col-xs-4">
					<strong>예산집행관서</strong> / {{ Sentry::getUser()->department->full_name }}	
				</span>
				<span class="col-xs-4">
					<strong>자료입력자</strong> / {{ Sentry::getUser()->user_name }}
				</span>
			</div>
		</div>
		<form id="insert_form" class="form-horizontal" novalidate>
			<fieldset>
				<table id="data_table" class="table table-condensed table-bordered table-hover table-striped">
					<colgroup>
						<col class="col-xs-1">
						<col class="col-xs-1">
						<col class="col-xs-2">
						<col class="col-xs-1">
						<col class="col-xs-1">
						<col class="col-xs-2">
						<col>
					</colgroup>
					<thead>
						<tr>
							<th>집행일자</th>
							<th>동원상황구분</th>
							<th>행사명</th>
							<th>지급구분</th>
							<th>식수인원</th>
							<th>집행액 (원)</th>
							<th>작업</th>
						</tr>
					</thead>
					<tbody>
						<tr id="add_row">
							<td colspan="8" align="right">
								<button class="btn btn-info btn-xs" id="add_row_btn" type="button">
									<span class="glyphicon glyphicon-plus"></span> 추가
								</button>
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>

			<div class="form-group row">
				<div class="col-xs-12">
					<button type="submit" class="btn btn-primary btn-lg btn-block">
						제출
					</button>
				</div>
			</div>
		</form>
	</div>
</div>
<table class="hide" id="form_template_table">
	<tbody>
		<tr class="data-row">
			<td>
				<input type="text" class="input-datepicker input-sm data-use-date" name="use_dates[]">
			</td>
			<td>
				{{ View::make('widget.code-select', array(
					'id'=>'sit_codes[]', 
					'category'=>'B002', 
					'blank'=>false,
					'options' => array(
						'class'=>'input-sm data-sit-code'
					)
				)) }}
			</td>
			<td>
				<input type="text" class="input-sm col-xs-12 data-event-name" name="event_names[]">
			</td>
			<td>
				{{ View::make('widget.code-select', array(
					'id'=>'use_codes[]', 
					'category'=>'B001', 
					'blank'=>false,
					'options' => array(
						'class'=>'input-sm data-use-code'
					)
				)) }}
			</td>
			<td>
				<input type="text" class="input-sm col-xs-12 data-meal-count" name="meal_counts[]">
			</td>
			<td>
				<input type="text" class="input-sm col-xs-12 data-meal-amout" name="meal_amounts[]">
			</td>
			<td>
				<button class="btn btn-success btn-xs copy-row" type="button">
					<span class="glyphicon glyphicon-th-large"></span> 복사
				</button>
				<button class="btn btn-danger btn-xs remove-row" type="button">
					<span class="glyphicon glyphicon-remove"></span> 삭제
				</button>
			</td>
		</tr>
	</tbody>
</table>
@stop
@section('styles')
{{ HTML::style('static/vendor/bootstrap-datepicker/css/datepicker3.css') }}
{{ HTML::style('static/vendor/timepicker/jquery.timepicker.css') }}
<style type="text/css" media="screen">
.data-row .data-dept-name {
	cursor: pointer;
}
#detail_table div.col-xs-2, #detail_table div.col-xs-3 {
	padding:0;

}
</style>
@stop

@section('scripts')
{{ HTML::script('static/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js') }}
{{ HTML::script('static/vendor/bootstrap-datepicker/js/locales/bootstrap-datepicker.kr.js') }}
{{ HTML::script('static/vendor/bootstrap-datepicker/js/defaults.js') }}

{{ HTML::script('static/vendor/validate/jquery.validate.min.js') }}
{{ HTML::script('static/vendor/validate/additional-methods.min.js') }}
{{ HTML::script('static/vendor/validate/messages_ko.js') }}

{{ HTML::script('static/vendor/timepicker/jquery.timepicker.min.js') }}
<script type="text/javascript">
$(function() {
	var validator = $("#insert_form").validate({
		submitHandler: function(form) {

			var data = [];
			$("#data_table tr.data-row").each(function() {
				data.push({
					use_date : $(this).find('.data-use-date').val(),
					sit_code : $(this).find('.data-sit-code').val(),
					event_name : $(this).find('.data-event-name').val(),
					use_code : $(this).find('.data-use-code').val(),
					meal_count : $(this).find('.data-meal-count').val(),
					meal_amount : $(this).find('.data-meal-amout').val()
				});
			});

			$.ajax({
				url: base_url+"/budgets/meal-cost",
				type: "post",
				contentType: "application/json; charset=UTF-8",
				data: JSON.stringify(data),
				success: function(response) {
					alert(response.message);
					if (response.result == 0) {
						redirect(response.url);
					}
				}
			});
			return false;
		}
	});

	addRow();
	$("#add_row_btn").click(addRow);

	$("#data_table tbody").on('click', '.remove-row', function() {
		if ($("#data_table tbody tr.data-row").length == 1) {
			alert('최소 하나의 집행 내역을 입력해야 합니다.');
			return;
		}

		$(this).parent().parent().remove();
	});

	$("#data_table tbody").on('click', '.copy-row', function() {
		var row = $(this).parent().parent();
		var newRow = row.clone();
		$("#add_row").before(newRow);
		onRowAdded(newRow);
	});
});

function addRow() {
	var row = $("#form_template_table tbody tr").clone();
	$("#add_row").before(row);
	onRowAdded(row);
}

function onRowAdded(row) {
	// dept-selector
	var rows = $("#detail_table tbody .data-row");
	var numRows = rows.length;
	row.find('.dept-selector').prop('id', 'dept_selector_'+numRows);

	row.find('.data-use-date')
	.prop('name', 'use_dates['+numRows+']')
	.rules('add', {
		required: true,
		dateISO: true
	});

	row.find('.data-event-name')
	.prop('name', 'event_names['+numRows+']')
	.rules('add', {
		required: true,
		maxlength: 1024
	});

	row.find('.data-meal-count')
	.prop('name', 'meal_counts['+numRows+']')
	.rules('add', {
		required: true,
		number: true,
		min: 0
	});

	row.find('.data-meal-amout')
	.prop('name', 'meal_amounts['+numRows+']')
	.rules('add', {
		required: true,
		number: true,
		min: 0
	});

	row.find('.data-use-date').datepicker();

	// remove error labels
	row.find('label.error').remove();
}
</script>
@stop