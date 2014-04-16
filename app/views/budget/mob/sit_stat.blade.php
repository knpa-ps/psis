@extends('layouts.master')

@section('content')

@include('budget.mob.nav')

<div class="row-fluid">
	<div class="span12 well well-small">
		<form class="form form-inline" id="q_form">
			<div class="header">
				<h4>조회조건</h4>
			</div>

			<div class="row-fluid">
				<div class="span12">
				<div class="input-group">
					<label for="q_month" class="control-label">귀속월</label>
					<input type="text" class="input-mini" name="q_month_start" id="q_month_start" value="{{date('Y-m')}}"> ~ 
					<input type="text" class="input-mini" id="q_month_end" name="q_month_end" value="{{date('Y-m')}}">
				</div>
				<div class="input-group">
					<label for="q_department" class="control-label">
						관서명 
					</label>
					<div class="input-append">
				        <input type="text" id="q_department" disabled
				        name="q_department" class="input-medium"><button class="btn" type="button" id="dept_search">
				            @lang('strings.select')
				        </button>
				        <input type="hidden" name="q_dept_id">
			        </div>
			    </div>

				<div class="input-group">
					<label class="control-label">조회형식</label>
					
					<label>
						<input type="checkbox" value="1" name="q_group_by_region"> 지방청별 합계
					</label>

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
	<div class="span12 panel panel-default">
		<div class="panel-body">

			<table id="mob_table" class="table table-condensed table-bordered table-striped table-hover datatable">
				<thead>
					<tr>
						<th>
							귀속월
						</th>
						<th>
							관서
						</th>
						
						@foreach ($mobCodes as $code)
						<th>
							{{$code->title}}
						</th>
						@endforeach

						<th>
							지급액 (천원)
						</th>
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
@stop

@section('scripts')
{{ HTML::script('static/js/jquery.dataTables.min.js') }}
{{ HTML::script('static/js/psis/dataTables.plugins.js') }}
<script type="text/javascript">
$(function(){
	$("input[name='q_month_start'], input[name='q_month_end']").inputmask('y-m');
	$("#dept_search").click(function(){
		popup('{{action('DepartmentController@showDeptTree')}}', '', 500, 800);
	});

	var oTable = $("#mob_table").dataTable($.extend(dtOptions,{
			"bFilter": false,
			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": "{{ action('BgMobController@getSitStatData') }}",
			"aoColumnDefs": [
				{
					"aTargets": [0,1],
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
		window.open("{{action('BgMobController@exportSitStat')}}?"+params);
	});
});

function setDept(deptId, deptName) {
	$("input[name='q_department']").val(deptName);
	$("input[name='q_dept_id']").val(deptId);
}
</script>

@stop

