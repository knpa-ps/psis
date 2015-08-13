@extends('layouts.base')
@section('content')
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<strong>{{$item->code->title}} 분실/폐기 내역</strong>		
				</h3>
			</div>
		</div>
		<div class="panel-body">
			<table class="table table-striped table-bordered" id="lists_table">
				<thead>
					<tr>
						<th rowspan="2">일자</th>
						<th rowspan="2">유형</th>
						<th colspan="{{sizeof($types)}}">사이즈별 수량</th>
						<th rowspan="2">사유서</th>
					</tr>
					<tr>
						@foreach ($types as $type)
							<td>{{$type->type_name}}</td>
						@endforeach	
					</tr>
				</thead>
				<tbody>
					@foreach ($sets as $set)
						<tr>
							<td>{{$set->discarded_date}}</td>
							@if($set->category === 'lost')
								<td>{{'분실'}}</td>
							@elseif($set->category === 'wrecked')
								<td>{{'파손'}}</td>
							@elseif($set->category === 'expired')
								<td>{{'내구연한초과'}}</td>
							@endif
							
							@foreach ($types as $type)
								<td>{{$count[$set->id][$type->id]}}</td>
							@endforeach
							
							<td>
							<a class="btn btn-sucess btn-xs btn-block" href="{{ url('/uploads/docs/'.$set->file_name)}}" target="_blank">사유서 보기</a>

							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
		
	</div>
</div>
