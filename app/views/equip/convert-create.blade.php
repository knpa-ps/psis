@extends('layouts.master')

@section('content')
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<strong>관리전환하기 [{{$item->code->title}}]</strong>
				</h3>
			</div>

			<div class="panel-body">
				{{ Form::open(array(
						'url'=> 'equips/convert',
						'method'=>'post',
						'id'=>'basic_form',
						'class'=>'form-horizontal'
					)) }}
						<fieldset>
							<legend>
								<h4>기본정보</h4>
							</legend>
							<input type="text" class="hidden" name="item_id" value="{{ $item->id }}">
							<div class="form-group">
								<label for="converted_date" class="control-label col-xs-2">관리전환일자</label>
								<div class="col-xs-10">
									<input type="text" class="form-control input-datepicker input-sm " name="converted_date">
								</div>
							</div>
							
							<div class="form-group">
								<label for="supply_node_id" class="col-xs-2 control-label">대상관서</label>
								<div class="col-xs-4">
									{{ View::make('widget.dept-selector', array('id'=>'supply_node_id', 'inputClass'=>'select-node')) }}
								</div>
							</div>

							<div class="form-group">
								<label for="explanation" class="control-label col-xs-2">관리전환사유</label>
								<div class="col-xs-10">
									<input type="text" class="form-control input-sm" name="explanation">
								</div>
							</div>

						</fieldset>
						<fieldset id="fieldset">
							<legend><h4>사이즈별 수량</h4></legend>
							<table class="table table-condensed table-bordered table-striped" id="count_table">
							<thead>
								<tr id="ths">
								@foreach ($item->types as $type)
									<th style='text-align: center;'>{{ $type->type_name }}</th>
								@endforeach
								</tr>
							</thead>
							<tbody>
								<tr id="tds">
								<!-- 수량을 입력하고 수량과 함께 type_id를 hidden form을 통해 보내기 위한 폼 -->
								@foreach ($item->types as $type)
									<td>									
										<input type="number" style="width:100%;" name="{{ 'type_counts['.$type->id.']' }}" placeholder="보유량 : {{ $holding[$type->id] }}">
									</td>
								@endforeach
								</tr>
							</tbody>
						</table>
						</fieldset>
						<button class="btn btn-lg btn-block btn-primary" type="submit">제출</button>

				{{ Form::close(); }}
				
				<div style="margin-bottom: 50px;"></div>
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
	$('#basic_form').validate({
		rules : {
			converted_date : {
				required : true,
				dateISO : true
			},
		}
	});
})
</script>

@stop

@section('styles')
{{ HTML::style('static/vendor/bootstrap-datepicker/css/datepicker3.css') }}
@stop