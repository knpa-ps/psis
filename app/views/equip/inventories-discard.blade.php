@extends('layouts.master')

@section('content')
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<strong>{{$item->code->title.'('.$item->classification.')'}} 분실/폐기 등록</strong>
				</h3>
			</div>

			<div class="panel-body">
				{{ Form::open(array(
						'url'=> 'equips/items/'.$item->id.'/discard',
						'method'=>'post',
						'id'=>'discard_form',
						'class'=>'form-horizontal'
					)) }}
						<fieldset>
							<legend><h4>처분장비정보</h4></legend>
							<div class="form-group">
								<label for="discard_date" class="control-label col-xs-2">일자</label>
								<div class="col-xs-10">
									<input type="text" class="form-control input-datepicker input-sm " name="discard_date">
								</div>
							</div>
							<div class="form-group">
								<label for="category" class="control-label col-xs-2">유형</label>
								<div class="col-xs-10">
									<select name="category" id="category" class="form-control">
										<option value="lost">분실</option>
										<option value="wrecked">파손</option>
										<option value="expired">불용연한초과</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="cause" class="control-label col-xs-2">사유</label>
								<div class="col-xs-10">
									<input type="text" class="form-control input-sm" name="cause">
								</div>
							</div>
						</fieldset>

						<fieldset id="fieldset">
							<legend><h4>사이즈별 수량</h4></legend>
							<table class="table table-condensed table-bordered table-striped" id="count_table">
								<thead>
									<tr>
									@foreach ($item->types as $type)
										<th style='text-align: center;'>{{ $type->type_name }}</th>
									@endforeach
									</tr>
								</thead>
								<tbody id="tbody">
									<tr>
									<!-- 수량을 입력하고 수량과 함께 type_id를 hidden form을 통해 보내기 위한 폼 -->
									@foreach ($item->types as $type)
										<td>									
											<input class="count" type="number" style="width:100%;" name="{{ 'type_counts['.$type->id.']' }}" placeholder="보유량 : {{ $holding[$type->id] }}">
										</td>
									@endforeach
									</tr>
								</tbody>
							</table>
						</fieldset>

						<button class="btn btn-lg btn-block btn-primary" type="submit" id="submit">제출</button>

				{{ Form::close(); }}
				
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts')
{{ HTML::script('static/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js') }}
{{ HTML::script('static/vendor/bootstrap-datepicker/js/locales/bootstrap-datepicker.kr.js') }}
{{ HTML::script('static/vendor/bootstrap-datepicker/js/defaults.js') }}

<script type="text/javascript">
$(function(){
	$("#discard_form").submit(function(){
		$(".count").each(function(){
			var count = $(this).attr('value');
			if (count == '') {
				$(this).val(0);
			};
		});
	});
});
</script>

@stop

@section('styles')
{{ HTML::style('static/vendor/bootstrap-datepicker/css/datepicker3.css') }}
@stop