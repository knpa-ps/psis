@extends('layouts.master')
@section('styles')
<style>
	th, td {
	  text-align: center;
	  vertical-align: middle!important;
	}
</style>
{{-- 월별 PAVA 사용내역(본청) --}}
@stop
@section('content')
<div class="row">
	<div class="col-xs-12" style="overflow: auto;">
		<ul class="nav nav-tabs">
			<li><a href="{{url('equips/water_per_month')}}">월별 살수내역</a></li>
			<li class="active"><a href="{{url('equips/pava_per_month')}}">월별 PAVA사용내역</a></li>
			<li><a href="{{url('equips/water_pava')}}">집회시 사용내역</a></li>
			<li><a href="{{url('equips/pava_io')}}">집회 외 PAVA소모내역</a></li>
			@if ( in_array($node->type_code, array("D001")) )
			<li><a href="{{url('equips/pava_confirm')}}">삭제요청</a></li>
			@endif
		</ul>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><strong>{{ $year }} 지방청별 월별 PAVA 사용내역 <span style="color: red; font-size: 12px;" class="blink">사용결과 보고는 일일보고임.</span></strong></h3>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-xs-6">
						<form>
							<div class="form-group">
								<label for="year" class="control-label">조회연도</label>
								<select name="year" id="year_select">
								@foreach ($initYears as $i)
									<option value="{{$i->year}}" {{ $i->year == $year ? 'selected' : ''}}>{{$i->year}}</option>
								@endforeach
								</select>
								<button type="submit" class="btn btn-xs btn-primary">조회</button>
							</div>
						</form>
					</div>
					{{-- <div class="col-xs-6">
						<a href="{{URL::current().'?export=true' }}" class="pull-right btn btn-info btn-xs"><span class="glyphicon glyphicon-download" ></span> 다운로드 (.xlsx)</a>
					</div> --}}
				</div>
				<div class="row">
					<div class="col-xs-2">
						<table class="table table-condensed table-hover table-striped table-bordered" id="node_table">
							<tr>
								<th>지방청 선택</th>
							</tr>
							@foreach ($regions as $r)
							<tr>
								<td><a href="#" id="{{$r->id}}" class="region">{{$r->node_name}}</a></td>
							</tr>
							@endforeach
						</table>
					</div>
					<div class="col-xs-10">
						<strong id="table_title"></strong>
						<table class="table table-condensed table-hover table-striped table-bordered" id="pava_table">
						<thead>
							<tr>
								<th rowspan="3">구분</th>
								<th colspan="2">보유량(ℓ)</th>
								<th colspan="3" style="background-color: #63E3DE">사용량(ℓ)</th>
								<th colspan="3">사용횟수</th>
								<th rowspan="2">소실량(ℓ)</th>
							</tr>
							<tr>
								<th>현재보유량(ℓ)</th>
								<th>최초보유량(ℓ)</th>
								<th style="background-color: #63E3DE">계</th>
								<th style="background-color: #63E3DE">훈련시</th>
								<th style="background-color: #63E3DE">집회 시위시</th>
								<th>계</th>
								<th>훈련시</th>
								<th>집회 시위시</th>
							</tr>
							<tr>
								<th id="presentStock" ></th>
								<th id="yearInitHolding"></th>
								<th id="usageSumSum" style="background-color: #C6FFFA"></th>
								<th id="usageTSum" style="background-color: #C6FFFA"></th>
								<th id="usageASum" style="background-color: #C6FFFA"></th>
								<th id="timesSumSum"></th>
								<th id="timesTSum"></th>
								<th id="timesASum"></th>
								<th id="lostSum"></th>
							</tr>
						</thead>
						<tbody>
							@for ($i=1; $i <=12 ; $i++)
							<tr>
								<td style="text-align: center;">{{$i}}월</td>
								@if (isset($stock[$i]))
									<td id="{{'stock_'.$i}}" colspan="2"></td>
									<td id="{{'usageSum_'.$i}}" style="background-color: #C6FFFA"></td>
									<td id="{{'usageT_'.$i}}" style="background-color: #C6FFFA"></td>
									<td id="{{'usageA_'.$i}}" style="background-color: #C6FFFA"></td>
								@else
									<td colspan="2"></td>
									<td></td>
									<td></td>
									<td></td>
								@endif
								<td id="{{'timesSum_'.$i}}"></td>
								<td id="{{'timesT_'.$i}}"></td>
								<td id="{{'timesA_'.$i}}"></td>
								<td id="{{'lost_'.$i}}"></td>
							</tr>
							@endfor
						</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts')
{{ HTML::datepicker() }}
<script type="text/javascript">
$(function(){
	var year = {{ $year }};

	$(".region").on('click', function(){

		var regionId = $(this).attr('id');
		var params = { regionId: regionId, year: year };

		$.ajax({
			url: url("equips/pava_per_month_data"),
			type: "post",
			data: params,
			dataType: 'json',//내부망에선 이걸 추가해줘야 돌아감
			success: function(res){
				for (var i = 0; i <= 12; i++) {
					$("#table_title").text(res['regionName']+" 월별 PAVA 사용내역");

					$("#presentStock").text(res['presentStock']);
					$("#yearInitHolding").text(res['yearInitHolding']);
			 		$("#usageSumSum").text(res['usageSumSum']);
			 		$("#usageTSum").text(res['usageTSum']);
			 		$("#usageASum").text(res['usageASum']);
			 		$("#timesSumSum").text(res['timesSumSum']);
			 		$("#timesTSum").text(res['timesTSum']);
			 		$("#timesASum").text(res['timesASum']);
			 		$("#lostSum").text(res['lostSum']);

					$("#stock_"+i).text(res['stock'][i]);
					$("#usageSum_"+i).text(res['usageSum'][i]);
					$("#usageT_"+i).text(res['usageT'][i]);
					$("#usageA_"+i).text(res['usageA'][i]);
					$("#timesSum_"+i).text(res['timesSum'][i]);
					$("#timesT_"+i).text(res['timesT'][i]);
					$("#timesA_"+i).text(res['timesA'][i]);
					$("#lost_"+i).text(res['lost'][i]);

				}
			}
		});
	});
$(".region").first().trigger("click");
})
</script>
@stop
