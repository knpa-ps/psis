@extends('layouts.master')

@section('content')
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<strong>보유장비수정</strong>
				</h3>
			</div>

			<div class="panel-body">
				{{ Form::open(array(
						'url'=> 'equips/inventories/'.$inventory->id,
						'method'=>'put',
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
										<option value="{{$c->id}}" {{ $inventory->item->category_id == $c->id ? 'selected' : '' }} >{{$c->name}}</option>
									@endforeach
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="item" class="control-label col-xs-2">장비명</label>
								<div class="col-xs-10">
									<select name="item" id="item" class="form-control">
										<!-- 분류 선택시 해당 분류의 장비 목록이 option으로 들어감 -->
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="model_name" class="control-label col-xs-2">모델명</label>
								<div class="col-xs-10">
									<input type="text" class="form-control input-sm" name="model_name" id="model_name" value="{{$inventory->model_name}}">
								</div>
							</div>
							<div class="form-group">
								<label for="acquired_date" class="control-label col-xs-2">취득시기</label>
								<div class="col-xs-10">
									<input type="text" class="form-control input-datepicker input-sm " name="acquired_date" value="{{$inventory->acq_date}}">
								</div>
							</div>
							<div class="form-group">
								<label for="acquired_route" class="control-label col-xs-2">취득경로</label>
								<div class="col-xs-10">
									<input type="text" class="form-control input-sm" name="acquired_route" value="{{$inventory->acq_route}}">
								</div>
							</div>
							<div class="form-group">
								<label for="count" class="control-label col-xs-2">보유량</label>
								<div class="col-xs-10">
									<input type="text" class="form-control input-sm" name="count" value="{{$inventory->count}}">
								</div>
							</div>
						</fieldset>
				<button class="btn btn-lg btn-block btn-primary" type="submit">제출</button>
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
$(function(){
	
	$('#item_category').on('change', function(){
		var data = {'id' : this.value};
		var itemId = {{$inventory->item_id}};

		$.ajax({
			url : base_url+"/equips/inventories/create/get_items_in_category",
			method : 'post',
			data : JSON.stringify(data),
			contentType: 'application/json',
			dataType: 'json',
			success : function(res){
				var options;
				if(res.length === 0){
					$("#item").html('<option>해당 분류에 속하는 장비 없음</option>');
				}
				for (var i=0; i< res.length; i++){
					if(itemId === res[i].id){
						var str = '<option selected value="'+res[i].id+'">'+res[i].name+'</option>';	
					} else {
						var str = '<option value="'+res[i].id+'">'+res[i].name+'</option>';	
					}
					options += str;
				}
				$("#item").html(options);
			}
		});
		$('#item').html("<option >")
	});
	$('#item_category').trigger('change');

	$('#basic_form').validate({
		rules : {
			item_category : {
				required : true
			},
			item_name : {
				required : true
			},
			model_name : {
				required : true
			},
			acquired_date : {
				required : true,
				dateISO : true
			},
			count : {
				required : true,
				number : true
			}
		}
	});
})
</script>

@stop

@section('styles')
{{ HTML::style('static/vendor/bootstrap-datepicker/css/datepicker3.css') }}
@stop