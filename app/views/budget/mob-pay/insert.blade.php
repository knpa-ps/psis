@extends('budget.mob-pay.base')

@section('tab-content')
<div class="row">
	<div class="col-xs-12">
		<h4>경비동원수당 자료 입력</h4>
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
				<legend><h5><strong>기본정보</strong></h5></legend>
				<div class="row">
					<div class="form-group col-xs-3">
						<label for="use_date" class="control-label col-xs-5">
							집행일자
						</label>
						<div class="col-xs-7">
							<input type="text" class="input-datepicker input-sm form-control" name="use_date" id="use_date"
							value="{{ date('Y-m-d') }}">
						</div>
					</div>

					<div class="form-group col-xs-4">
						<label for="sit_code" class="control-label col-xs-5">
							동원상황구분
						</label>
						<div class="col-xs-7">
							{{ View::make('widget.code-select', array('id'=>'sit_code', 'category'=>'B002', 'blank'=>false)) }}
						</div>
					</div>
					<div class="form-group col-xs-5">
						<label for="event_name" class="control-label col-xs-4">
							동원행사명
						</label>
						<div class="col-xs-8">
							<input type="text" class="input-sm form-control" name="event_name" id="event_name">
						</div>
					</div>
				</div>
			</fieldset>

			<fieldset>
				<legend><h5><strong>동원자 명단</strong></h5></legend>
				<table id="detail_table" class="table table-condensed table-bordered table-hover table-striped">
					<colgroup>
						<col class="col-xs-3">
						<col class="col-xs-1">
						<col class="col-xs-1">
						<col>
						<col class="col-xs-1">
						<col class="col-xs-1">
					</colgroup>
					<thead>
						<tr>
							<th>관서</th>
							<th>계급</th>
							<th>이름</th>
							<th>동원기간</th>
							<th>지급액 (원)</th>
							<th>작업</th>
						</tr>
					</thead>
					<tbody>
						<tr id="add_row">
							<td colspan="6" align="right">
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
				<div class="dept-selector" id="test">
					<input type="text" class="input-sm data-dept-name col-xs-12" readonly="readonly">
					<input type="hidden" name="dept_ids[]" class="data-dept-id">
				</div>
			</td>
			<td>
				{{ View::make('widget.code-select', array(
					'id'=>'ranks[]', 
					'category'=>'H001', 
					'blank'=>false,
					'default' => 'R008',
					'options' => array(
						'class'=>'input-sm data-rank'
					)
				)) }}
			</td>
			<td>
				<input type="text" class="input-sm col-xs-12 data-name" name="names[]">
			</td>
			<td>
				<div class="col-xs-3 ">
					<input type="text" class="input-sm col-xs-12 data-start-date input-datepicker" name="start_dates[]" placeholder="Y-m-d">
				</div>
				<div class="col-xs-2">
					<input type="text" class="input-sm col-xs-12 data-start-time" name="start_times[]" placeholder="00:00">
				</div>
				<div class="col-xs-1" align="center">~</div>
				<div class="col-xs-3">
					<input type="text" class="input-sm col-xs-12 data-end-date input-datepicker" name="end_dates[]" placeholder="Y-m-d">
				</div>
				<div class="col-xs-2">
					<input type="text" class="input-sm col-xs-12 data-end-time" name="end_times[]" placeholder="00:00">
				</div>
			</td>
			<td>
				<input type="text" class="input-sm col-xs-12 data-amount" name="amounts[]">
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
		rules: {
			use_date: {
				required: true,
				dateISO: true
			},
			event_name: {
				required: true,
				maxlength: 1024
			}
		},
		submitHandler: function(form) {
			var data = {
				use_date : $("#use_date").val(),
				sit_code : $("#sit_code").val(),
				event_name: $("#event_name").val()
			};
			var details = [];
			$("#detail_table .data-row").each(function() {
				var detail = {
					dept_id : $(this).find('.data-dept-id').val(),
					rank : $(this).find('.data-rank').val(),
					name : $(this).find('.data-name').val(),
					start_date : $(this).find('.data-start-date').val(),
					start_time : $(this).find('.data-start-time').val(),
					end_date : $(this).find('.data-end-date').val(),
					end_time : $(this).find('.data-end-time').val(),
					amount : $(this).find('.data-amount').val()
				};
				details.push(detail);
			});

			data.details = details;

			$.ajax({
				url: base_url+"/budgets/mob-pay",
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

	$("#detail_table tbody").on('click', '.remove-row', function() {
		if ($("#detail_table tbody tr.data-row").length == 1) {
			alert('최소 하나의 동원 내역을 입력해야 합니다.');
			return;
		}

		$(this).parent().parent().remove();
	});

	$("#detail_table tbody").on('click', '.copy-row', function() {
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

	row.find('.data-dept-name')
	.prop('name', 'dept_names['+numRows+']')
	.rules('add', {
		required: true
	});

	row.find('.data-name')
	.prop('name', 'names['+numRows+']')
	.rules('add', {
		required: true,
		maxlength: 10
	});

	row.find('.data-start-date')
	.prop('name', 'start_dates['+numRows+']')
	.rules('add', {
		required: true,
		dateISO: true
	});

	row.find('.data-start-time')
	.prop('name', 'start_times['+numRows+']')
	.rules('add', {
		required: true,
		time: true
	});

	row.find('.data-end-date')
	.prop('name', 'end_dates['+numRows+']')
	.rules('add', {
		required: true,
		dateISO: true
	});

	row.find('.data-end-time')
	.prop('name', 'end_times['+numRows+']')
	.rules('add', {
		required: true,
		time: true
	});

	row.find('.data-amount')
	.prop('name', 'amounts['+numRows+']')
	.rules('add', {
		required: true,
		number: true
	});

	row.find('.data-start-date').datepicker();
	row.find('.data-end-date').datepicker();

	row.find('.data-start-time').timepicker({
		scrollDefaultNow: true,
		timeFormat: 'H:i'
	});
	row.find('.data-end-time').timepicker({
		scrollDefaultNow: true,
		timeFormat: 'H:i'
	});

	// remove error labels
	row.find('label.error').remove();
}

$("#detail_table tbody").on('click', '.data-dept-name', function() {
	$("body").modalmanager('loading');

	var container_id = $(this).parent().prop('id');

	var data = {
		container_id: container_id
	};

	$modal.load(base_url+"/ajax/dept_select_tree", data, function() {
		$modal.modal({
			modalOverflow: true
		});
	});
});

$("#detail_table tbody").on('select.dept-selector', '.dept-selector', function(e, data) {
	$(this).find(".data-dept-name").val(data.full_name);
	$(this).find(".data-dept-id").val(data.dept_id);
});
</script>
@stop