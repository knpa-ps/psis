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
						'url' => $mode === "create" ? 'equips/surveys':'equips/surveys/'.$survey->id,
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
								<td style="text-align: center;">관서명</td>
								<td style="text-align: center;">수량</td>
							</tr>
						</thead>
						<tbody>
							@foreach($childrenNodes as $n)
								<tr>
									<td style="padding-top: 11px; text-align: center;">{{$n->node_name}}</td>
									<td>
										<input type="text" class="input-sm form-control input-count" name="{{'count_'.$n->id}}" id="{{'count_'.$n->id}}" value="{{$count[$n->id] or ''}}">
									</td>
								</tr>
							@endforeach
							<tr>
								<td style="text-align: center;">총계</td>
								<td style="text-align: center;" id="sum"></td>
							</tr>
						</tbody>
					</table>
					<input type="hidden" name="item_id" value="{{$item->id}}">
				</div>
				<div class="col-xs-12">
					<button class="btn btn-primary btn-lg btn-block" type="submit">제출</button>
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
		var re = /^\d+$/;
		if (!re.test(input)) {
			alert('양의 정수만 입력하세요');
			$(this).val('');
		};

		var sumNode = 0;
		@foreach ($childrenNodes as $n)
			var typeValue = $("#{{'count_'.$n->id}}").val();
			if (re.test(typeValue)) {
				sumNode += parseInt(typeValue);
			}
			//노드별합계 계산
		@endforeach
		$("#sum").html('<b>'+sumNode+'</b>')
	});
});
</script>
@stop