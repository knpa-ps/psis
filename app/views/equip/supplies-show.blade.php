@extends('layouts.master')

@section('styles')
<style>
	td, th {
		 text-align: center;
	}
</style>
@stop

@section('content')
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<strong>보급내역</strong>
				</h3>
			</div>
			<div class="panel-body">
				<h4>[{{$supply->node->node_name}} 보급] {{$item->code->title}} ({{$item->maker_name.','.$item->classification }})</h4>
				<span class="pull-right" >보급일자 {{ $supply->supplied_date }}</span>
				<table class="table table-condensed table-bordered table-striped" style="table-layout: fixed;" >
					<thead>
						<tr>
							<th>구분</th>
							<th>총계</th>
							@foreach($types as $t)
								<th>{{$t->type_name}}</th>
							@endforeach
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>계</td>
							<td id="sum_all"></td>
							@foreach ($types as $t)
							<td id="{{'sum_type_'.$t->id}}"></td>
							@endforeach
						</tr>
						@foreach ($lowerNodes as $node)
						<tr>
							@if($node->parent->type_code == 'D003')
								<td>{{$node->parent->node_name.' '.$node->node_name}}</td>
							@else
								<td>{{$node->node_name}}</td>
							@endif
							<td id="{{'sum_node_'.$node->id}}">0</td>
							@foreach($types as $t)
								<td id="count_{{$node->id}}_{{$t->id}}">{{$count[$node->id][$t->id]}}</td>
							@endforeach
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts')
<script>
$(function() {
	calcSum();	
	
	//입력된 숫자에 따라 각 합계를 표시해줌
	function calcSum(){
		var sumAll = 0;
		@foreach ($lowerNodes as $node)
			// 지급노드별 합계 넣기
			var sumNode = 0;
			@foreach ($types as $type)
				var typeValue = $("#{{'count_'.$node->id.'_'.$type->id}}").text();
				if (jQuery.isNumeric(typeValue)) {
					sumNode += parseFloat(typeValue);
				}
				//노드별합계 계산
			@endforeach
			$("#{{'sum_node_'.$node->id}}").html('<b>'+sumNode+'</b>');
			//노드별 합계를 넣었다.
			//총 합계 계산
			sumAll += parseFloat(sumNode);
		@endforeach
		$("#sum_all").html('<b>'+sumAll+'</b>');
		//총 합계를 넣었다.
		@foreach($types as $type)
			var sumType = 0;
			@foreach($lowerNodes as $node)
				var nodeValue = $("#{{'count_'.$node->id.'_'.$type->id}}").text();
				if(jQuery.isNumeric(nodeValue)){
					sumType += parseFloat(nodeValue);
				}
			@endforeach
			$("#{{'sum_type_'.$type->id}}").html('<b>'+sumType+'</b>');
		@endforeach
	}
});
</script>
@stop