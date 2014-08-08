@extends('layouts.master')

@section('styles')
@stop
@section('content')
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					수요조사 등록하기 - {{$item->name.'('.$item->maker_name.')'}}
				</h3>
			</div>
			<div class="panel-body">
				<div class="col-xs-12">
					{{ Form::open(array(
						'url' => $mode === "create" ? 'equips/surveys/new':'equips/surveys/new/'.$item->id,
						'method' => $mode === 'create' ? 'post':'put',
						'class' => 'form-horizontal',
						'id' => 'new_survey_form'
					)) }}
					<legend>
						관서별 총 수량 지정
					</legend>
					<table class="table table-condensed table-bordered table-striped" style="table-layout: fixed;">
						<thead>
							<tr>
								<td style="text-align: center;">총계</td>
								@foreach($childrenNodes as $n)
									<td style="text-align: center;">{{$n->node_name}}</td>
								@endforeach
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="padding-top: 11px; text-align: center;" id="sum"></td>
								@foreach ($childrenNodes as $n)
									<td>
										<input type="text" class="input-sm form-control input-count" name="{{'count_'.$n->id}}" id="{{'count_'.$n->id}}">
									</td>
								@endforeach
							</tr>
						</tbody>
					</table>
					<input type="hidden" name="item_id" value="{{$item->id}}">
				</div>
				<div class="col-xs-12">
					<button class="btn btn-primary pull-right" type="submit">등록</button>
				</div>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts')

<script type="text/javascript">
$(function(){
	$('.input-count').on('change', function(){
		//인풋이 숫자아니면 없애기
		var input = $(this).val();
		if (!jQuery.isNumeric(input)) {
			$(this).val('');
		};

		var sumNode = 0;
		@foreach ($childrenNodes as $n)
			var typeValue = $("#{{'count_'.$n->id}}").val();
			if (jQuery.isNumeric(typeValue)) {
				sumNode += parseInt(typeValue);
			}
			//노드별합계 계산
		@endforeach
		$("#sum").html('<b>'+sumNode+'</b>')
	});
});
</script>
@stop