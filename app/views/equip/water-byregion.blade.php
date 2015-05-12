@extends('layouts.master')
@section('styles')
<style>
	th, td {
	  text-align: center;
	  vertical-align: middle!important;
	}
</style>
@stop
@section('content')
<div class="row">
	<div class="col-xs-12" style="overflow: auto;">
		<ul class="nav nav-tabs">
			<li class="active"><a href="{{url('equips/water_region')}}">월별 보기</a></li>
			<li><a href="{{url('equips/water_affair')}}">행사별 보기</a></li>
		</ul>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><strong>{{ $year }} 물 사용량 현황</strong></h3>
			</div>
			<div class="panel-body">
				<div class="col-xs-6">
					<h3><b>지방청별 사용량</b></h3>
					<table class="table table-condensed table-hover table-striped table-bordered" id="capsaicin_table">
						<thead>
							<tr>
								<th>지방청</th>
								<th>사용량(ton)</th>
								<th>사용횟수</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($regions as $r)
							<tr>
								<td><a href="#" id="{{$r->id}}" class="region">{{ $r->node_name }}</a></td>
								<td>{{ round($consumption[$r->id], 2) }}</td>
								<td>{{ $count[$r->id] }}</td>
							</tr>
							@endforeach
						</tbody>
						<tfoot>
							<tr>
								<td><b>합계</b></td>
								<td>{{ round($consumptionSum, 2) }}</td>
								<td>{{ $countSum }}</td>
							</tr>
						</tfoot>
					</table>
				</div>
				<div class="col-xs-6">
					<h3><b id="selected_region"></b></h3>
					<table class="table table-condensed table-hover table-striped table-bordered" id="capsaicin_table">
						<thead>
							<tr>
								<th>월</th>
								<th>사용량(ton)</th>
								<th>사용횟수</th>
							</tr>
						</thead>
						<tbody>
						@for ($i=1; $i <= 12; $i++)
							<tr>
								<td>{{$i}}월</td>
								<td id="{{ 'consumption_'.$i }}"></td>
								<td id="{{ 'count_'.$i }}"></td>
							</tr>
						@endfor
						</tbody>
						<tfoot>
							<tr>
								<td><b>합계</b></td>
								<td id="consumptionSum"></td>
								<td id="countSum"></td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>

</div>
@stop

@section('scripts')
{{ HTML::datepicker() }}계
<script type="text/javascript">
$(function(){
	var year = {{ $year }};
	var initDept = {{ $regions[0]->id }};
	
	var consumptionSum;
	var countSum;

	$(".region").on('click', function(){
		var regionId = this.id;

		consumptionSum = 0;
		countSum = 0;

		params = { regionId: regionId, year: year };
		$.ajax({
			url: url("equips/water_region/get_consumption_by_month"),
			type: "post",
			data: params, 
			success: function(res){
				for (var i = 0; i < 12; i++) {
					$("#consumption_"+(i+1)).text(res[0][i]);
					$("#count_"+(i+1)).text(res[1][i]);

					consumptionSum+=res[0][i];
					countSum+=res[1][i];
					$("#consumptionSum").text(consumptionSum);
					$("#countSum").text(countSum);
					$("#selected_region").text(res[2]+" 월별 사용량");
				};
			}
		});
	})

	$("#"+initDept).trigger('click');
	
})
</script>
@stop