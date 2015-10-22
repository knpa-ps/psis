@extends('layouts.master')

@section('styles')
@stop
@section('content')
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					관리전환 수량확인 - {{substr($item->acquired_date, 0, 4).' '.$item->code->title.' / '.$item->maker_name.''}}
				</h3>
			</div>
			<div class="panel-body">
				<div class="col-xs-12">
					<legend>
						<h5><b>관리전환사유</b> : {{$convSet->explanation}}</h5>
					</legend>
					<table class="table table-condensed table-bordered table-striped" style="table-layout: fixed;">
						<thead>
							<tr>
								<th style="text-align: center;">총계</th>
								@foreach($types as $t)
									<th style="text-align: center;">{{$t->type_name}}</th>
								@endforeach
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="text-align: center;" id="sum">{{ $sum }}</td>
								@foreach ($types as $t)
									<td style="text-align: center;">{{ $convData[$t->id] }}</td>
								@endforeach
							</tr>
						</tbody>
					</table>
					<input type="hidden" name="item_id" value="{{$item->id}}">
				</div>

				<div class="col-xs-12">
					@if ($isImport)
						@if ($convSet->is_confirmed==0)
							{{ Form::open(array(
								'url'=> 'equips/convert/'.$convSet->id.'/confirm',
								'method'=>'post',
								'id'=>'confirm_form'
							)) }}

							<input type="text" class="hidden" name="item_id" value="{{ $item->id }}">

							<button class="btn btn-primary pull-right btn-xs" type="button" id="confirm"><span class="glyphicon glyphicon-ok-sign"></span> 확정하기</button>

							{{ Form::close(); }}
						@else
							<button disabled class="btn btn-success pull-right btn-xs" type="button" ><span class="glyphicon glyphicon-ok-sign"></span> 확정됨</button>
						@endif
					@endif

					<!-- 지방청간 전환인 경우에만 본청 승인하도록 함 -->
					@if ($convSet->cross_head == 1)
						@if ($user->supplyNode->type_code === "D001")
							@if ($convSet->head_confirmed==0)
								<a class="btn btn-primary btn-xs pull-right" href="{{url('equips/convert_cross_head/'.$convSet->id.'/confirm')}}"><span class="glyphicon glyphicon-ok-sign"></span> 본청 승인하기</a>
							@else
								<button disabled class="btn btn-success pull-right btn-xs" type="button" ><span class="glyphicon glyphicon-ok-sign"></span> 본청 승인됨</button>
							@endif
						@endif
					@endif
				</div>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts')

<script type="text/javascript">
$(function(){
	$('#confirm').on('click', function(){
		if(confirm('관리전환 입고 수량이 정확히 확인되었습니까?')) {
			$('#confirm_form').submit();
		}
	})
})
</script>
@stop
