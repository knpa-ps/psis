@extends('layouts.master')
@section('styles')
<style>
	th, td {
	  text-align: center;
	  vertical-align: middle!important;
	}
</style>
{{-- 월별 살수내역(지방청) --}}
@stop
@section('content')
<div class="row">
	<div class="col-xs-12" style="overflow: auto;">
		<ul class="nav nav-tabs">
			<li class="active"><a href="{{url('equips/water_per_month')}}">월별 살수내역</a></li>
			<li><a href="{{url('equips/pava_per_month')}}">월별 PAVA사용내역</a></li>
			<li><a href="{{url('equips/water_pava')}}">집회시 사용내역</a></li>
			<li><a href="{{url('equips/pava_io')}}">집회 외 PAVA소모내역</a></li>
			@if ( in_array($node->type_code, array("D001")) )
			<li><a href="{{url('equips/pava_confirm')}}">삭제요청</a></li>
			@endif
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
									<th>총 살수량(ton)</th>
									<th>살수차 사용횟수</th>
								</tr>
							</thead>
							<tbody>
							@for ($i=1; $i <= 12; $i++)
								<tr>
									<td>{{$i}}월</td>
									<td id="{{ 'amount_'.$i }}"></td>
									<td id="{{ 'count_'.$i }}"></td>
								</tr>
							@endfor
							</tbody>
							<tfoot>
								<tr bgcolor="#fee9fc">
									<td><b>합계</b></td>
									<td id="amount_sum"></td>
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

	var amountSum=0;
	var countSum=0;

	var regionId = {{ $user->supplySet->node->id }};

	params = { regionId: regionId, year: year };
	$.ajax({
		url: url("equips/get_water_consumption_by_month"),
		type: "post",
		data: params,
		dataType: 'json',//내부망에선 이걸 추가해줘야 돌아감
		success: function(res){
			for (var i = 0; i < 12; i++) {
				$("#amount_"+(i+1)).text(res[0][i]);
				$("#count_"+(i+1)).text(res[1][i]);

				amountSum+=res[0][i];
				countSum+=res[1][i];

				$("#amount_sum").text(amountSum);
				$("#count_sum").text(countSum);
				$("#panel-title").html(res[2]+' '+year+" 월별 살수내역 " + "<span style='color: red; font-size: 12px;' class='blink'>사용결과 보고는 일일보고임.</span>");
			};
		}
	});

})
</script>
@stop
