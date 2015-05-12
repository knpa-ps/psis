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
			<li><a href="{{url('equips/water_region')}}">월별 보기</a></li>
			<li class="active"><a href="{{url('equips/water_affair')}}">행사별 보기</a></li>
		</ul>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><strong>행사별 물 사용내역</strong></h3>
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
									    <input type="text" class="input-sm form-control" name="start" 
									    value="{{ $start }}">
									    <span class="input-group-addon">~</span>
									    <input type="text" class="input-sm form-control" name="end"
									    value="{{ $end }}" >
									</div>
								</div>
							</div>
							<div class="col-xs-6 form-group">
								<label for="event_name" class="control-label col-xs-3">
									행사명
								</label>
								<div class="col-xs-9">
									<input type="text" class="input-sm form-control" id="event_name" name="event_name" value="{{ $eventName or '' }}">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-6 form-group">
								<label for="region" class="control-label col-xs-3">
									지방청별 보기
								</label>
								<div class="col-xs-9">
									<select name="region" id="region" class="input-sm form-control">
										<option value="">전체</option>
										@foreach ($regions as $r)
											<option value="{{ $r->id }}" {{ $region == $r->id ? 'selected' : '' }} >{{$r->node_name }}</option>
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
						<input type="hidden" name="tab_id" value="3">
					</form>
				</div>
				<div class="toolbar-table">
					@if($isRegion)
					<a href="{{url('equips/water_affair/create?nodeId='.$nodeId)}}" class="btn btn-info btn-xs pull-right">
						<span class="glyphicon glyphicon-plus"></span> 사용내역 추가
					</a>
					@endif
					<a href="{{URL::current().'?export=true&start='.$start.'&end='.$end.'&event_name='.$eventName.'&region='.$region}}" class="btn btn-success btn-xs pull-right">
						<span class="glyphicon glyphicon-download"></span> 다운로드(.xlsx)
					</a>
					<div class="clearfix"></div>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<table class="table table-condensed table-hover table-striped table-bordered" id="water_table">
						<thead>
								<tr>
									<th>일자</th>
									<th>지방청</th>
									<th>사용장소</th>
									<th>행사명</th>
									<th style="background-color: #E89ECC">사용량(ton)</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								@if(sizeof($rows)==0)
								<tr>
									<td colspan="6">내역이 없습니다.</td>
								</tr>
								@else
								@foreach ($rows as $r)
								<tr id="{{$r->id}}">
									<td>{{ $r->date }}</td>
									<td>{{ $r->node->node_name }}</td>
									<td>{{ $r->location }}</td>
									@if($r->attached_file_name != '')
									<td><a href="{{ url('uploads/docs/'.$r->attached_file_name) }}">{{ $r->event_name }}</a></td>
									@else
									<td>{{ $r->event_name }}</td>
									@endif
									<td style="background-color: #FEE9FC">{{ round($r->amount,2) }}</td>
									<td><a href="#" class="delete-usage label label-danger"><span class="glyphicon glyphicon-remove"></span> 삭제</a></td>
								</tr>
								@endforeach
								@endif
							</tbody>
							<tfoot>
								<tr>
									<td colspan="4">조회내역 총 사용량</td>
									<td>{{ round($totalUsage, 2) }}</td>
									<td></td>
								</tr>
							</tfoot>
							{{ $rows->links() }}
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
	$(".delete-usage").on('click', function(){
		if (!confirm('정말 삭제하시겠습니까?')) {
			return;
		};
		var usageId = $(this).parent().parent().attr('id');
		$.ajax({
			url : base_url+'/equips/water_affair/'+usageId,
			type : 'delete',
			success : function(res) {
				alert(res);
				location.reload();
			}
		});
	});
})
</script>
@stop