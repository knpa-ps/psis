@extends('layouts.master')

@section('content')
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<strong>{{$node->node_name}} 집회 외 PAVA소모내역 추가</strong>
				</h3>
			</div>

			<div class="panel-body">
				{{ Form::open(array(
						'url'=> $mode=='create'?'equips/pava_io':'equips/pava_io/'.$pava->id,
						'method'=>$mode=='create'?'post':'put',
						'id'=>'basic_form',
						'class'=>'form-horizontal'
					)) }}
						<fieldset>
							<legend>
								<h4>기본정보</h4>
							</legend>
									
							<div class="form-group">
								<label for="event_name" class="control-label col-xs-2">행사명</label>
								<div class="col-xs-10">
									<input type="text" class="form-control input-sm" name="event_name" id="event_name"
									value="">
								</div>
							</div>
							
							<div class="form-group">
								<label for="date" class="control-label col-xs-2">날짜</label>
								<div class="col-xs-10">
									<input type="text" class="form-control input-datepicker input-sm" name="date" id="date"
									value="">
								</div>
							</div>

							<div class="form-group">
								<label for="sort" class="control-label col-xs-2">행사구분</label>
								<div class="col-xs-10">
									<select name="sort" id="sort" class="form-control input-sm">
										<option value="training">훈련</option>
										<option value="lost">소실</option>
									</select>
								</div>
							</div>

							<div class="form-group">
								<label for="amount" class="control-label col-xs-2">소모량</label>
								<div class="col-xs-10">
									<input type="text" class="form-control input-sm" name="amount" id="amount"
									value="">
								</div>
							</div>

						</fieldset>
						<input type="hidden" name="node_id" value="{{$node->id}}">

				{{ Form::close(); }}
				
				<input type="button" id="submit_btn" class="btn btn-lg btn-block btn-primary" value="제출">
			</div>
		</div>
	</div>
</div>
@stop
@section('scripts')
{{ HTML::script('static/vendor/jquery.form.js') }}
{{ HTML::script('static/vendor/validate/jquery.validate.min.js') }}
{{ HTML::script('static/vendor/validate/additional-methods.min.js') }}
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
			event_name: {
				required: true,
				maxlength: 255
			},
			date: {
				required: true,
				dateISO: true
			},
			sort: {
				required: true
			},
			amount: {
				required: true,
				number: true
			}
		},
		submitHandler: function(form) {
		    form.submit();
		}
	});

</script>
@stop

@section('styles')

{{ HTML::style('static/vendor/bootstrap-datepicker/css/datepicker3.css') }}

@stop