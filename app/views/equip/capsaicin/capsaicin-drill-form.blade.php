@extends('layouts.master')

@section('content')
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<strong>{{$node->node_name}} 훈련시 캡사이신 사용내역 추가</strong>
				</h3>
			</div>

			<div class="panel-body">
				{{ Form::open(array(
						'url'=> 'equips/capsaicin/',
						'method' => 'post',
						'id'=>'basic_form',
						'class'=>'form-horizontal'
					)) }}
						<fieldset>
							<legend>
								<h4>캡사이신 사용정보</h4>
							</legend>
							{{-- use DatePicker plugin --}}
							<div class="form-group">
								<label for="date" class="control-label col-xs-2">훈련날짜</label>
								<div class="col-xs-10">
									<input type="text" class="form-control input-datepicker input-sm" name="date" id="date">
								</div>
							</div>

							<div class="form-group">
								<label for="event" class="control-label col-xs-2">훈련명</label>
								<div class="col-xs-10">
									<input type="text" name="event" id="event" class="form-control input-sm">
									</input>
								</div>
							</div>
							<div class="form-group">
								<label for="location" class="control-label col-xs-2">장소</label>
								<div class="col-xs-10">
									<input name="location" id="location" class="form-control input-sm" type="text">
								</div>
							</div>

							<div class="form-group">
								<label for="amount" class="control-label col-xs-2">사용량(ℓ)</label>
								<div class="col-xs-10">
									<input type="text" class="form-control input-sm" name="amount" id="amount">
								</div>
							</div>
						</fieldset>
						<input type="text" class="hidden" id="file_name" name="type" value="drill">
						<input type="text" class="hidden" name="nodeId" value="{{$node->id}}">
				{{ Form::close(); }}
				<input type="submit" id="submit_btn" class="btn btn-lg btn-block btn-primary" value="제출">
			</div>
		</div>
	</div>
</div>
@stop
@section('scripts')
{{ HTML::script('static/vendor/jquery.form.js') }}
{{ HTML::script('static/vendor/validate/jquery.validate.min.js') }}
{{ HTML::script('static/vendor/validate/messages_ko.js') }}
{{ HTML::script('static/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js') }}
{{ HTML::script('static/vendor/bootstrap-datepicker/js/locales/bootstrap-datepicker.kr.js') }}
{{ HTML::script('static/vendor/bootstrap-datepicker/js/defaults.js') }}

<script type="text/javascript">

	$("#submit_btn").on('click', function(){
		$("#basic_form").submit();
	})

	$("#basic_form").validate({
		rules: {
			date: {
				required: true,
				dateISO : true
			},
			event: {
				required: true,
				maxlength: 255
			},
			location: {
				required: true,
				maxlength: 255
			},
			amount: {
				required: true,
				number: true,
				min: 0
			}
		},

		submitHandler: function(form) {
		    form.submit();
		}
	})

</script>
@stop

@section('styles')
{{ HTML::style('static/vendor/bootstrap-datepicker/css/datepicker3.css') }}
@stop
