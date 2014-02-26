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
							<input type="text" class="input-small" name="q_bm_start" id="q_bm_start" value="{{$startMonth}}"> ~ 
							<input type="text" class="input-small" id="q_bm_end" name="q_bm_end" value="{{$endMonth}}">
						</div>
					</div>
					
				</div>

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
									<option value="{{$r['id']}}" {{ $regionId==$r['id']?'selected':'' }} >{{$r['dept_name']}}</option>
								@endforeach
							</select>
							@else
							<span class="uneditable input-large">{{ $region->dept_name }}</span>
							<input type="hidden" name="q_region" value="{{ $region->id }}">
							@endif
						</div>
					</div>
					
					
				</div>
			<div class="row-fluid">
				<div class="span12">
					<div class="control-group pull-right">
						<div class="controls">
							<input type="submit" class="btn btn-primary" value="조회">
							<input type="button" class="btn btn-info" value="다운로드" id="export">
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
			<h2><i class="icon-th-list"></i> 경비동원수당</h2>
			<div class="box-icon">
				
			</div>
		</div>
		<div class="box-content">
			
			<h2>
				@if ($startMonth == $endMonth)
					{{$startMonth}} 경비동원수당 등 집행현황 ({{$regionName}})
				@else
					{{$startMonth}} ~ {{$endMonth}} 경비동원수당 등 집행현황 ({{$regionName}})
				@endif
			</h2>
			<div class="row-fluid">
				<div class="span12">
					<div class="pull-right">
						<button class="btn hide" id="edit_mobpay_cancel">
							<i class="icon-remove"></i> 취소
						</button>
						<button class="btn btn-danger hide" id="edit_mobpay_submit">
							<i class="icon-ok"></i> 완료
						</button>
						<button class="btn btn-info" id="edit_mobpay">
							<i class="icon-edit"></i> 수정
						</button>
						<button class="btn btn-primary" id="create_mobpay" data-toggle="modal" href="#create_mobpay_modal">
							<i class="icon-plus"></i> 입력
						</button>
					</div>
				</div>
			</div>
			<br><br>
			<form id="edit_form">
				<input type="hidden" name="bm" id="edit_bm">
				<input type="hidden" name="region" id="edit_region">
				<div class="row-fluid">
					<div class="span12">
						
						<table id="mobpay_sit" class="table table-bordered table-striped table-condensed table-hover">
							<caption>
								상황별 동원인원(휴일, 비번, 휴무일 동원)
							</caption>
							<thead>
								<tr>
									<th>구분</th>
									<th>합계</th>
									<th>집회시위관리</th>
									<th>경호행사</th>
									<th>혼잡경비</th>
									<th>수색재난구조</th>
									<th>훈련 등 기타</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<th>
										인원 (명)
									</th>
									<td id="sit_sum">{{ $data['master']->sit_sum }}</td>
									<td id="sit_demo" class="editable">{{ $data['master']->sit_demo }}</td>
									<td id="sit_escort" class="editable">{{ $data['master']->sit_escort }}</td>
									<td id="sit_crowd" class="editable">{{ $data['master']->sit_crowd }}</td>
									<td id="sit_rescue" class="editable">{{ $data['master']->sit_rescue }}</td>
									<td id="sit_etc" class="editable">{{ $data['master']->sit_etc }}</td>
								</tr>
							</tbody>
						</table>

						<table id="mobpay_ftn" class="table table-bordered table-striped table-condensed table-hover">		
							<caption>기능별 동원인원</caption>
							<thead>
								<tr>
									<th colspan="7">경비동원수당 지급 대상(일반대상자)</th>
								</tr>
								<tr>
									<th>구분</th>
									<th>합계</th>
									<th>지방청</th>
									<th>경찰서</th>
									<th>지구대</th>
									<th>경찰관기동대</th>
									<th>전의경부대</th>
								</tr>
							</thead>
							<tbody>

							@if (count($data['detail']) > 0)
								<?php $sum=array(0,0,0,0,0,0); ?>
								@foreach ($data['detail'] as $d)

								<tr>
									<td>{{$intervals[$d->interval_id]->start}}~{{$intervals[$d->interval_id]->end}}시간</td>
									<td id="ftn_sum_{{$d->interval_id}}" class="editable">{{ $d->sum }}</td>
									<td id="ftn_region_{{$d->interval_id}}" class="editable">{{ $d->region }}</td>
									<td id="ftn_office_{{$d->interval_id}}" class="editable">{{ $d->office }}</td>
									<td id="ftn_local_{{$d->interval_id}}" class="editable">{{ $d->local }}</td>
									<td id="ftn_officer_troop_{{$d->interval_id}}" class="editable">{{ $d->officer_troop }}</td>
									<td id="ftn_troop_{{$d->interval_id}}" class="editable">{{ $d->troop }}</td>
									<?php 
										$sum[0]+=$d->sum;
										$sum[1]+=$d->region;
										$sum[2]+=$d->office;
										$sum[3]+=$d->local;
										$sum[4]+=$d->officer_troop;
										$sum[5]+=$d->troop;
									 ?>
								</tr>
								@endforeach
								<tr>
									<th>인원(명)</th>
									<td>{{ $sum[0] }}</td>
									<td>{{ $sum[1] }}</td>
									<td>{{ $sum[2] }}</td>
									<td>{{ $sum[3] }}</td>
									<td>{{ $sum[4] }}</td>
									<td>{{ $sum[5] }}</td>

								</tr>
								<tr>
									<th>지급액(천원)</th>
									<td>{{ $data['master']->ftn_sum }}</td>
									<td>{{ $data['master']->ftn_region }}</td>
									<td>{{ $data['master']->ftn_office }}</td>
									<td>{{ $data['master']->ftn_local }}</td>
									<td>{{ $data['master']->ftn_officer_troop }}</td>
									<td>{{ $data['master']->ftn_troop }}</td>
								</tr>
							@else
								<tr>
									<td colspan="7">
										입력된 자료가 없습니다.
									</td>
								</tr>
							@endif
							</tbody>
						</table>

						<table id="mobpay_extra" class="table table-bordered table-striped table-condensed table-hover">		
							<caption>시간외수당 지급 대상(현업대상자)</caption>
							<thead>
								<tr>
									<th>구분</th>
									<th>합계</th>
									<th>지방청</th>
									<th>경찰서</th>
									<th>지구대</th>
									<th>경찰관기동대</th>
									<th>전의경부대</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<th>인원(명)</th>
									<td>{{ $data['master']->extra_sum }}</td>
									<td id="extra_region" class="editable">{{ $data['master']->extra_region }}</td>
									<td id="extra_office" class="editable">{{ $data['master']->extra_office }}</td>
									<td id="extra_local" class="editable">{{ $data['master']->extra_local }}</td>
									<td id="extra_officer_troop" class="editable">{{ $data['master']->extra_officer_troop }}</td>
									<td id="extra_troop" class="editable">{{ $data['master']->extra_troop }}</td>
								</tr>
							</tbody>
						</table>
					</div>

				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal hide fade" id="create_mobpay_modal">
	<div class="modal-header">
		<a class="close" data-dismiss="modal">&times;</a>
		<h3>경비동원수당 입력</h3>
	</div>
	<div class="modal-body">
		<form id="create_form" method="POST" class="form form-horizontal">
			
			<div class="row-fluid">
				<div class="span6">
					<div class="control-group">
						<label for="bm" class="control-label">
							귀속월
						</label>
						<div class="controls">
							<input type="text" class="input-small" name="bm" id="bm">
						</div>
					</div>
				</div>
							
				<div class="span6">
					<div class="control-group">
						<label for="q_department" class="control-label">
							지방청
						</label>
						<div class="controls">
							@if (is_array($region))
							<select name="q_region">
								@foreach ($region as $r)
									<option value="{{$r['id']}}" {{ $regionId==$r['id']?'selected':'' }} >{{$r['dept_name']}}</option>
								@endforeach
							</select>
							@else
							<span class="uneditable input-large">{{ $region->dept_name }}</span>
							<input type="hidden" name="q_region" value="{{ $region->id }}">
							@endif
						</div>
					</div>		
				</div>
			</div>

			<table id="mobpay_sit" class="table table-bordered table-striped table-condensed table-hover">
				<caption>
					상황별 동원인원(휴일, 비번, 휴무일 동원)
				</caption>
				<thead>
					<tr>
						<th>구분</th>
						<th>합계</th>
						<th>집회시위관리</th>
						<th>경호행사</th>
						<th>혼잡경비</th>
						<th>수색재난구조</th>
						<th>훈련 등 기타</th>
					</tr>
				</thead>
				<tbody>
					<tr class="input-rowsum">
						<th>
							인원 (명)
						</th>
						<td><span class="input-sum">0</span></td>
						<td><input type="text" class="input-number input-mini" name="sit_demo"></td>
						<td><input type="text" class="input-number input-mini" name="sit_escort"></td>
						<td><input type="text" class="input-number input-mini" name="sit_crowd"></td>
						<td><input type="text" class="input-number input-mini" name="sit_rescue"></td>
						<td><input type="text" class="input-number input-mini" name="sit_etc"></td>
					</tr>
				</tbody>
			</table>

			<table id="mobpay_ftn" class="table table-bordered table-striped table-condensed table-hover">		
				<caption>기능별 동원인원</caption>
				<thead>
					<tr>
						<th colspan="7">경비동원수당 지급 대상(일반대상자)</th>
					</tr>
					<tr>
						<th>구분</th>
						<th>합계</th>
						<th>지방청</th>
						<th>경찰서</th>
						<th>지구대</th>
						<th>경찰관기동대</th>
						<th>전의경부대</th>
					</tr>
				</thead>
				<tbody>
				@foreach($intervals as $k=>$i)
					<tr class="input-rowsum">
						<th>{{$i->start}}~{{$i->end}}시간</th>
						<td><span class="input-sum">0</span></td>
						<td><input type="text" class="input-number input-mini col1" data-col="1" name="ftn_region_{{$k}}"></td>
						<td><input type="text" class="input-number input-mini col2" data-col="2" name="ftn_office_{{$k}}"></td>
						<td><input type="text" class="input-number input-mini col3" data-col="3" name="ftn_local_{{$k}}"></td>
						<td><input type="text" class="input-number input-mini col4" data-col="4" name="ftn_officer_troop_{{$k}}"></td>
						<td><input type="text" class="input-number input-mini col5" data-col="5" name="ftn_troop_{{$k}}"></td>
					</tr>
				@endforeach
				<tr id="ftn_sum_row">
					<th>합계</th>
					<td><span id="sum0">0</span></td>
					<td><span id="sum1" class="subtotal">0</span></td>
					<td><span id="sum2" class="subtotal">0</span></td>
					<td><span id="sum3" class="subtotal">0</span></td>
					<td><span id="sum4" class="subtotal">0</span></td>
					<td><span id="sum5" class="subtotal">0</span></td>
				</tr>
				</tbody>
			</table>

			<table id="mobpay_extra" class="table table-bordered table-striped table-condensed table-hover">		
				<caption>시간외수당 지급 대상(현업대상자)</caption>
				<thead>
					<tr>
						<th>구분</th>
						<th>합계</th>
						<th>지방청</th>
						<th>경찰서</th>
						<th>지구대</th>
						<th>경찰관기동대</th>
						<th>전의경부대</th>
					</tr>
				</thead>
				<tbody>
					<tr class="input-rowsum">
						<th>인원(명)</th>
						<td><span class="input-sum">0</span></td>
						<td><input type="text" class="input-number input-mini" name="extra_region"></td>
						<td><input type="text" class="input-number input-mini" name="extra_office"></td>
						<td><input type="text" class="input-number input-mini" name="extra_local"></td>
						<td><input type="text" class="input-number input-mini" name="extra_officer_troop"></td>
						<td><input type="text" class="input-number input-mini" name="extra_troop"></td>
					</tr>
				</tbody>
			</table>
	</div>
	<div class="modal-footer">
		<button type="submit" class="btn btn-primary">제출</button>
		<button href="#" class="btn" data-dismiss="modal">취소</button>
	</div>
	</form>	
</div>

@stop

@section('scripts')
{{ HTML::script('static/js/jquery.inputmask.bundle.min.js') }}
<script type="text/javascript">
$(function(){
	$("#q_form").submit(function(){
		var start = $("#q_bm_start").val();
		var end = $("#q_bm_end").val();
		if (new Date(start+"-01").getTime() > new Date(end+"-01").getTime()) {
			bootbox.alert("시작 월은 종료 월보다 같거나 더 이전이어야 합니다.");
			return false;				
		}

		return true;
	});
	$("#q_bm_end, #q_bm_start, #bm").inputmask("y-m");
	$(".input-number").inputmask("9999");
	$(".input-number").change(function(){
		var sum = 0;
		var row = $(this).closest(".input-rowsum");
		row.find(".input-number").each(function(){
			sum += $(this).val()?parseInt($(this).val()):0;
		});
		row.find(".input-sum").text(sum);
	});

	$("#mobpay_ftn .input-number").change(function(){
		var col = $(this).data('col');
		var sum = 0;
		$("#mobpay_ftn .col"+col).each(function(){
			sum+= $(this).val()?parseInt($(this).val()):0;	
		});
		$("#ftn_sum_row #sum"+col).text(sum);
		var total = 0;
		$("#ftn_sum_row .subtotal").each(function(){
			total+=$(this).text()?parseInt($(this).text()):0;
		});
		$("#ftn_sum_row #sum0").text(total);
	});

	$("#create_form").submit(function(){
		var bm = $("#bm").val();

		if (!bm.match(/[0-9]{4}\-[0-9]{2}/)) {
			bootbox.alert('귀속월을 정확히 입력해주세요');
			return false;
		}

		bootbox.confirm("입력하시겠습니까?", function(res){
			if (!res) return;
			var params = $("#create_form").serializeArray();

			$.ajax({
				url: "{{ action('BgMobPayController@insert') }}",
				type: 'post',
				data: params,
				success: function(res) {
					if (res == 0) {
						noty({type:'success', layout:'topRight', text:'입력되었습니다'});
						
						var bm = $("#bm").val();
						var r = $("#create_form select[name='q_region']").val();

						window.location = "{{ action('BgMobPayController@show') }}?q_bm_start="+bm+"&q_bm_end="+bm+"&q_region="+r;

						return;
					} else {
						noty({type:'error', layout:'topRight', text:'이미 해당 귀속월의 자료가 존재합니다.'});
					}
				}
			});
		});
		return false;
	});

	$("#edit_mobpay").click(function(){

		if ($("#q_bm_start").val() != $("#q_bm_end").val()) 
		{
			bootbox.alert("수정을 위해서는 조회기간을 특정 한 달로 설정해야 합니다.")
			return;
		}

		@if (is_array($region))
		var region = $("select[name='q_region']").val();
		if (!region)
		{
			bootbox.alert("수정을 위해서는 특정 지방청을 선택해야 합니다.");
			return;
		}
		@endif

		var bm = $("#q_bm_start").val();
		$("#edit_bm").val(bm);
		$("#edit_region").val(region);

		$(this).hide();
		$("#edit_mobpay_cancel").show();
		$("#edit_mobpay_submit").show();

		$(".editable").each(function() {
			var id = $(this).prop('id');
			var data = $(this).text();
			$(this).html('<input type="text" class="input-mini input-edit" name="'+id+'" value="'+data+'" data-value="'+data+'">');
		});
	});

	$("#edit_mobpay_cancel").click(function() {
		$(this).hide();
		$("#edit_mobpay_submit").hide();
		$("#edit_mobpay").show();
		$(".editable input").each(function() {
			var data = $(this).data('value');
			$(this).parent().html(data);
		});
	});

	$("#edit_mobpay_submit").click(function(){
		bootbox.confirm("수정하시겠습니까?", function(res){
			if (!res) return;
			var params = $("#edit_form").serializeArray();
			$.ajax({
				url: "{{ action('BgMobPayController@edit') }}",
				type: "post",
				data: params,
				success: function(res) {
					if (res == 0) {
						noty({type:'success',layout:'topRight',text:"수정되었습니다."});

						$("#edit_mobpay_cancel").hide();
						$("#edit_mobpay_submit").hide();
						$("#edit_mobpay").show();
						$(".editable input").each(function(){
							var data = $(this).val();
							$(this).parent().html(parseInt(data)?parseInt(data):0);
						});
						return;
					}
				}
			});
		});
	});

	$("#export").click(function(){
		var params = $("#q_form").serialize();
		window.open("{{ action('BgMobPayController@export') }}?"+params);
	});
});
</script>
@stop

@section('styles')
<style type="text/css">
	#create_mobpay_modal {
		width: 1000px;
		margin-left: -500px;
	}
</style>
@stop