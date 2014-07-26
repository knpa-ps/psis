@extends('layouts.master')

@section('styles')
{{ HTML::style('static/vendor/bootstrap-datepicker/css/datepicker3.css') }}
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
							<h4>기본정보</h4>
						</legend>
						<div class="form-group">
							<label for="item_name" class="control-label col-xs-2">장비명</label>
							<div class="col-xs-10">
								<select name="item" id="item_name" class="form-control">
								@foreach($items as $i)
									<option value="{{$i->id}}">{{$i->name}}</option>
								@endforeach
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="classifier" class="control-label col-xs-2">취득구분</label>
							<div class="col-xs-10">
								<select name="classifier" id="classifier" class="form-control">
									<!-- 장비를 선택하면 해당 장비의 취득시기/제조업체 출력-->

								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="title" class="control-label col-xs-2">보급내역</label>
							<div class="col-xs-10">
								<input type="text" class="form-control input-sm" name="title" id="title" value="{{ $supply->title or '' }}">
							</div>
						</div>
						<div class="form-group">
							<label for="supply_date" class="control-label col-xs-2">보급일자</label>
							<div class="col-xs-10">
								<input type="text" class="form-control input-datepicker input-sm" name="supply_date" id="supply_date" value="{{ $supply->supply_date or ''}}">
							</div>
						</div>

						<button class="btn btn-lg btn-block btn-primary" type="submit" >제출</button>
					</fieldset>
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

	// Load initial selected equip's classifiers.
	$.ajax({
		url : base_url+'/equips/supplies/create/get_classifiers',
		type : 'post',
		data : { 'item_id' : $("#item_name").children(":selected").attr("value") },
		success : function(res){
			if(res.code===1){
				$("#classifier").html(res.body);
			} else {
				alert(res.body);
			}
		}
	});
	// Load selected equip's classifiers.
	$("#item_name").on("change",function(){
		$.ajax({
			url : base_url+'/equips/supplies/create/get_classifiers',
			type : 'post',
			data : { 'item_id' : $(this).children(":selected").attr("value") },
			success : function(res){
				if(res.code===1){
					$("#classifier").html(res.body);
				} else {
					alert(res.body);
				}
			}
		});
	});
	$("#supply_form").validate({
		rules: {
			item_name: {
				required: true,
				maxlength: 255
			},
			classifier: {
				required: true
			},
			description: {
				maxlength: 255
			},
			supply_date: {
				required: true,
				dateISO: true
			}
		}
	});
})
</script>
@stop