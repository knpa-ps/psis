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
			@if ( in_array($node->type_code, array("D001","D002")) )
			<li><a href="{{url('equips/capsaicin/node/'.$node->id.'/confirm')}}">삭제요청</a></li>
			<li><a href="{{url('equips/capsaicin/node/'.$node->id.'/holding')}}">월별보기</a></li>
			@endif
			<li class="active"><a href="{{url('equips/capsaicin/node/'.$node->id.'/events')}}">행사별 보기</a></li>
		</ul>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><strong>{{ $node->node_name }} 캡사이신 희석액 사용내역</strong></h3>
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
									<input type="text" class="input-sm form-control" id="event_name" name="event_name" value="{{ $eventName ? $eventName : ''}}">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-6 form-group">
								<label for="event_type" class="control-label col-xs-3">
									행사구분
								</label>
								<div class="col-xs-9">
									<select name="event_type" id="event_type" class="input-sm form-control">
										<option value="" {{$eventType == '' ? 'selected' : '' }}>전체</option>
										<option value="assembly" {{$eventType == 'assembly' ? 'selected' : '' }}>집회</option>
										<option value="drill" {{$eventType == 'drill' ? 'selected' : '' }}>훈련</option>
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

				<div class="toolbar-table">
					@if ($userNode->type_code == "D002")
					<a href="#" id="add_event" class="btn btn-warning btn-xs pull-right">
						<span class="glyphicon glyphicon-plus"></span> 행사명 등록
					</a>
					@endif
					<a href="{{url('equips/capsaicin/create?nodeId='.$node->id.'&type=event')}}" class="btn btn-info btn-xs pull-right">
						<span class="glyphicon glyphicon-plus"></span> 집회내역 추가
					</a>
					<a href="{{url('equips/capsaicin/create?nodeId='.$node->id.'&type=drill')}}" class="btn btn-primary btn-xs pull-right">
						<span class="glyphicon glyphicon-plus"></span> 훈련내역 추가
					</a>
					<a href="{{URL::current().'?export=true&event_type='.$eventType.'&start='.$start.'&end='.$end.'&event_name='.$eventName}}" class="btn btn-success btn-xs pull-right">
						<span class="glyphicon glyphicon-download"></span> 다운로드(.xlsx)
					</a>
					<div class="clearfix"></div>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<table class="table table-condensed table-hover table-striped table-bordered" id="capsaicin_table">
						<thead>
							<tr>
								<th style="width:10%; white-space: nowrap; text-align: center">일자</th>
								<th style="width:5%; white-space: nowrap; text-align: center">관할청</th>
								<th>중대</th>
								<th>사용장소</th>
								<th style="white-space: nowrap; text-align: center;">행사명</th>
								<th style="width:5%; white-space: nowrap; text-align: center;background-color: #E89ECC">사용량(ℓ)</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@if(sizeof($rows)==0)
							<tr>
								<td colspan="8">내역이 없습니다.</td>
							</tr>
							@else
							@foreach ($rows as $r)
							<tr id="{{$r->id}}">
								<td style="white-space: nowrap">{{ $r->date }}</td>
								<td style="white-space: nowrap">{{ $r->node->region()->node_name }}</td>
								<td>{{ $r->user_node->full_name }}</td>
								<td>{{ $r->location }}</td>
								<td style="white-space: nowrap">{{ $r->event_name }}</td>
								<td style="background-color: #FEE9FC">{{ round($r->amount, 2) }}</td>
								<td>
									@if($r->fileName != '')
									<a href="{{ url('uploads/docs/'.$r->fileName) }}" class="label label-primary"><span class="glyphicon glyphicon-download"></span> 사용보고서</a>
									@endif
									<a href="#" class="delete-usage label label-danger"><span class="glyphicon glyphicon-remove"></span> 삭제</a>
								</td>
							</tr>
							@endforeach

							@endif
						</tbody>
						<tfoot>
							<tr>
								<td colspan="5">{{ $start }} ~ {{$end}} 총 사용량</td>
								<td>{{ round($totalUsage, 2) }}</td>
								<td></td>
							</tr>
						</tfoot>
						{{ $rows->appends(array('is_state'=>'false', 'start'=>$start, 'end'=>$end, 'event_name'=>$eventName, 'event_type' => $eventType) )->links() }}
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
	$("#add_event").on('click', function(){
		popup(base_url+'/equips/capsaicin/node/'+{{ $node->id }}+'/add_event', 700, 400);
	});

	$(".delete-usage").on('click', function(){
		if (!confirm('정말 삭제하시겠습니까?')) {
			return;
		};
		var usageId = $(this).parent().parent().attr('id');
		$.ajax({
			url : base_url+'/equips/capsaicin_usage/'+usageId,
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
