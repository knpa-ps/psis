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
						<label for="q_format" class="control-label">
							조회형식
						</label>
						<div class="controls">
							<label class="checkbox inline">
								<div class="checker">
									<span class="checked">
										<input type="checkbox" value="1" name="q_monthly_sum" style="opacity: 0;">
									</span>
								</div> 월별 합계
							</label>
						</div>
					</div>		
				</div>
			</div>

			<div class="row-fluid">
				<div class="span6">
					
					
					<div class="control-group">
						<label for="q_department" class="control-label">
							지방청
						</label>
						<div class="controls">

							@if (is_array($region))
							<select name="q_region" id="q_region" rel="chosen">
								<option value="">전체</option>
								@foreach ($region as $r)
									<option value="{{$r['id']}}">{{$r['dept_name']}}</option>
								@endforeach
							</select>
							@else
							<span class="uneditable input-large">{{ $region->dept_name }}</span>
							<input type="hidden" name="q_region" value="{{ $region->id }}">
							@endif
						</div>
					</div>
					
					
				</div>
				<div class="span6">
					<div class="control-group">
						<label for="q_event" class="control-label">
							행사명
						</label>
						<div class="controls">
							<input type="text" class="input-large" name="q_event">
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
	<div class="box span12">
		<div class="box-header well" data-original-title>
			<h2><i class="icon-th-list"></i> 동원급식비</h2>
			<div class="box-icon">
				
			</div>
		</div>
		<div class="box-content">
			<div class="datatable-controls">
				<button class="btn select-all" data-toggle="button" data-target="reports_table">
					<i class="icon-check"></i> @lang('strings.select_all')
				</button>
				<button class="btn btn-danger" id="delete-selected">
					<i class="icon-trash"></i> 삭제
				</button>
				<div class="pull-right">
					<button class="btn btn-primary" id="create-mealpay" data-toggle="modal" href="#create-mealpay-modal">
						<i class="icon-plus"></i> 입력
					</button>
				</div>
			</div>
			<p class="help-block">
				※ 산출근거 단가<br>
				경찰관: {{$configs['budget.mealpay.officer_amt'] or 0}}원 / 경찰관 기동대(2식 이상): {{$configs['budget.mealpay.officer2_amt'] or 0}}원 / 전의경부대(지휘요원 포함): {{$configs['budget.mealpay.troop_amt'] or 0}}원 

			</p>
			<table id="mealpay_table" class="datatable multi-selectable table table-striped table-hover table-bordered table-condensed">
				<colgroup>
					
				</colgroup>
				<thead>
					<tr>
						<th rowspan="2">번호</th>
						<th rowspan="2">
							일자
						</th>
						<th rowspan="2">
							지방청
						</th>
						<th rowspan="2">
							행사명
						</th>
						<th colspan="6">
							행사유형별 동원인원 (명)
						</th>
						<th colspan="4">
							급식인원 (명)
						</th>
						<th rowspan="2">
							소요액 (원)
						</th>
					</tr>
					<tr>
						<th rowspan="1">합계</th>
						<th rowspan="1">집회관리</th>
						<th rowspan="1">경호행사</th>
						<th rowspan="1">혼잡경비</th>
						<th rowspan="1">재난구조</th>
						<th rowspan="1">훈련 등</th>
						<th rowspan="1">합계</th>
						<th rowspan="1">경찰관</th>
						<th rowspan="1">경찰관기동대<br>(2식 이상)</th>
						<th rowspan="1">전의경부대<br>(지휘요원 포함)</th>
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

<div class="modal hide fade" id="create-mealpay-modal">
	<div class="modal-header">
		<a class="close" data-dismiss="modal">&times;</a>
		<h3>동원급식비 집행내역 입력</h3>
	</div>
	<form class="form-modal form form-horizontal" id="create-form">
		<div class="modal-body">

		<div class="control-group">
			<label for="use_date" class="control-label">
				일자
			</label>
			<div class="controls">
				<input type="text" class="datepicker input-small" name="use_date" id="use_date" required>
			</div>
		</div>

		<div class="control-group">
			<label for="event_name" class="control-label">
				행사명
			</label>
			<div class="controls">
				<input type="text" class="input-large" name="event_name" id="event_name" required>
			</div>
		</div>

		<div class="row-fluid">
			<div class="span6">

				<fieldset>
					<legend>행사유형별 동원인원</legend>
					<div class="control-group">
						<label for="demo_cnt" class="control-label">
							집회관리
						</label>
						<div class="controls">
							<input type="number" class="input-mini" min="0" required value="0" name="demo_cnt">
						</div>
					</div>

					<div class="control-group">
						<label for="escort_cnt" class="control-label">
							경호행사
						</label>
						<div class="controls">
							<input type="number" min="0" class="input-mini" required value="0" name="escort_cnt">
						</div>
					</div>

					<div class="control-group">
						<label for="crowd_cnt" class="control-label">
							혼잡경비
						</label>
						<div class="controls">
							<input type="number" min="0" class="input-mini" required value="0" name="crowd_cnt">
						</div>
					</div>

					<div class="control-group">
						<label for="rescue_cnt" class="control-label">
							재난구조
						</label>
						<div class="controls">
							<input type="number" min="0" class="input-mini" required value="0" name="rescue_cnt">
						</div>
					</div>

					<div class="control-group">
						<label for="etc_cnt" class="control-label">
							훈련 등
						</label>
						<div class="controls">
							<input type="number" min="0" class="input-mini" required value="0" name="etc_cnt">
						</div>
					</div>
				</fieldset>

			</div>
			<div class="span6">
				<fieldset>
					<legend>급식인원</legend>
					<div class="control-group">
						<label for="officer_cnt" class="control-label">
							경찰관
						</label>
						<div class="controls">
							<input type="number" class="input-mini" min="0" required value="0" name="officer_cnt">
						</div>
					</div>

					<div class="control-group">
						<label for="officer2_cnt" class="control-label">
							경찰관기동대<br>(2식이상)
						</label>
						<div class="controls">
							<input type="number" min="0" class="input-mini" required value="0" name="officer2_cnt">
						</div>
					</div>

					<div class="control-group">
						<label for="troop_cnt" class="control-label">
							전의경부대<br>(지휘요원 포함)
						</label>
						<div class="controls">
							<input type="number" min="0" class="input-mini" required value="0" name="troop_cnt">
							<p class="help-block"></p>
						</div>
					</div>
				</fieldset>
			</div>
		</div>

		</div>
		<div class="modal-footer">
			<a href="#" class="btn btn-primary" id="create-submit">확인</a>
			<a href="#" class="btn" data-dismiss="modal">취소</a>
		</div>
	</form>
</div>

@stop
@section('styles')
<style type="text/css">
	#mealpay_table thead th {
		vertical-align: middle;
		text-align: center;
		background-color: aliceblue;
	}
	#mealpay_table tbody tr:first-child td {
		background-color: antiquewhite;
	}
</style>
@stop
@section('scripts')
{{HTML::script('static/js/jqBootstrapValidation.js')}}
{{ HTML::script('static/js/jquery.dataTables.min.js') }}
{{ HTML::script('static/js/psis/dataTables.plugins.js') }}
{{ HTML::script('static/js/jquery.inputmask.bundle.min.js') }}
<script type="text/javascript">
$(function(){
	$("input,select,textarea").not("[type=submit]").jqBootstrapValidation(); 
	var editableStart = "{{ $editableStart }}";
	$(".datepicker").inputmask("y-m-d", {"placeholder":"yyyy/mm/dd"});
	
	$("#use_date").datepicker("option", "minDate", new Date({{ strtotime($editableStart)*1000 }}));
	var oTable = $("#mealpay_table").dataTable($.extend(dtOptions,{
			"bFilter": false,
			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": "{{ action('BgMealPayController@read') }}",
			"aoColumnDefs": [
				{
					"aTargets": [0],
					"bVisible": false
				},
				{
					"aTargets": [2],
					"mRender": function(data, type, full) {
						var segments = data.split(':');
						if (segments.length >= 3)
						{
							return segments[1];
						}
						else 
						{
							return data;
						}
					}
				}
			],

			"fnServerParams": function(aoData) {
				var params = $("#q_form").serializeArray();
				aoData = $.merge(aoData, params);
			}
	}));

	$("#delete-selected").click(function(){
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
				url: "{{ action('BgMealPayController@delete') }}",
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

	$("#create-submit").click(function(){
		var params = $("#create-form").serializeArray();
		$.ajax({
			url: "{{ action('BgMealPayController@create') }}",
			type: "post",
			data: params,
			success: function(response)
			{
				var msg = {
					layout: "topRight"
				};
				switch(parseInt(response))
				{
					case 0:
					msg.type = "success";
					msg.text = "추가되었습니다.";
					$("#create-form input[type=text]").val('');
					$("#create-form input[type=number]").val(0);

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

	$("#q_form_submit").click(function(){
		oTable.fnDraw();
	});
});
</script>
@stop