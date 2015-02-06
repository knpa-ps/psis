@extends('layouts.master')

@section('content')
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<strong>장비코드등록</strong>
				</h3>
			</div>

			<div class="panel-body">
				{{ Form::open(array(
						'url'=> 'admin/item_codes',
						'method'=>'post',
						'id'=>'basic_form',
						'class'=>'form-horizontal'
					)) }}
						<fieldset>
							<legend>
								<h4>기본정보</h4>
							</legend>

							<div class="form-group">
								<label for="item_category" class="control-label col-xs-2">분류</label>
								<div class="col-xs-10">
									<select name="item_category" id="item_category" class="form-control">
									@foreach($categories as $c)
										<option value="{{$c->id}}">{{$c->name}}</option>
									@endforeach
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="title" class="control-label col-xs-2">장비명</label>
								<div class="col-xs-10">
									<input type="text" class="form-control input-sm" name="title">
								</div>
							</div>
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
			item_category : {
				required : true
			},
			title: {
				required : true
			}
		}
	});
})
</script>

@stop

@section('styles')
{{ HTML::style('static/vendor/bootstrap-datepicker/css/datepicker3.css') }}
@stop