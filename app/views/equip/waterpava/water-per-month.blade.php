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
			<li class="active"><a href="{{url('equips/water_per_month')}}">월별 살수내역</a></li>
			<li><a href="{{url('equips/pava_per_month')}}">월별 PAVA사용내역</a></li>
			<li><a href="{{url('equips/water_pava')}}">집회시 사용내역</a></li>
			<li><a href="{{url('equips/pava_io')}}">집회 외 PAVA소모내역</a></li>
		</ul>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><strong id="panel-title"></strong></h3>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-xs-6">
						<form>
							<div class="form-group">
								<label for="year" class="control-label">조회연도</label>
								<select name="year" id="year_select">
								@for ($i=$initYear; $i <= $nowYear; $i++)
									<option value="{{$i}}" {{ $i == $selectedYear ? 'selected' : ''}}>{{$i}}</option>
								@endfor
								</select>
								<button type="submit" class="btn btn-xs btn-primary">조회</button>
							</div>
						</form>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<table class="table table-condensed table-hover table-striped table-bordered" id="capsaicin_table">
							<thead>
								<tr>
									<th>월</th>
									<th>경고살수량(ton)</th>
									<th>곡사살수량(ton)</th>
									<th>직사살수량(ton)</th>
									<th>총 살수량(ton)</th>
									<th>살수차 사용횟수</th>
								</tr>
							</thead>
							<tbody>
							@for ($i=1; $i <= 12; $i++)
								<tr>
									<td>{{$i}}월</td>
									<td id="{{ 'warn_'.$i }}"></td>
									<td id="{{ 'direct_'.$i }}"></td>
									<td id="{{ 'high_angle_'.$i }}"></td>
									<td id="{{ 'sum_'.$i }}"></td>
									<td id="{{ 'count_'.$i }}"></td>
								</tr>
							@endfor
							</tbody>
							<tfoot>
								<tr bgcolor="#fee9fc">
									<td><b>합계</b></td>
									<td id="warn_sum"></td>
									<td id="direct_sum"></td>
									<td id="high_angle_sum"></td>
									<td id="sum_sum"></td>
									<td id="count_sum"></td>
								</tr>
							</tfoot>
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
	var year = {{ $selectedYear }};
	
	var warnSum=0;
	var directSum=0;
	var highAngleSum=0;
	var sumSum=0;
	var countSum=0;

	var regionId = {{ $user->supplyNode->id }};

	params = { regionId: regionId, year: year };
	$.ajax({
		url: url("equips/get_water_consumption_by_month"),
		type: "post",
		data: params, 
		success: function(res){
			for (var i = 0; i < 12; i++) {
				$("#warn_"+(i+1)).text(res[0][i]);
				$("#direct_"+(i+1)).text(res[1][i]);
				$("#high_angle_"+(i+1)).text(res[2][i]);
				$("#sum_"+(i+1)).text(res[3][i]);
				$("#count_"+(i+1)).text(res[4][i]);

				warnSum+=res[0][i];
				directSum+=res[1][i];
				highAngleSum+=res[2][i];
				sumSum+=res[3][i];
				countSum+=res[4][i];

				$("#warn_sum").text(warnSum);
				$("#direct_sum").text(directSum);
				$("#high_angle_sum").text(highAngleSum);
				$("#sum_sum").text(sumSum);
				$("#count_sum").text(countSum);
				$("#panel-title").html(res[5]+' '+year+" 월별 살수내역 " + "<span style='color: red; font-size: 12px;' class='blink'>사용결과 보고는 일일보고임.</span>");
			};
		}
	});

})
</script>
@stop