@extends('layouts.master')

@section('content')
@include('budget.meal.nav')

<div class="row-fluid">
	<div class="span12 well well-small">
		<form class="form form-inline" id="q_form">
			<div class="header">
				<h4>조회조건</h4>
			</div>

			<div class="row-fluid">
				<div class="span12">
				<div class="input-group">
					<label for="q_date" class="control-label">동원일자</label>
					<input type="text" class="input-small datepicker start" name="q_date_start" id="q_date_start"> ~ 
					<input type="text" class="input-small datepicker end" id="q_date_end" name="q_date_end">
				</div>
				<div class="input-group">
					<label for="q_department" class="control-label">
						관서명 
					</label>
					<div class="input-append">
				        <input type="text" id="q_department" disabled
				        name="q_department" class="input-medium"><button class="btn" type="button" id="dept-search">
				            @lang('strings.select')
				        </button>
				        <input type="hidden" name="q_dept_id">
			        </div>
			    </div>
			    <div class="input-group">
					<label for="q_mob_code">
						동원상황구분
					</label>
					<select name="q_mob_code" rel="chosen" class="input-medium">
					<option value="">전체</option>
					@foreach ($mobCodes as $code) 
						<option value="{{$code->code}}">{{$code->title}}</option>
					@endforeach
					</select>
				</div>
				</div>
			</div>
			
			<div class="row-fluid">
				<div class="span12">
					<div class="pull-right">
						<button type="button" id="q_form_submit" class="btn btn-primary">조회</button>
						<button type="button" id="q_form_export" class="btn btn-info">다운로드</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<div class="row-fluid">		
	<div class="panel panel-default span12">
		<div class="panel-body">

			<div class="btn-toolbar">
				<div class="btn-group">
					<button class="btn select-all" data-toggle="button" data-target="mealpay_table">
						<i class="icon-check"></i> @lang('strings.select_all')
					</button>
				</div>
				<div class="btn-group">
				  	<button class="btn btn-danger" id="btn_del">
						<i class="icon-lock"></i> 삭제
					</button>
				</div>
				<div class="btn-group pull-right">
					<button class="btn btn-primary" id="btn_add" data-toggle="modal" href="#modal_insert_form">
						<i class="icon-plus"> </i> 입력
					</button>
				</div>
			</div>

			<p class="help-block well well-small">
				※ 산출근거 단가<br>
				경찰관: {{$configs['budget.mealpay.officer_amt']*1000}}원 / 경찰관 기동대(2식 이상): {{$configs['budget.mealpay.officer2_amt']*1000}}원 / 전의경부대(지휘요원 포함): {{$configs['budget.mealpay.troop_amt']*1000}}원 
			</p>
			<table id="mealpay_table" class="datatable multi-selectable table table-striped table-hover table-bordered table-condensed">
				<thead>
					<tr>
						<th rowspan="2">번호</th>
						<th rowspan="2">
							일자
						</th>
						<th rowspan="2">
							관서
						</th>
						<th rowspan="2">
							행사유형 
						</th>
						<th rowspan="2">
							행사명
						</th>
						<th colspan="4">
							급식인원 (명)
						</th>
						<th rowspan="2">
							소요액 (천원)
						</th>
					</tr>
					<tr>
						<th rowspan="1">합계</th>
						<th rowspan="1">경찰관</th>
						<th rowspan="1">경찰관기동대<br>(2식 이상)</th>
						<th rowspan="1">전의경부대<br>(지휘요원 포함)</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="center" colspan="100">
							{{HTML::image('static/img/ajax-loaders/ajax-loader-6.gif')}}
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="modal hide fade" id="modal_insert_form">
	<div class="modal-header">
		<a class="close" data-dismiss="modal">&times;</a>
		<h3>동원급식비 지급내역 입력</h3>
	</div>
	<div class="modal-body">
		<div class="well well-small">
			<p class="help-block">
				1. 현재 내역을 입력할 수 있는 날짜는 <strong>{{$editableStart}}</strong>부터입니다.
			</p>
			<p class="help-block">
			※ 산출근거 단가<br>
			경찰관: {{$configs['budget.mealpay.officer_amt']*1000}}원 / 경찰관 기동대(2식 이상): {{$configs['budget.mealpay.officer2_amt']*1000}}원 / 전의경부대(지휘요원 포함): {{$configs['budget.mealpay.troop_amt']*1000}}원 
			</p>
		</div>

		<div class="btn-toolbar">
			<button id="dept_select_all" class="btn" type="button">
	            부대 일괄 선택
	        </button>

	        <button class="btn btn-info pull-right" id="add_row"><i class="icon-plus"></i> 필드 추가</button>
		</div>

		<form id="insert_form" class="form form-inline">
			<table id="insert_row_container" class="table table-bordered">

				<thead>
					<tr>
						<th>부대</th>
						<th>동원일자</th>	
						<th>동원상황</th>
						<th>행사명</th>
						<th>경찰관</th>
						<th>경찰관기동대<br>(2식이상)</th>
						<th>전의경부대<br>(지휘요원포함)</th>
						<th>삭제</th>
					</tr>
				</thead>
				
				<tbody>
					
				</tbody>
			</table>
		</form>
	</div>
	<div class="modal-footer">
		<a href="#" class="btn btn-primary" id="insert_submit">제출</a>
		<a href="#" class="btn" data-dismiss="modal">취소</a>
	</div>
</div>

<table class="hide">
	<tbody id="insert_row_template">
		<tr>
			<td>
				<div class="input-append">
			        <input type="text" disabled
			        name="i_dept[]" class="input-medium"><button class="btn insert_dept_select" type="button">
			            선택
			        </button>
			        <input type="hidden" name="i_dept_id[]">
		        </div>
		    </td>
			<td>
				<input type="text" name="i_date[]" class="input-small">
			</td>
			<td>
				<select name="i_mob_code[]" class="input-small">
				@foreach ($mobCodes as $code) 
					<option value="{{$code->code}}">{{$code->title}}</option>
				@endforeach
				</select>
			</td>
			<td>
				<input type="text" class="input-small" name="i_event_name[]">
			</td>
			<td>
				<input type="text" class="input-mini" name="i_officer[]">
			</td>
			<td>
				<input type="text" class="input-mini" name="i_officer_troop[]">
			</td>
			<td>
				<input type="text" class="input-mini" name="i_troop[]">
			</td>
			<td>
				<button type="button" class="del-row btn btn-danger">
					<i class="icon-remove icon-white"></i>
				</button>
			</td>
		</tr>
	</tbody>
</table>
@stop

@section('styles')
<style type="text/css">
	#modal_insert_form {
		width: 1200px;
		margin-left: -600px;
	}
</style>

@stop

@section('scripts')
{{ HTML::script('static/js/jquery.dataTables.min.js') }}
{{ HTML::script('static/js/psis/dataTables.plugins.js') }}
<script type="text/javascript" charset="utf-8">
$(function(){
	$(".datepicker").inputmask("y-m-d");
	var oTable = $("#mealpay_table").dataTable($.extend(dtOptions,{
			"bFilter": false,
			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": "{{ action('BgMealPayController@getPayrollData') }}",
			"aoColumnDefs": [
				{
					"aTargets": [0,1,2],
					"sClass": "single-line"
				}
			],
			"fnServerParams": function(aoData) {
				var params = $("#q_form").serializeArray();
				aoData = $.merge(aoData, params);
			}
	}));

	$("#q_form_submit").click(function(){
		oTable.fnDraw();
	});

	$("#q_form_export").click(function(){
		var params = $("#q_form").serialize();
		window.open("{{action('BgMealPayController@exportPayroll')}}?"+params);
	});

	$("#btn_del").click(function() {
		var selected = fnGetSelected(oTable);
		
		if (selected.length == 0)
		{
			bootbox.alert("@lang('strings.no_selection')"
				);
			return;
		}

		bootbox.confirm("삭제하시겠습니까?", function(result){
			if (!result) return;
				
			var ids = [];
			selected.each(function(){
				var data = oTable.fnGetData(this);
				var id = data[0];
				if (id > 0)
				{
					ids.push(id);
				}
			});

			$.ajax({
				url: "{{ action('BgMealPayController@deletePayroll') }}",
				contentType: 'application/json; charset=utf-8',
				data: JSON.stringify(ids),
				type: "post",
				success: function(response) {
					var msg = {
						layout: "topRight"
					};
					switch (parseInt(response)) {
						case 0:
						msg.type = "success";
						msg.text = "삭제되었습니다.";
						break;
						case -1:
						msg.type = "error";
						msg.text = editableStart+" 이전의 자료는 이미 마감되어 삭제할 수 없습니다.";
						break;
						default:
						msg.type = "error";
						msg.text = "서버에서 오류가 발생했습니다.";
						break;
					}
					noty(msg);
					oTable.fnDraw();
				}
			});
		});
	});

	$("#dept-search").click(function(){
		currentDeptNameElement = $("input[name='q_department']");
		currentDeptIdElement = $("input[name='q_dept_id']");
		popup('{{action('DepartmentController@showDeptTree')}}', '', 500, 800);
	});

	$("#dept_select_all").click(function(){
		setAllDepts = true;
		popup('{{action('DepartmentController@showDeptTree')}}', '', 500, 800);
	});
	var editableStart = "{{ $editableStart }}";
	
	var addRow = function() {
		var row = $("#insert_row_template");
		$("#insert_row_container tbody").append(row.html());
		$("input[name='i_date[]']").datepicker("option", "minDate", new Date({{ strtotime($editableStart)*1000 }}));
		$("input[name='i_mob_start[]'],input[name='i_mob_end[]']").inputmask("h:s");
		$("input[name='i_date[]']").inputmask("y-m-d");
	}

	addRow();

	$("#add_row").click(function(){
		addRow();
	});

	$(document).on('click', '.del-row', function(){
		$(this).parent().parent().remove();
	});

	$(document).on('click', '.insert_dept_select', function(){
		currentDeptNameElement = $(this).siblings('input[name="i_dept[]"]');
		currentDeptIdElement = $(this).siblings('input[name="i_dept_id[]"]');
		popup('{{action('DepartmentController@showDeptTree')}}', '', 500, 800);
	});

	$("#insert_submit").click(function(){
		
		var params = $("#insert_form").serializeArray();

		if (params.length == 0) {
			bootbox.alert("자료를 입력해주세요.");
			return;
		}

		$.ajax({
			url: "{{ action('BgMealPayController@insertPayroll') }}",
			type: "post",
			data: params,
			success: function(resp) {
				if (resp == 0) {
					$("#insert_row_container tbody").empty();
					addRow();
					$("#modal_insert_form").modal('hide');
					noty({type:'success', layout: 'topRight', text: '완료되었습니다.'});
					oTable.fnDraw();
				} else if (resp == -1) {
					noty({type:'error', layout: 'topRight', text: '이미 마감된 날짜에 대해서 입력할 수 없습니다.'});
				} else if (resp == -2) {
					noty({type:'error', layout: 'topRight', text: '해당 부서에 대한 권한이 없습니다.'});
				}
			}
		});
	});
});

var setAllDepts = false;
var currentDeptNameElement = '';
var currentDeptIdElement = '';
function setDept(deptId, deptName) {

	if (setAllDepts) 
	{
		setAllDepts = false;
		$("#insert_row_container input[name='i_dept[]']").val(deptName);
		$("#insert_row_container input[name='i_dept_id[]']").val(deptId);
		return;
	}

	if (currentDeptNameElement)
    	currentDeptNameElement.val(deptName);
    if (currentDeptIdElement)
    	currentDeptIdElement.val(deptId);
}
</script>
@stop


