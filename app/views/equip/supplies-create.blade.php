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
						<legend>
							<h4>부서별 수량</h4>
						</legend>
						<div class="form-group">
						<!-- 부서 -->
                            <label for="target_dept" class="control-label col-xs-2">대상부서</label>
                            <div class="col-xs-10">
                                {{ View::make('widget.dept-selector', array('id'=>'target_dept')) }}
                            </div>			
						</div>
						<div class="form-group">
							<label for="count" class="control-label col-xs-2">수량</label>
							<div class="col-xs-10">
								<input type="text" id="count" class="form-control input-sm">
							</div>
						</div>
						<div class="col-xs-12">
							<button class="btn btn-xs btn-info pull-right">
								<span class="glyphicon glyphicon-plus"> 보급부서추가</span>
							</button>
							<div class="clearfix"></div>
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
	$("#supply_form").validate({
		rules: {
			item_name: {
				required: true,
				maxlength: 255
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
</script>
@stop