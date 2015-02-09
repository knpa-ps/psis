@extends('layouts.master')

@section('styles')
@stop
@section('content')
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					설문조사 응답하기 - {{$item->code->title.'('.$item->maker_name.' / '.$item->classification.')'}}
				</h3>
			</div>
			<div class="panel-body">
				<div class="col-xs-12">
					{{ Form::open(array(
						'url' => 'equips/surveys/'.$survey->id.'/response',
						'method' => $mode === 'create' ? 'post':'put', 
						'class' => 'form-horizontal',
						'id' => 'survey_form'
					)) }}
					<legend>
						사이즈 별 수량 입력
					</legend>
					<table class="table table-condensed table-bordered table-striped" style="table-layout: fixed;">
						<thead>
							<tr>
								<td style="text-align: center;">총계</td>
								@foreach($types as $t)
									<td style="text-align: center;">{{$t->type_name}}</td>
								@endforeach
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="padding-top: 11px; text-align: center;" id="sum">{{ $sum }}</td>
								@foreach ($types as $t)
									<td>
										<input type="text" class="input-sm form-control input-count" name="{{'count_'.$t->id}}" id="{{'count_'.$t->id}}" value="{{$count[$t->id] or ''}}">
									</td>
								@endforeach
							</tr>
						</tbody>
					</table>
					<input type="hidden" name="item_id" value="{{$item->id}}">
				</div>
				<div class="col-xs-12">
					<button class="btn btn-primary pull-right" type="submit">제출</button>
				</div>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts')

<script type="text/javascript">
$(function(){

	var sum = 0;

	$('.input-count').on('change', function(){
		//인풋이 숫자아니면 없애기
		var input = $(this).val();
		var re = /^\d+$/;

		if (!re.test(input)) {
			alert('양의 정수만 입력하세요');
			$(this).val('');
		};

		@foreach ($types as $t)
			var count = $("#{{'count_'.$t->id}}").val();
			if (count=='') {
				count = 0;
			};
			sum += parseInt(count);
		@endforeach

		if (sum > {{$sum}}) {
			alert('수량이 초과되었습니다');
			$(this).val('');
			return;
		};

		$('#sum').text("{{$sum}} / "+ sum);
	});
	
	$('#survey_form').submit(function(event){
		
		if (sum != {{$sum}} ) {
			alert("총 수량이 일치하지 않습니다.");
			event.preventDefault();
		}
	});
});
</script>
@stop