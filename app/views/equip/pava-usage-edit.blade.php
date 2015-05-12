@extends('layouts.master')

@section('content')
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<strong>PAVA 사용내역 수정</strong>
				</h3>
			</div>

			<div class="panel-body">
				{{ Form::open(array(
						'url'=> 'equips/pava_usage/'.$usage->id.'/update',
						'method'=> 'post',
						'class'=>'form-horizontal',
						'id'=>'asdf'
					)) }}
						<fieldset>
							<legend>
								<h4>행사정보</h4>
							</legend>
									
							<div class="form-group">
								<label for="event_date" class="control-label col-xs-2">행사일자</label>
								<div class="col-xs-10">
									<input type="text" class="form-control input-datepicker input-sm" name="event_date" id="event_date" value="{{$event->date}}">
								</div>
							</div>
							<div class="form-group">
								<label for="event_type" class="control-label col-xs-2">행사유형</label>
								<div class="col-xs-10">
									<select name="event_type" id="event_type" class="form-control input-sm">
										<option value="assembly" {{$event->type_code == 'assembly' ? 'selected' : ''}}>집회</option>
										<option value="training" {{$event->type_code == 'training' ? 'selected' : ''}}>훈련</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="location" class="control-label col-xs-2">사용장소</label>
								<div class="col-xs-10">
									<input type="text" class="form-control input-sm" name="location" id="location" value="{{$event->location}}">
								</div>
							</div>
							<div class="form-group">
								<label for="event_name" class="control-label col-xs-2">행사명</label>
								<div class="col-xs-10">
									<input type="text" class="form-control input-sm" name="event_name" id="event_name" value="{{$event->event_name}}">
								</div>
							</div>
						</fieldset>
						<br>
						<fieldset>
							<legend>
								<h4>부대별 정보</h4>
							</legend>
							<div class="form-group">
								<label for="user_node" class="control-label col-xs-2">사용부대</label>
								<div class="col-xs-10">
									{{ View::make('widget.dept-selector', array('id'=>'user_node_id', 'inputClass'=>'select-node', 'default'=>$usage->node )) }}
								</div>
							</div>
							<div class="form-group">
								<label for="amount" class="control-label col-xs-2">사용량</label>
								<div class="col-xs-10">
									<input type="text" class="form-control input-sm" id="amount" name="amount" value="{{$usage->amount}}">
								</div>
							</div>
						</fieldset>
						<br>
						<button id="submit_btn" class="btn btn-block btn-lg btn-primary">제출</button>
				{{ Form::close(); }}
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
$(function() {
	$("#submit_btn").on('click', function(){
		$("#asdf").submit();
	})

	$("#asdf").validate({
		rules: {
			event_name: {
				required: true,
				maxlength: 255
			},
			event_type: {
				required: true
			},
			location: {
				required: true,
				maxlength: 255
			},
			event_name: {
				required: true,
				maxlength: 255
			},
			amount: {
				required: true,
				number: true
			},
			event_date: {
				required: true,
				dateISO: true
			}
		}
	});
});
</script>
@stop

@section('styles')
{{ HTML::style('static/vendor/bootstrap-datepicker/css/datepicker3.css') }}
@stop