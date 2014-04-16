@extends('layouts.master')

@section('content')
<div class="row-fluid">		
	<div class="box span6">
		<div class="box-header well" data-original-title>
			<h2><i class="icon-cog"></i> 동원급식비 입력마감일</h2>
			<div class="box-icon">
				
			</div>
		</div>
		<div class="box-content">
			<form id="budget_mealpay_form" class="form-inline form well well-small">
				<label for="budget.mealpay.close_date" class="label label-inline">기본 마감일</label>
				<input type="text" class="input-mini" value="{{ $configs['budget.mealpay.close_date'] }}" name="budget.mealpay.close_date">
				<label for="budget.mealpay.close_time" class="label label-inline"  >기본 마감시간</label>
				<input type="text" class="input-mini" value="{{ $configs['budget.mealpay.close_time'] }}" name="budget.mealpay.close_time">
				<input type="submit" class="btn btn-primary pull-right" value="저장"/>
				<p class="help-block">마감일은 1~28 사이의 숫자를 입력하시고, 마감시간은 00:00~23:59까지의 시간을 입력해주세요.</p>
			</form>

			<div class="page-header">
				<h4>월별 마감일 설정</h4>
			</div>

			<div class="datatable-controls">
				<button class="btn select-all" data-toggle="button" data-target="close_date_table">
					<i class="icon-check"></i> @lang('strings.select_all')
				</button>
				<button class="btn btn-danger" id="delete-selected">
					<i class="icon-trash"></i> 삭제
				</button>
				<div class="pull-right">
					<button class="btn btn-primary" id="create_close_date" data-toggle="modal" href="#create_close_date_modal">
						<i class="icon-plus"></i> 입력
					</button>
				</div>
			</div>
			<table id="close_date_table" class="table multi-selectable table-hover table-condensed table-bordered table-striped datatable">
				<thead>
					<tr>
						<th>번호</th>
						<th>귀속월</th>
						<th>마감일</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>
	<div class="box span6">
		<div class="box-header well" data-original-title>
			<h2><i class="icon-cog"></i> 동원급식비 단가</h2>
			<div class="box-icon">
			
			</div>
		</div>
		<div class="box-content">
			<form id="mealpay_cost_form" class="form form-horizontal">
				
				<div class="control-group">
					<label for="officer_amt" class="control-label">
						경찰관
					</label>
					<div class="controls">
						<input type="text" class="input-small" value="{{ $configs['budget.mealpay.officer_amt'] }}" name="budget.mealpay.officer_amt">
					</div>
				</div>

				<div class="control-group">
					<label for="officer2_amt" class="control-label">
						경찰관기동대<br>(2식이상)
					</label>
					<div class="controls">
						<input type="text" class="input-small" value="{{ $configs['budget.mealpay.officer2_amt'] }}" name="budget.mealpay.officer2_amt">
					</div>
				</div>

				<div class="control-group">
					<label for="troop_amt" class="control-label">
						전의경부대<br>(지휘요원포함)
					</label>
					<div class="controls">
						<input type="text" class="input-small" value="{{ $configs['budget.mealpay.troop_amt'] }}" name="budget.mealpay.troop_amt">
					</div>
				</div>
				<div class="control-group">
					<div class="controls form-action">
						<button type="submit" class="btn btn-primary">저장</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<div class="modal hide fade" id="create_close_date_modal">
	<div class="modal-header">
		<a class="close" data-dismiss="modal">&times;</a>
		<h3>동원급식비 입력마감일 생성</h3>
	</div>
	<form class="form form-modal form-horizontal" id="create_close_date_form">
		<div class="modal-body">
			
			<div class="control-group">
				<label for="bm" class="control-label">
					귀속월
				</label>
				<div class="controls">
					<input type="text" class="input-small" required name="bm" id="bm">
				</div>
			</div>
			<div class="control-group">
				<label for="cd" class="control-label">
					마감일
				</label>
				<div class="controls">
					<input type="text" class="input-large" required name="cd" id="cd" data-validation-callback-callback="close_date_validation_callback" >
				</div>
			</div>
			
		</div>
		<div class="modal-footer">
			<button type="submit" class="btn btn-primary">입력</button>
			<a href="#" class="btn" data-dismiss="modal">취소</a>
		</div>
	</form>
</div>
@stop

@section('scripts')
{{HTML::script('static/js/jqBootstrapValidation.js')}}
{{ HTML::script('static/js/jquery.dataTables.min.js') }}
{{ HTML::script('static/js/psis/dataTables.plugins.js') }}
{{ HTML::script('static/js/jquery.inputmask.bundle.min.js') }}
<script type="text/javascript">
$(function(){
	$("#create_close_date_form").submit(function(){
		var params = $(this).serializeArray();
		$.ajax({
			url: "{{ action('BgConfigController@createCloseDate') }}",
			type: "post",
			data: params,
			success: function(result) {
				if (result == 0)
				{
					noty({type:"success", layout:"topRight", text:"생성되었습니다."});
					oTable.fnDraw();
					$("#create_close_date_form").find('input').val('');
				}
			}
		});
		return false;
	});

	$("#delete-selected").click(function(){
		var selected = fnGetSelected(oTable);
		
		if (selected.length == 0)
		{
			bootbox.alert("@lang('strings.no_selection')"
				);
			return;
		}
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
			url: "{{ action('BgConfigController@deleteCloseDates') }}",
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
					msg.text = "이미 마감일이 지난 자료는 삭제할 수 없습니다.";
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

	$("#budget_mealpay_form, #mealpay_cost_form").submit(function(){
		var params = $(this).serializeArray();
		$.ajax({
			url: "{{ action('HomeController@setConfigs') }}",
			type: "post",
			data: JSON.stringify(params),
			contentType: "application/json;charset=utf-8",
			success: function(result) {
				if (result == 0)
				{
					noty({type:"success", layout:"topRight", text:"저장되었습니다."});
				}
			}
		});
		return false;
	});
	$("input,select,textarea").not("[type=submit]").jqBootstrapValidation(); 
	$("#bm").inputmask("y-m");
	$("#cd").inputmask("y-m-d h:s");

	var oTable = $("#close_date_table").dataTable($.extend(dtOptions,{
		"bFilter": false,
		"iDisplayLength": 5,
		"bLengthChange":false,
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "{{ action('BgConfigController@readCloseDates') }}"
	}));

});
function close_date_validation_callback(el, value, callback)
{
	var bm = $("#bm").val();
	var segs = bm.split('-');
	if (segs.length != 2)
	{
		valid = false;
	}
	else
	{
		valid = new Date(value).getTime() >= new Date(segs[0], segs[1], 1).getTime();
	}

	callback({
		value: value,
		valid: valid,
		message: "마감일은 귀속월의 마지막날보다 나중이어야 합니다."
	});
}
</script>
@stop