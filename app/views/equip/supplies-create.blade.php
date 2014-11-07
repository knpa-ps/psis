@extends('layouts.master')

@section('styles')
{{ HTML::style('static/vendor/bootstrap-datepicker/css/datepicker3.css') }}
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
					<strong>{{ $mode === "create" ? "보급내역추가" : "보급내역수정" }}</strong>
				</h3>
			</div>
			<div class="panel-body">
				{{ Form::open(array(
					'url' => $mode === "create" ? 'equips/supplies':'equips/supplies/'.$supply->id,
					'method' => $mode === 'create' ? 'post':'put',
					'class' => 'form-horizontal',
					'id' => 'supply_form'
				)) }}
					<fieldset>
						<legend>
							<h4>[{{$userNode->node_name}}] {{$item->code->title}} ({{$item->classification}}) 보급하기</h4>
						</legend>
						<input type="text" style="display: none;" name="item_id" value="{{$item->id}}">
						<div class="form-group">
							<label for="supply_date" class="control-label col-xs-1">보급일자</label>
							<div class="col-xs-2">
								<input type="text" class="form-control input-datepicker input-sm" name="supply_date" id="supply_date" value="{{ $supply->supplied_date or ''}}">
							</div>
						</div>
						<table class="table table-condensed table-bordered" style="table-layout: fixed;" >
							<thead>
								<tr style="background-color: #F5F5F5;">
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
									<td id="sum_all">0</td>
									@foreach ($types as $t)
									<td id="{{'sum_type_'.$t->id}}">0</td>
									@endforeach
								</tr>
								@foreach ($lowerNodes as $node)
								<tr>
									<td>{{$node->node_name}}</td>
									<td id="{{'sum_node_'.$node->id}}">0</td>
									@foreach($types as $t)
									<td>
										<input class="input-count input-sm form-control" style="width:100%;" type="text" id="count_{{$node->id}}_{{$t->id}}" name="count_{{$node->id}}_{{$t->id}}" value="{{$mode === 'create' ? '' : $count[$node->id][$t->id] }}">
									</td>
									@endforeach
								</tr>
								@endforeach
							</tbody>
						</table>
					</fieldset>
					<button class="btn btn-lg btn-block btn-primary" type="submit">제출</button>
				{{ Form::close() }}
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts')
{{ HTML::script('static/vendor/validate/jquery.validate.min.js') }}
{{ HTML::script('static/vendor/validate/additional-methods.min.js') }}
{{ HTML::script('static/vendor/validate/messages_ko.js') }}

{{ HTML::script('static/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js') }}
{{ HTML::script('static/vendor/bootstrap-datepicker/js/locales/bootstrap-datepicker.kr.js') }}
{{ HTML::script('static/vendor/bootstrap-datepicker/js/defaults.js') }}

<script type="text/javascript">
$(function(){
	calcSum();	
	
	//입력된 숫자에 따라 각 합계를 표시해줌
	function calcSum(){
		var sumAll = 0;
		@foreach ($lowerNodes as $node)
			// 지급노드별 합계 넣기
			var sumNode = 0;
			@foreach ($types as $type)
				var typeValue = $("#{{'count_'.$node->id.'_'.$type->id}}").val();
				if (jQuery.isNumeric(typeValue)) {
					sumNode += parseInt(typeValue);
				}
				//노드별합계 계산
			@endforeach
			$("#{{'sum_node_'.$node->id}}").text(sumNode);
			//노드별 합계를 넣었다.
			//총 합계 계산
			sumAll += parseInt(sumNode);
		@endforeach
		$("#sum_all").text(sumAll);
		//총 합계를 넣었다.
		@foreach($types as $type)
			var sumType = 0;
			@foreach($lowerNodes as $node)
				var nodeValue = $("#{{'count_'.$node->id.'_'.$type->id}}").val();
				if(jQuery.isNumeric(nodeValue)){
					sumType += parseInt(nodeValue);
				}
			@endforeach
			$("#{{'sum_type_'.$type->id}}").text(sumType);
		@endforeach
	}


	$('.input-count').on('change', function(){
		calcSum();
		var input = $(this).val();
		if (!jQuery.isNumeric(input)) {
			$(this).val('');
		};
	});

	$("#supply_form").validate({
		rules: {
			supply_date: {
				required: true,
				dateISO: true
			}
		}
	});
})
</script>
@stop