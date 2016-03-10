@extends('layouts.base')
@section('content')
<style>
.table thead>tr>th.discard-align {
	vertical-align: middle;
	text-align: center;
}
.table tbody>tr>td.discard-align {
	vertical-align: middle;
	text-align: center;
}
</style>
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<strong>{{$item->code->title}} @lang('equip.discard') 내역</strong>
				</h3>
			</div>
			<div class="panel-body">
				<table class="table table-striped table-bordered table-hover" id="lists_table">
					<thead>
						@if(sizeof($types)==1)
						<th class="discard-align" rowspan="2">구분</th>
						<th class="discard-align" rowspan="2">일자</th>
						<th class="discard-align" rowspan="2">유형</th>
						<th class="discard-align" rowspan="2" colspan="{{sizeof($types)}}">수량</th>
						<th class="discard-align" rowspan="2">@lang('equip.written_reason')</th>
						<th class="discard-align" rowspan="2">삭제</th>
						@else
							<tr>
								<th class="discard-align" rowspan="2">구분</th>
								<th class="discard-align" rowspan="2">일자</th>
								<th class="discard-align" rowspan="2">유형</th>
								<th class="discard-align" rowspan="2">총 수량</th>
								<th class="discard-align" colspan="{{sizeof($types)}}">사이즈별 수량</th>
								<th class="discard-align" rowspan="2">@lang('equip.written_reason')</th>
								<th class="discard-align" rowspan="2">삭제</th>
							</tr>
						<tr>
							@foreach ($types as $type)
								<td>{{$type->type_name}}</td>
							@endforeach
						</tr>
						@endif
					</thead>
					<tbody>
						@foreach ($sets as $set)
							<tr>
								<td class="discard-align" >{{$set->node->full_name}}</td>
								<td class="discard-align" >{{$set->discarded_date}}</td>
								@if($set->category === 'repaired')
									<td class="discard-align" >{{'수리'}}</td>
								@elseif($set->category === 'lost')
									<td class="discard-align" >{{'분실'}}</td>
								@elseif($set->category === 'wrecked')
									<td class="discard-align" >{{'파손'}}</td>
								@elseif($set->category === 'expired')
									<td class="discard-align" >{{'내구연한초과'}}</td>
								@endif
								@if(sizeof($types)!=1)
									<td class="discard-align" >{{$sum[$set->id]}}</td>
								@endif
								@foreach ($types as $type)
									<td class="discard-align" >{{$count[$set->id][$type->id]}}</td>
								@endforeach
								<td class="discard-align" >
									@if($set->file_name)
									<a class="btn btn-success btn-xs btn-block" href="{{ url('/uploads/docs/'.$set->file_name)}}" target="_blank"><span class="glyphicon glyphicon-ok"></span> @lang('equip.written_reason') 보기</a>
									@endif
								</td>
								<td class="discard-align" >
								<a class="btn btn-xs btn-danger btn-block" href="{{url('/equips/items/'.$set->id.'/cancel_discarded_item')}}"><span class="glyphicon glyphicon-ok"></span> 삭제</button>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
