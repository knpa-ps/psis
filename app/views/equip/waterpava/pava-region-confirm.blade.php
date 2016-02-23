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
{{-- 삭제요청(본청) --}}
<div class="row">
	<div class="col-xs-12" style="overflow: auto;">
		<ul class="nav nav-tabs">
			<li><a href="{{url('equips/water_per_month')}}">월별 살수내역</a></li>
			<li><a href="{{url('equips/pava_per_month')}}">월별 PAVA사용내역</a></li>
			<li><a href="{{url('equips/water_pava')}}">집회시 사용내역</a></li>
			<li><a href="{{url('equips/pava_io')}}">집회 외 PAVA소모내역</a></li>
			@if ( in_array($node->type_code, array("D001")) )
			<li class="active"><a href="{{url('equips/pava_confirm')}}">삭제요청</a></li>
			@endif
		</ul>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><strong>캡사이신 사용보고 삭제요청 목록</strong></h3>
			</div>
			<div class="panel-body">

				<div class="row">
					<div class="col-xs-12">
						<table class="table table-condensed table-hover table-striped table-bordered" id="capsaicin_table">
						<thead>
							<tr>
								<th>일자</th>
								<th>지방청</th>
								<th>행사명</th>
								<th>사용장소</th>
								<th style="background-color: #C6FFFA">살수량(ton)</th>
								<th style="background-color: #63E3DE">PAVA혼합량(ℓ)</th>
								<th style="background-color: #E2FFA8">염료혼합량(ℓ)</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@if(sizeof($requests)==0)
							<tr>
								<td colspan="8">내역이 없습니다.</td>
							</tr>
							@else
								@foreach ($requests as $r)
								<tr id="{{$r->id}}">
									<td>{{ $r->event->date }}</td>
									<td>{{ $r->event->node->node_name }}</td>
									<td>{{ $r->event->event_name }}</td>
									<td>{{ $r->event->location }}</td>
									<td>{{ round($r->event->amount, 2) }}</td>
									<td>{{ round($r->event->pava_amount, 2) }}</td>
									<td>{{ round($r->event->dye_amount, 2) }}</td>
									<td>
										@if($r->event->attached_file_name != '')
										<a href="{{ url('uploads/docs/'.$r->event->attached_file_name) }}" class="label label-primary"><span class="glyphicon glyphicon-download"></span> 사용보고서</a>
										@endif
										<a href="#" class="delete-usage label label-danger"><span class="glyphicon glyphicon-remove"></span> 삭제</a>
									</td>
								</tr>
								@endforeach
							@endif
						</tbody>
						{{ $requests->links() }}
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
	$(".delete-usage").on('click', function(){
		if (!confirm('정말 삭제하시겠습니까?')) {
			return;
		};
		var usageId = $(this).parent().parent().attr('id');
		$.ajax({
			url : base_url+'/equips/confirm_delete/'+usageId,
			type : 'delete',
			success : function(res) {
				alert(res);
				location.reload();
			}
		});
	});
</script>
@stop
