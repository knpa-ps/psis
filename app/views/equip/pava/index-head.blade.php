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
			<li><a href="{{url('equips/water_per_month')}}">월별 살수내역</a></li>
			<li><a href="{{url('equips/pava_per_month')}}">월별 PAVA사용내역</a></li>
			<li><a href="{{url('equips/water_pava')}}">집회시 사용내역</a></li>
			<li class="active"><a href="{{url('equips/pava_io')}}">집회 외 PAVA소모내역</a></li>
			@if ( in_array($node->type_code, array("D001")) )
			<li><a href="{{url('equips/pava_confirm')}}">삭제요청</a></li>
			@endif
		</ul>
		
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><strong>지방청별 집회 외 PAVA 소모내역 <span style="color: red; font-size: 12px;" class="blink">사용결과 보고는 일일보고임.</span></strong></h3>
			</div>
			<div class="panel-body">
				<div class="well well-sm">
					<form class="form-horizontal" id="data_table_form">
						<h5>조회조건</h5>
						<div class="row">
							<div class="col-xs-6 form-group">
								<label for="start" class="col-xs-3 control-label">
									사용일자
								</label>
								<div class="col-xs-9">
									<div class="input-daterange input-group">
									    <input type="text" class="input-sm form-control" name="start" value="{{ $start }}">
									    <span class="input-group-addon">~</span>
									    <input type="text" class="input-sm form-control" name="end" value="{{ $end }}" >
									</div>
								</div>
							</div>
							<div class="col-xs-6 form-group">
								<label for="event_name" class="control-label col-xs-3">
									제목
								</label>
								<div class="col-xs-9">
									<input type="text" class="input-sm form-control" id="event_name" name="event_name" value="{{ $eventName ? $eventName : ''}}">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-6 form-group">
								<label for="sort" class="control-label col-xs-3">
									유형
								</label>
								<div class="col-xs-9">
									<select name="sort" id="sort" class="input-sm form-control">
										<option value="" {{$sort == '' ? 'selected' : '' }}>전체</option>
										<option value="training" {{$sort == 'training' ? 'selected' : '' }}>훈련</option>
										<option value="training" {{$sort == 'lost' ? 'selected' : '' }}>소실</option>
									</select>
								</div>
							</div>
							<div class="col-xs-6 form-group">
								<label for="region" class="control-label col-xs-3">
									지방청별 보기
								</label>
								<div class="col-xs-9">
									<select name="region" id="region" class="input-sm form-control">
										<option value="">전체</option>
										@foreach ($regions as $r)
											<option value="{{ $r->id }}" {{ $region == $r->id ? 'selected' : '' }} >{{$r->node_name}}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-xs-12">
								<div class="pull-right">
									<button class="btn btn-primary btn-xs" type="submit"><span class="glyphicon glyphicon-ok"></span> 조회</button>
								</div>
								<div class="clearfix"></div>
							</div>
						</div>
					</form>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<table class="table table-condensed table-hover table-striped table-bordered" id="capsaicin_table">
						<thead>
							<th>일자</th>
							<th>지방청</th>
							<th>행사명</th>
							<th>유형</th>
							<th>소모량(ℓ)</th>
							<th></th>
						</thead>
						<tbody>
							@if(sizeof($events)==0)
							<tr>
								<td colspan="10">내역이 없습니다.</td>
							</tr>
							@else
								@foreach ($events as $e)
								<tr id="{{$e->id}}">
									<td>{{ $e->date }}</td>
									<td>{{ $e->node->node_name }}</td>
									<td>{{ $e->event_name }}</td>
									<td>{{ $e->type() }}</td>
									<td>{{ round($e->amount, 2) }}</td>
									<td>
										{{-- <a href="{{url('equips/water_pava/'.$e->id.'/edit')}}" class="label label-success"><span class="glyphicon glyphicon-pencil"></span> 수정</a><br /> --}}
										<a href="#" class="delete-usage label label-danger"><span class="glyphicon glyphicon-remove"></span> 삭제</a>
									</td>
								</tr>
								@endforeach
							@endif
						</tbody>
						<tfoot>
							<tr>
								<td colspan="4">조회내역 합계</td>
								<td>{{ round($events->sum('amount'), 2) }}</td>
								<td></td>
							</tr>
						</tfoot>
						{{ $events->appends(array('start'=>$start, 'end'=>$end, 'event_name'=>$eventName))->links() }}
						</table>
					</div>
				</div>
			</div>
		</div>

		<script type="text/javascript">
		$(function(){
			$(".delete-usage").on('click', function(){
				if (!confirm('정말 삭제하시겠습니까?')) {
					return;
				};
				var usageId = $(this).parent().parent().attr('id');
				$.ajax({
					url : base_url+'/equips/pava_io/'+usageId,
					type : 'delete',
					success : function(res) {
						alert(res);
						location.reload();
					}
				});
			});
		})
		</script>
	</div>
</div>
@stop

@section('scripts')
{{ HTML::datepicker() }}
@stop