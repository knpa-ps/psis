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
			<li class="active"><a href="{{url('equips/capsaicin/node/'.$node->id.'/confirm')}}">삭제요청</a></li>
			@endif
			<li><a href="{{url('equips/capsaicin/node/'.$node->id.'/holding')}}">월별보기</a></li>
			<li><a href="{{url('equips/capsaicin/node/'.$node->id.'/events')}}">집회별 보기</a></li>
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
								<th>관할청</th>
								<th>중대</th>
								<th>사용장소</th>
								<th>행사명</th>
								<th style="background-color: #E89ECC">사용량(ℓ)</th>
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
								<td>{{ $r->date }}</td>
								<td>{{ $r->node->node_name }}</td>
								<td>{{ $r->user_node->full_name }}</td>
								<td>{{ $r->location }}</td>
								<td>{{ $r->event_name }}</td>
								<td style="background-color: #FEE9FC">{{ round($r->amount, 2) }}</td>
								<td>
									@if($r->fileName != '')
									<a href="{{ url('uploads/docs/'.$r->fileName) }}" class="label label-primary"><span class="glyphicon glyphicon-download"></span> 사용보고서</a>
									@endif
									<a href="#" class="confirm-delete label label-danger"><span class="glyphicon glyphicon-remove"></span> 삭제</a>
								</td>
							</tr>
							@endforeach
							
							@endif
						</tbody>
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
	$(".confirm-delete").on('click', function(){
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