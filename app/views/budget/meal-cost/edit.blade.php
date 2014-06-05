@extends('budget.meal-cost.base')

@section('tab-content')
<div class="row">
	<div class="col-xs-12">
		<h4>동원급식비 자료 수정</h4>
		<div class="well well-sm">
			<div class="row">
				<span class="col-xs-8">
					<strong>예산집행관서</strong> / {{ $master->creator->department->full_name }}	
				</span>
				<span class="col-xs-4">
					<strong>자료입력자</strong> / {{ $master->creator->user_name }}
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
					</colgroup>
					<thead>
						<tr>
							<th>집행일자</th>
							<th>동원상황구분</th>
							<th>행사명</th>
							<th>지급구분</th>
							<th>식수인원</th>
							<th>집행액 (원)</th>
						</tr>
					</thead>
					<tbody>
						<tr class="data-row">
							<td>
								<input type="text" class="input-datepicker input-sm data-use-date" name="use_date" value="{{ $master->use_date }}">
							</td>
							<td>
								{{ View::make('widget.code-select', array(
									'id'=>'sit_code', 
									'category'=>'B002', 
									'blank'=>false,
									'options' => array(
										'class'=>'input-sm data-sit-code'
									),
									'default'=>$master->sit_code
								)) }}
							</td>
							<td>
								<input type="text" class="input-sm col-xs-12 data-event-name" name="event_name" value="{{ $master->event_name }}">
							</td>
							<td>
								{{ View::make('widget.code-select', array(
									'id'=>'use_code', 
									'category'=>'B001', 
									'blank'=>false,
									'options' => array(
										'class'=>'input-sm data-use-code'
									),
									'default'=>$master->use_code
								)) }}
							</td>
							<td>
								<input type="text" class="input-sm col-xs-12 data-meal-count" name="meal_count" value="{{ $master->meal_count }}">
							</td>
							<td>
								<input type="text" class="input-sm col-xs-12 data-meal-amout" name="meal_amount" value="{{ $master->meal_amount }}">
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>

			<div class="btn-group pull-right">
				<button type="submit" id="submit" class="btn btn-success btn-xs">
					<span class="glyphicon glyphicon-edit"></span> 수정
				</button>
				<button type="button" id="delete" class="btn btn-danger btn-xs">
					<span class="glyphicon glyphicon-edit"></span> 삭제
				</button>
			</div>
		</form>
	</div>
</div>

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
	$("#delete").on('click', function(){
		if (!confirm('내역을 삭제하시겠습니까?')) {
			return;
		}
		$.ajax({
			url : base_url+"/budgets/meal-cost/"+{{ $master->id }},
			type : "DELETE",
			success: function(response){
				alert(response.message);
				if (response.result==0) {
					redirect(response.url);
				}
			}
		});
	});
	$("#insert_form").validate({
		rules: {
			use_date : {
				required : true,
				dateISO : true
			},
			event_name: {
				required: true,
				maxlength: 1024
			},
			meal_count: {
				required: true,
				number: true,
				min: 0
			},
			meal_amount: {
				required: true,
				number: true,
				min: 0
			}
		},
		submitHandler: function(form) {
			var data = $("#insert_form").serialize();
			$.ajax({
				url : base_url+"/budgets/meal-cost/"+{{ $master->id }},
				type : "PUT",
				data : data,
				success: function(response) {
					alert(response.message);
					if (response.result == 0) {
						redirect(response.url);
					}
				}
			});
		}
	});
});
</script>
@stop